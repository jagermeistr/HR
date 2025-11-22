<?php

namespace App\Livewire\Admin\Leave;

use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $statusFilter = '';

    public function deleteLeave($leaveRequestId)
    {
        try {
            \App\Models\LeaveRequest::findOrFail($leaveRequestId)->delete();
            session()->flash('success', 'Leave request deleted successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error deleting leave request: ' . $e->getMessage());
        }
    }

    public function render()
    {
        // Always define the variable first
        $leaveRequests = collect();
        
        try {
            // Check if the model and table exist
            if (class_exists('App\Models\LeaveRequest')) {
                $leaveRequests = \App\Models\LeaveRequest::with(['employee', 'leaveType'])
                    ->when($this->statusFilter, function ($query) {
                        $query->where('status', $this->statusFilter);
                    })
                    ->latest()
                    ->paginate(10);
            }
        } catch (\Exception $e) {
            // If there's any error, use empty collection
            $leaveRequests = collect();
        }

        // Always return the view with leaveRequests
        return view('livewire.admin.leave.index', [
            'leaveRequests' => $leaveRequests,
        ]);
    }
}