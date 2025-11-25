<?php

namespace App\Livewire;

use App\Models\Feedback;
use App\Models\FeedbackResponse;
use App\Notifications\NewResponseNotification;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class EmployeeFeedbackResponse extends Component
{
    public Feedback $feedback;
    public $response = '';
    public $employee_email = '';

    protected $rules = [
        'response' => 'required|string|min:5|max:1000',
        'employee_email' => 'required|email'
    ];

    public function mount($feedbackId)
    {
        $this->feedback = Feedback::with(['employee', 'sender', 'responses'])
            ->findOrFail($feedbackId);
    }

    public function submitResponse()
    {
        $this->validate();

        // Verify employee email matches
        if ($this->employee_email !== $this->feedback->employee->email) {
            session()->flash('error', 'Email does not match the employee record.');
            return;
        }

        // Create response
        $feedbackResponse = FeedbackResponse::create([
            'feedback_id' => $this->feedback->id,
            'responder_email' => $this->employee_email,
            'responder_name' => $this->feedback->employee->name,
            'response' => $this->response,
            'type' => 'employee'
        ]);

        // Notify manager about the response
        try {
            $this->feedback->sender->notify(new NewResponseNotification($this->feedback, $feedbackResponse));
        } catch (\Exception $e) {
            Log::error('Failed to send response notification: ' . $e->getMessage());
        }

        $this->response = '';
        $this->employee_email = '';

        session()->flash('success', 'Thank you for your response! The manager has been notified.');
        $this->feedback->refresh();
    }

    public function render()
    {
        return view('livewire.employee-feedback-response');
    }
}