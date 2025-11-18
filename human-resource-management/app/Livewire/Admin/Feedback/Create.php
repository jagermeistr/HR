<?php

namespace App\Livewire\Admin\Feedback;

use App\Models\Employee;
use App\Models\Feedback;
use Illuminate\Support\Facades\Auth;
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
        // Keep employees as Eloquent collection objects, not arrays
        $this->employees = Employee::inCompany()
            ->with(['designation.department'])
            ->get();
    }

    public function save()
    {
        $this->validate();

        Feedback::create([
            'sender_id' => Auth::id(),
            'employee_id' => $this->employee_id,
            'message' => $this->message,
            'type' => $this->type,
            'is_anonymous' => $this->is_anonymous,
            'status' => 'sent'
        ])->markAsSent();

        session()->flash('success', 'Feedback sent successfully to employee!');

        return $this->redirect(route('feedback.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.feedback.create');
    }
}