<?php

namespace App\Livewire\Surveys;

use Livewire\Component;
use App\Models\Survey;
use Livewire\Attributes\Layout;

class SurveyResults extends Component
{
    public Survey $survey;
    public $context;

    public function mount(Survey $survey)
    {
        $user = auth()->user();

        if ($user->isSuperAdmin() && $survey->type === 'platform') {
            $this->context = 'superadmin';
        } elseif ($user->isOwner() && $survey->type === 'outlet') {
            // Verify ownership
            $outletIds = $user->ownedOutlets->pluck('id')->toArray();
            if (!in_array($survey->outlet_id, $outletIds)) {
                abort(403);
            }
            $this->context = 'owner';
        } else {
            abort(403);
        }

        $this->survey = $survey->load(['questions', 'responses.answers']);
    }

    public function render()
    {
        $questions = $this->survey->questions;
        $responses = $this->survey->responses()->with('answers')->latest()->get();
        $totalResponses = $responses->count();

        // Build question stats
        $questionStats = [];
        foreach ($questions as $question) {
            $stat = [
                'question' => $question->question,
                'type' => $question->type,
                'id' => $question->id,
            ];

            $answers = $responses->flatMap->answers->where('survey_question_id', $question->id);

            if ($question->type === 'rating') {
                $ratings = $answers->pluck('rating')->filter();
                $stat['average'] = $ratings->count() > 0 ? round($ratings->avg(), 1) : 0;
                $stat['count'] = $ratings->count();
                $stat['distribution'] = [];
                for ($i = 1; $i <= 5; $i++) {
                    $stat['distribution'][$i] = $ratings->filter(fn($r) => $r == $i)->count();
                }
            } elseif ($question->type === 'text') {
                $stat['answers'] = $answers->pluck('answer')->filter()->values()->toArray();
            } elseif ($question->type === 'choice') {
                $stat['options'] = $question->options ?? [];
                $stat['distribution'] = [];
                foreach ($stat['options'] as $option) {
                    $stat['distribution'][$option] = $answers->where('answer', $option)->count();
                }
            }

            $questionStats[] = $stat;
        }

        $backRoute = $this->context === 'superadmin'
            ? route('superadmin.surveys.index')
            : route('surveys.index');

        return view('livewire.surveys.survey-results', [
            'questionStats' => $questionStats,
            'totalResponses' => $totalResponses,
            'responses' => $responses,
            'backRoute' => $backRoute,
        ])->layout($this->context === 'superadmin' ? 'layouts.superadmin' : 'layouts.app');
    }
}
