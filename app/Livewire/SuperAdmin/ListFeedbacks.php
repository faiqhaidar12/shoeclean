<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use App\Models\Feedback;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.superadmin')]
class ListFeedbacks extends Component
{
    use WithPagination;

    public $category = '';

    public function updatingCategory()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Feedback::with(['user', 'outlet'])->latest();

        if ($this->category) {
            $query->where('category', $this->category);
        }

        $feedbacks = $query->paginate(10);

        return view('livewire.super-admin.list-feedbacks', [
            'feedbacks' => $feedbacks
        ]);
    }
}
