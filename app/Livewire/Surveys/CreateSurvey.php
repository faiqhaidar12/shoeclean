<?php

namespace App\Livewire\Surveys;

use Livewire\Component;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;

class CreateSurvey extends Component
{
    public $context; // 'superadmin' or 'owner'

    public $title = '';
    public $description = '';
    public $outlet_id = '';
    public $questions = [];

    public $outlets = []; // For owner only

    public function mount()
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            $this->context = 'superadmin';
        } elseif ($user->isOwner()) {
            $this->context = 'owner';
            $this->outlets = $user->ownedOutlets->toArray();
            if (count($this->outlets) === 1) {
                $this->outlet_id = $this->outlets[0]['id'];
            }
        } else {
            abort(403);
        }

        // Start with one question
        $this->addQuestion();
    }

    public function addQuestion()
    {
        $this->questions[] = [
            'question' => '',
            'type' => 'rating',
            'options' => ['', ''],
        ];
    }

    public function removeQuestion($index)
    {
        unset($this->questions[$index]);
        $this->questions = array_values($this->questions);
        if (count($this->questions) === 0) {
            $this->addQuestion();
        }
    }

    public function addOption($qIndex)
    {
        $this->questions[$qIndex]['options'][] = '';
    }

    public function removeOption($qIndex, $oIndex)
    {
        unset($this->questions[$qIndex]['options'][$oIndex]);
        $this->questions[$qIndex]['options'] = array_values($this->questions[$qIndex]['options']);
    }

    public function save()
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'questions' => 'required|array|min:1',
            'questions.*.question' => 'required|string|max:500',
            'questions.*.type' => 'required|in:rating,text,choice',
        ];

        if ($this->context === 'owner') {
            $rules['outlet_id'] = 'required|exists:outlets,id';
        }

        $this->validate($rules, [
            'title.required' => 'Judul survey wajib diisi.',
            'questions.*.question.required' => 'Pertanyaan wajib diisi.',
            'outlet_id.required' => 'Pilih outlet.',
        ]);

        // Validate choice questions have options
        foreach ($this->questions as $i => $q) {
            if ($q['type'] === 'choice') {
                $filtered = array_filter($q['options'], fn($o) => trim($o) !== '');
                if (count($filtered) < 2) {
                    $this->addError("questions.{$i}.options", 'Pilihan ganda minimal 2 opsi.');
                    return;
                }
            }
        }

        $slug = Str::slug($this->title) . '-' . Str::random(6);

        $survey = Survey::create([
            'type' => $this->context === 'superadmin' ? 'platform' : 'outlet',
            'outlet_id' => $this->context === 'owner' ? $this->outlet_id : null,
            'title' => $this->title,
            'slug' => $slug,
            'description' => $this->description,
            'is_active' => true,
            'created_by' => auth()->id(),
        ]);

        foreach ($this->questions as $i => $q) {
            $options = null;
            if ($q['type'] === 'choice') {
                $options = array_values(array_filter($q['options'], fn($o) => trim($o) !== ''));
            }

            SurveyQuestion::create([
                'survey_id' => $survey->id,
                'question' => $q['question'],
                'type' => $q['type'],
                'options' => $options,
                'sort_order' => $i,
            ]);
        }

        $redirectRoute = $this->context === 'superadmin'
            ? 'superadmin.surveys.index'
            : 'surveys.index';

        session()->flash('success', 'Survey berhasil dibuat!');
        return redirect()->route($redirectRoute);
    }

    public function render()
    {
        return view('livewire.surveys.create-survey')
            ->layout($this->context === 'superadmin' ? 'layouts.superadmin' : 'layouts.app');
    }
}
