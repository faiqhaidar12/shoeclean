<?php

namespace App\Livewire\Surveys;

use Livewire\Component;
use App\Models\Survey;
use Livewire\Attributes\Layout;

class ListSurveys extends Component
{
    public $context; // 'superadmin' or 'owner'

    public function mount()
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            $this->context = 'superadmin';
        } elseif ($user->isOwner()) {
            $this->context = 'owner';
        } else {
            abort(403);
        }
    }

    public function toggleActive($surveyId)
    {
        $survey = $this->getSurveyQuery()->findOrFail($surveyId);
        $survey->update(['is_active' => !$survey->is_active]);
    }

    public function deleteSurvey($surveyId)
    {
        $survey = $this->getSurveyQuery()->findOrFail($surveyId);
        $survey->delete();
    }

    private function getSurveyQuery()
    {
        if ($this->context === 'superadmin') {
            return Survey::platform();
        }

        // Owner: show outlet surveys for their outlets
        $outletIds = auth()->user()->ownedOutlets->pluck('id');
        return Survey::outletType()->whereIn('outlet_id', $outletIds);
    }

    public function render()
    {
        $surveys = $this->getSurveyQuery()
            ->withCount('responses')
            ->with('outlet')
            ->latest()
            ->get();

        $createRoute = $this->context === 'superadmin'
            ? route('superadmin.surveys.create')
            : route('surveys.create');

        $resultsRouteName = $this->context === 'superadmin'
            ? 'superadmin.surveys.results'
            : 'surveys.results';

        return view('livewire.surveys.list-surveys', [
            'surveys' => $surveys,
            'createRoute' => $createRoute,
            'resultsRouteName' => $resultsRouteName,
        ])->layout($this->context === 'superadmin' ? 'layouts.superadmin' : 'layouts.app');
    }
}
