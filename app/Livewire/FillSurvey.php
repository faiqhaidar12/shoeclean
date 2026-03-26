<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Survey;
use App\Models\SurveyResponse;
use App\Models\SurveyAnswer;
use Livewire\Attributes\Layout;

#[Layout('layouts.storefront')]
class FillSurvey extends Component
{
    public Survey $survey;
    public $questions = [];

    // Respondent info
    public $respondent_name = '';
    public $respondent_phone = '';
    public $respondent_type = 'customer';

    // Answers keyed by question id
    public $answers = [];

    // UI
    public $step = 1; // 1 = intro + info, 2 = questions, 3 = thank you
    public $submitted = false;

    public function mount(Survey $survey)
    {
        if (!$survey->is_active) {
            abort(404, 'Survey tidak tersedia.');
        }

        $this->survey = $survey;
        $this->questions = $survey->questions->toArray();

        // Initialize answers
        foreach ($this->questions as $q) {
            if ($q['type'] === 'rating') {
                $this->answers[$q['id']] = 0;
            } else {
                $this->answers[$q['id']] = '';
            }
        }

        // If platform survey, allow owner/customer selection
        if ($survey->type === 'outlet') {
            $this->respondent_type = 'customer';
        }
    }

    public function nextStep()
    {
        if ($this->step === 1) {
            $this->validate([
                'respondent_name' => 'required|string|max:255',
            ], [
                'respondent_name.required' => 'Nama wajib diisi.',
            ]);
        }

        $this->step = min($this->step + 1, 3);
    }

    public function prevStep()
    {
        $this->step = max($this->step - 1, 1);
    }

    public function setRating($questionId, $value)
    {
        $this->answers[$questionId] = $value;
    }

    public function submit()
    {
        // Validate all questions answered
        foreach ($this->questions as $q) {
            $answer = $this->answers[$q['id']] ?? null;
            if ($q['type'] === 'rating' && (empty($answer) || $answer < 1)) {
                $this->addError('answers.' . $q['id'], 'Rating wajib diisi.');
                return;
            }
            if ($q['type'] === 'choice' && empty($answer)) {
                $this->addError('answers.' . $q['id'], 'Pilihan wajib diisi.');
                return;
            }
        }

        $response = SurveyResponse::create([
            'survey_id' => $this->survey->id,
            'respondent_name' => $this->respondent_name,
            'respondent_phone' => $this->respondent_phone ?: null,
            'respondent_type' => $this->respondent_type,
            'outlet_id' => $this->survey->outlet_id,
        ]);

        foreach ($this->questions as $q) {
            $answer = $this->answers[$q['id']] ?? null;

            SurveyAnswer::create([
                'survey_response_id' => $response->id,
                'survey_question_id' => $q['id'],
                'answer' => $q['type'] !== 'rating' ? $answer : null,
                'rating' => $q['type'] === 'rating' ? (int)$answer : null,
            ]);
        }

        $this->submitted = true;
        $this->step = 3;
    }

    public function render()
    {
        return view('livewire.fill-survey');
    }
}
