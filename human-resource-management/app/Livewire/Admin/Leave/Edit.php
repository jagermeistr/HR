<?php

namespace App\Livewire\Admin\Leave;

use Livewire\Component;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Employee;

class Edit extends Component
{
    public $leaveRequest;
    public $employees;
    public $leaveTypes;
    public $form = [
        'employee_id' => '',
        'leave_type_id' => '',
        'start_date' => '',
        'end_date' => '',
        'reason' => '',
        'status' => ''
    ];

    protected $rules = [
        'form.employee_id' => 'required|exists:employees,id',
        'form.leave_type_id' => 'required|exists:leave_types,id',
        'form.start_date' => 'required|date',
        'form.end_date' => 'required|date|after_or_equal:form.start_date',
        'form.reason' => 'required|string|min:10|max:500',
        'form.status' => 'required|in:pending,approved,rejected',
    ];

    public function mount($leaveRequest)
    {
        $this->leaveRequest = LeaveRequest::with(['employee.designation.department', 'leaveType'])
            ->whereHas('employee', function ($query) {
                $query->inCompany();
            })
            ->findOrFail($leaveRequest);
            
        $this->employees = Employee::inCompany()->get();
        $this->leaveTypes = LeaveType::where('is_active', true)->get();
        
        // Initialize form with existing data
        $this->form = [
            'employee_id' => $this->leaveRequest->employee_id,
            'leave_type_id' => $this->leaveRequest->leave_type_id,
            'start_date' => $this->leaveRequest->start_date->format('Y-m-d'),
            'end_date' => $this->leaveRequest->end_date->format('Y-m-d'),
            'reason' => $this->leaveRequest->reason,
            'status' => $this->leaveRequest->status,
        ];
    }

    public function update()
    {
        $this->validate();

        // Calculate total days (excluding weekends)
        $startDate = \Carbon\Carbon::parse($this->form['start_date']);
        $endDate = \Carbon\Carbon::parse($this->form['end_date']);
        $totalDays = $startDate->diffInDaysFiltered(fn($date) => !$date->isWeekend(), $endDate) + 1;

        $this->leaveRequest->update([
            'employee_id' => $this->form['employee_id'],
            'leave_type_id' => $this->form['leave_type_id'],
            'start_date' => $this->form['start_date'],
            'end_date' => $this->form['end_date'],
            'total_days' => $totalDays,
            'reason' => $this->form['reason'],
            'status' => $this->form['status'],
        ]);

        session()->flash('success', 'Leave request updated successfully.');
        return redirect()->route('leave.index');
    }

    public function render()
    {
        return view('livewire.admin.leave.edit');
    }
}