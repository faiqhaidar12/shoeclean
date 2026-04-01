<?php

namespace App\Http\Controllers\Api;

use App\Models\Survey;
use App\Models\SurveyAnswer;
use App\Models\SurveyQuestion;
use App\Models\SurveyResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class SurveyManagementController
{
    public function publicShow(Survey $survey): JsonResponse
    {
        abort_unless($survey->is_active, 404, 'Survey tidak tersedia.');

        $survey->load(['outlet:id,name,slug,address,phone', 'questions']);

        return response()->json([
            'survey' => [
                'id' => $survey->id,
                'title' => $survey->title,
                'slug' => $survey->slug,
                'description' => $survey->description,
                'type' => $survey->type,
                'is_active' => (bool) $survey->is_active,
                'outlet' => $survey->outlet ? [
                    'id' => $survey->outlet->id,
                    'name' => $survey->outlet->name,
                    'slug' => $survey->outlet->slug,
                    'address' => $survey->outlet->address,
                    'phone' => $survey->outlet->phone,
                ] : null,
                'questions' => $survey->questions->map(fn (SurveyQuestion $question) => [
                    'id' => $question->id,
                    'question' => $question->question,
                    'type' => $question->type,
                    'options' => $question->options ?? [],
                    'sort_order' => $question->sort_order,
                ])->values(),
            ],
        ]);
    }

    public function publicStoreResponse(Request $request, Survey $survey): JsonResponse
    {
        abort_unless($survey->is_active, 404, 'Survey tidak tersedia.');
        $survey->load('questions');

        $validated = $request->validate([
            'respondent_name' => ['required', 'string', 'max:255'],
            'respondent_phone' => ['nullable', 'string', 'max:255'],
            'respondent_type' => ['required', 'in:owner,customer'],
            'answers' => ['required', 'array', 'min:1'],
        ]);

        if ($survey->type === 'outlet') {
            $validated['respondent_type'] = 'customer';
        }

        foreach ($survey->questions as $question) {
            $answer = $validated['answers'][$question->id] ?? null;

            if ($question->type === 'rating' && (!is_numeric($answer) || (int) $answer < 1 || (int) $answer > 5)) {
                return response()->json([
                    'message' => 'Rating wajib diisi.',
                    'errors' => [
                        "answers.{$question->id}" => ['Rating wajib diisi.'],
                    ],
                ], 422);
            }

            if ($question->type === 'choice' && blank($answer)) {
                return response()->json([
                    'message' => 'Pilihan wajib diisi.',
                    'errors' => [
                        "answers.{$question->id}" => ['Pilihan wajib diisi.'],
                    ],
                ], 422);
            }
        }

        $response = DB::transaction(function () use ($survey, $validated) {
            $response = SurveyResponse::create([
                'survey_id' => $survey->id,
                'respondent_name' => $validated['respondent_name'],
                'respondent_phone' => $validated['respondent_phone'] ?: null,
                'respondent_type' => $validated['respondent_type'],
                'outlet_id' => $survey->outlet_id,
            ]);

            foreach ($survey->questions as $question) {
                $answer = $validated['answers'][$question->id] ?? null;

                SurveyAnswer::create([
                    'survey_response_id' => $response->id,
                    'survey_question_id' => $question->id,
                    'answer' => $question->type !== 'rating' ? (is_string($answer) ? trim($answer) : $answer) : null,
                    'rating' => $question->type === 'rating' ? (int) $answer : null,
                ]);
            }

            return $response;
        });

        return response()->json([
            'message' => 'Terima kasih, jawaban Anda sudah tersimpan.',
            'response' => [
                'id' => $response->id,
            ],
        ], 201);
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user->isOwner(), 403);

        $outletIds = $user->ownedOutlets->pluck('id')->all();

        $query = Survey::query()
            ->where('type', 'outlet')
            ->whereIn('outlet_id', $outletIds)
            ->withCount('responses')
            ->with('outlet:id,name,slug')
            ->latest();

        if ($search = trim((string) $request->string('search'))) {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('title', 'like', '%' . $search . '%')
                    ->orWhere('slug', 'like', '%' . $search . '%');
            });
        }

        if ($status = trim((string) $request->string('status'))) {
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $surveys = $query
            ->paginate(12)
            ->through(fn (Survey $survey) => $this->transformSurvey($survey));

        return response()->json([
            'filters' => [
                'search' => (string) $request->string('search'),
                'status' => $status ?? '',
            ],
            'summary' => [
                'total_surveys' => $surveys->total(),
                'active_surveys' => Survey::query()
                    ->where('type', 'outlet')
                    ->whereIn('outlet_id', $outletIds)
                    ->where('is_active', true)
                    ->count(),
                'total_responses' => Survey::query()
                    ->where('type', 'outlet')
                    ->whereIn('outlet_id', $outletIds)
                    ->withCount('responses')
                    ->get()
                    ->sum('responses_count'),
            ],
            'statuses' => ['active', 'inactive'],
            'outlets' => $user->ownedOutlets->map(fn ($outlet) => [
                'id' => $outlet->id,
                'name' => $outlet->name,
                'slug' => $outlet->slug,
            ])->values(),
            'surveys' => $surveys,
        ]);
    }

    public function show(Request $request, Survey $survey): JsonResponse
    {
        $this->authorizeSurvey($request, $survey);

        $survey->load(['outlet:id,name,slug', 'questions', 'responses.answers.question']);
        $responses = $survey->responses()->with('answers.question')->latest()->get();

        return response()->json([
            'survey' => $this->transformSurvey($survey, true),
            'question_stats' => $survey->questions->map(fn (SurveyQuestion $question) => $this->questionStats($question, $responses))->values(),
            'recent_responses' => $responses->take(8)->map(function ($response) {
                return [
                    'id' => $response->id,
                    'respondent_name' => $response->respondent_name,
                    'respondent_phone' => $response->respondent_phone,
                    'respondent_type' => $response->respondent_type,
                    'created_at' => optional($response->created_at)->toIso8601String(),
                    'answers' => $response->answers->map(function ($answer) {
                        return [
                            'question' => $answer->question?->question,
                            'type' => $answer->question?->type,
                            'answer' => $answer->answer,
                            'rating' => $answer->rating,
                        ];
                    })->values(),
                ];
            })->values(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user->isOwner(), 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'outlet_id' => ['required', 'integer'],
            'questions' => ['required', 'array', 'min:1'],
            'questions.*.question' => ['required', 'string', 'max:500'],
            'questions.*.type' => ['required', 'in:rating,text,choice'],
            'questions.*.options' => ['nullable', 'array'],
        ]);

        abort_unless(in_array((int) $validated['outlet_id'], $user->ownedOutlets->pluck('id')->all(), true), 403);

        foreach ($validated['questions'] as $index => $question) {
            if ($question['type'] === 'choice') {
                $options = array_values(array_filter($question['options'] ?? [], fn ($option) => trim((string) $option) !== ''));
                if (count($options) < 2) {
                    return response()->json([
                        'message' => 'Pilihan ganda minimal 2 opsi.',
                        'errors' => [
                            "questions.$index.options" => ['Pilihan ganda minimal 2 opsi.'],
                        ],
                    ], 422);
                }
            }
        }

        $survey = DB::transaction(function () use ($validated, $user) {
            $survey = Survey::create([
                'type' => 'outlet',
                'outlet_id' => $validated['outlet_id'],
                'title' => $validated['title'],
                'slug' => Str::slug($validated['title']) . '-' . Str::lower(Str::random(6)),
                'description' => $validated['description'] ?? null,
                'is_active' => true,
                'created_by' => $user->id,
            ]);

            foreach ($validated['questions'] as $index => $question) {
                $options = null;
                if ($question['type'] === 'choice') {
                    $options = array_values(array_filter($question['options'] ?? [], fn ($option) => trim((string) $option) !== ''));
                }

                SurveyQuestion::create([
                    'survey_id' => $survey->id,
                    'question' => $question['question'],
                    'type' => $question['type'],
                    'options' => $options,
                    'sort_order' => $index,
                ]);
            }

            return $survey;
        });

        $survey->load(['outlet:id,name,slug'])->loadCount('responses');

        return response()->json([
            'message' => 'Survey berhasil dibuat.',
            'survey' => $this->transformSurvey($survey, true),
        ], 201);
    }

    public function toggle(Request $request, Survey $survey): JsonResponse
    {
        $this->authorizeSurvey($request, $survey);
        $survey->update(['is_active' => !$survey->is_active]);
        $survey->load(['outlet:id,name,slug'])->loadCount('responses');

        return response()->json([
            'message' => $survey->is_active ? 'Survey diaktifkan.' : 'Survey dinonaktifkan.',
            'survey' => $this->transformSurvey($survey, true),
        ]);
    }

    public function destroy(Request $request, Survey $survey): JsonResponse
    {
        $this->authorizeSurvey($request, $survey);
        $survey->delete();

        return response()->json([
            'message' => 'Survey berhasil dihapus.',
        ]);
    }

    protected function authorizeSurvey(Request $request, Survey $survey): void
    {
        $user = $request->user();
        abort_unless($user->isOwner(), 403);
        abort_unless($survey->type === 'outlet', 403);
        abort_unless($user->ownedOutlets->pluck('id')->contains((int) $survey->outlet_id), 403);
    }

    protected function transformSurvey(Survey $survey, bool $detailed = false): array
    {
        $base = [
            'id' => $survey->id,
            'title' => $survey->title,
            'slug' => $survey->slug,
            'description' => $survey->description,
            'type' => $survey->type,
            'is_active' => (bool) $survey->is_active,
            'responses_count' => (int) ($survey->responses_count ?? 0),
            'average_rating' => $survey->averageRating(),
            'public_url' => url("/survey/{$survey->slug}"),
            'created_at' => optional($survey->created_at)->toIso8601String(),
            'outlet' => $survey->outlet ? [
                'id' => $survey->outlet->id,
                'name' => $survey->outlet->name,
                'slug' => $survey->outlet->slug,
            ] : null,
        ];

        if ($detailed) {
            $base['outlet_id'] = $survey->outlet_id;
            $base['questions'] = $survey->relationLoaded('questions')
                ? $survey->questions->map(fn (SurveyQuestion $question) => [
                    'id' => $question->id,
                    'question' => $question->question,
                    'type' => $question->type,
                    'options' => $question->options ?? [],
                    'sort_order' => $question->sort_order,
                ])->values()
                : [];
        }

        return $base;
    }

    protected function questionStats(SurveyQuestion $question, $responses): array
    {
        $answers = $responses
            ->flatMap->answers
            ->where('survey_question_id', $question->id);

        $stat = [
            'id' => $question->id,
            'question' => $question->question,
            'type' => $question->type,
        ];

        if ($question->type === 'rating') {
            $ratings = $answers->pluck('rating')->filter();
            $stat['average'] = $ratings->count() > 0 ? round($ratings->avg(), 1) : 0;
            $stat['count'] = $ratings->count();
            $stat['distribution'] = collect(range(1, 5))
                ->mapWithKeys(fn ($rating) => [(string) $rating => $ratings->filter(fn ($item) => (int) $item === $rating)->count()])
                ->all();
        } elseif ($question->type === 'choice') {
            $options = $question->options ?? [];
            $stat['options'] = $options;
            $stat['distribution'] = collect($options)
                ->mapWithKeys(fn ($option) => [$option => $answers->where('answer', $option)->count()])
                ->all();
        } else {
            $stat['answers'] = $answers
                ->pluck('answer')
                ->filter(fn ($answer) => filled($answer))
                ->values()
                ->take(20)
                ->all();
        }

        return $stat;
    }
}
