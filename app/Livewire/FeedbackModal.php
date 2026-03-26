<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Feedback;
use Livewire\Attributes\On;

class FeedbackModal extends Component
{
    public $showModal = false;
    public $category = 'saran';
    public $message = '';
    public $sent = false;

    #[On('open-feedback-modal')]
    public function open()
    {
        $this->reset(['message', 'sent', 'category']);
        $this->showModal = true;
    }

    public function submit()
    {
        $this->validate([
            'category' => 'required|in:keluhan,ide,saran',
            'message' => 'required|min:10|max:2000',
        ], [
            'message.required' => 'Pesan tidak boleh kosong.',
            'message.min' => 'Pesan minimal 10 karakter.',
        ]);

        $user = auth()->user();

        Feedback::create([
            'user_id' => $user->id,
            'outlet_id' => $user->outlet_id,
            'category' => $this->category,
            'message' => $this->message,
        ]);

        $this->sent = true;
        
        // Auto close after 2 seconds
        $this->dispatch('feedback-sent');
    }

    public function render()
    {
        return view('livewire.feedback-modal');
    }
}
