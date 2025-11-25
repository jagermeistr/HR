<?php

namespace App\Livewire\Admin\Feedback;

use App\Models\Employee;
use App\Models\Feedback;
use App\Notifications\NewFeedbackNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Create extends Component
{
    public $employee_id;
    public $message;
    public $type = 'positive';
    public $is_anonymous = false;
    public $employees = [];

    protected $rules = [
        'employee_id' => 'required|exists:employees,id',
        'message' => 'required|string|min:10|max:1000',
        'type' => 'required|in:positive,constructive,general',
        'is_anonymous' => 'boolean'
    ];

    public function mount(): void
    {
        $this->employees = Employee::inCompany()
            ->with(['designation.department'])
            ->get();
    }

    public function save()
    {
        $this->validate();

        $feedback = Feedback::create([
            'sender_id' => Auth::id(),
            'employee_id' => $this->employee_id,
            'message' => $this->message,
            'type' => $this->type,
            'is_anonymous' => $this->is_anonymous,
            'status' => 'sent'
        ]);

        $feedback->markAsSent();

        // Send email notification to the employee
        try {
            $employee = Employee::find($this->employee_id);
            
            if ($employee && $employee->email) {
                $employee->notify(new NewFeedbackNotification($feedback));
                
                session()->flash('success', 'Feedback sent successfully to ' . $employee->email . '!');
            } else {
                session()->flash('warning', 'Feedback saved but employee email not found.');
            }
        } catch (\Exception $e) {
            Log::error('Failed to send feedback email: ' . $e->getMessage());
            session()->flash('error', 'Feedback saved but email failed to send: ' . $e->getMessage());
        }

        return $this->redirect(route('feedback.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.feedback.create');
    }
}