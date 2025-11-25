<?php

namespace App\Livewire\Admin\Feedback;

use App\Models\Feedback;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Notifications\NewFeedbackResponseNotification;
use Illuminate\Support\Facades\Log;

class Show extends Component
{
    public Feedback $feedback;
    public $response = '';

    protected $rules = [
        'response' => 'required|string|min:5|max:500'
    ];

    public function mount(Feedback $feedback)
    {
        $user = Auth::user();
        
        if (!$feedback->canView($user)) {
            abort(403, 'Unauthorized action.');
        }

        $this->feedback = $feedback->load('responses.user');
    }

    public function addResponse()
    {
        $this->validate();

        if (!$this->feedback->canRespond(Auth::user())) {
            abort(403, 'Unauthorized action.');
        }

        $newResponse = $this->feedback->responses()->create([
            'user_id' => Auth::id(),
            'response' => $this->response
        ]);

        // Send email notification to the feedback sender
        try {
            // Notify the original feedback sender about the response
            if (Auth::id() !== $this->feedback->sender_id) {
                $this->feedback->sender->notify(
                    new NewFeedbackResponseNotification($this->feedback, $newResponse)
                );
            }

            // Also notify the employee if they're not the one responding
            if (Auth::id() !== $this->feedback->employee->user_id && $this->feedback->employee->email) {
                $this->feedback->employee->notify(
                    new NewFeedbackResponseNotification($this->feedback, $newResponse)
                );
            }
        } catch (\Exception $e) {
            Log::error('Failed to send response notification: ' . $e->getMessage());
        }

        $this->response = '';
        $this->feedback->refresh();

        session()->flash('message', 'Response added successfully!');
    }

    public function render()
    {
        return view('livewire.admin.feedback.show');
    }
}