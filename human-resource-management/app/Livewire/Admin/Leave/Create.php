<?php

namespace App\Livewire\Admin\Leave;

use Livewire\Component;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Employee;

class Create extends Component
{
    public $employees;
    public $leaveTypes;
    public $form = [
        'employee_id' => '',
        'leave_type_id' => '',
        'start_date' => '',
        'end_date' => '',
        'reason' => '',
        'status' => 'approved'
    ];

    protected $rules = [
        'form.employee_id' => 'required|exists:employees,id',
        'form.leave_type_id' => 'required|exists:leave_types,id',
        'form.start_date' => 'required|date|after_or_equal:today',
        'form.end_date' => 'required|date|after_or_equal:form.start_date',
        'form.reason' => 'required|string|min:10|max:500',
        'form.status' => 'required|in:pending,approved,rejected',
    ];

    public function mount()
    {
        $this->employees = Employee::inCompany()->get();
        $this->leaveTypes = LeaveType::where('is_active', true)->get();
    }

    public function save()
    {
        $this->validate();

        // Calculate total days (excluding weekends)
        $startDate = \Carbon\Carbon::parse($this->form['start_date']);
        $endDate = \Carbon\Carbon::parse($this->form['end_date']);
        $totalDays = $startDate->diffInDaysFiltered(fn($date) => !$date->isWeekend(), $endDate) + 1;

        LeaveRequest::create([
            'employee_id' => $this->form['employee_id'],
            'leave_type_id' => $this->form['leave_type_id'],
            'start_date' => $this->form['start_date'],
            'end_date' => $this->form['end_date'],
            'total_days' => $totalDays,
            'reason' => $this->form['reason'],
            'status' => $this->form['status'],
        ]);

        session()->flash('success', 'Leave request created successfully.');
        return redirect()->route('leave.index');
    }

    public function render()
    {
        return view('livewire.admin.leave.create');
    }
}