<?php

namespace App\Livewire\Admin\Attendance;

use Livewire\Component;
use App\Models\Employee;
use App\Models\Attendance;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Index extends Component
{
    use WithPagination;

    public $selectedDate;
    public $search = '';
    public $burnoutFilter = false;

    public function mount()
    {
        $this->selectedDate = today()->format('Y-m-d');
    }

    public function checkIn($employeeId)
    {
        try {
            $attendance = Attendance::firstOrNew([
                'employee_id' => $employeeId,
                'date' => $this->selectedDate,
            ]);

            if (!$attendance->check_in) {
                $attendance->fill([
                    'check_in' => now()->toTimeString(), // Use toTimeString() for proper time format
                    'status' => 'present'
                ])->save();

                session()->flash('success', 'Check-in recorded successfully at ' . now()->format('h:i A'));
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to record check-in: ' . $e->getMessage());
        }
    }

    public function checkOut($employeeId)
    {
        try {
            $attendance = Attendance::where('employee_id', $employeeId)
                ->whereDate('date', $this->selectedDate)
                ->first();

            if ($attendance && $attendance->check_in && !$attendance->check_out) {
                $attendance->update([
                    'check_out' => now()->toTimeString() // Use toTimeString() for proper time format
                ]);

                // Calculate hours worked
                $checkIn = Carbon::parse($attendance->check_in);
                $checkOut = Carbon::parse($attendance->check_out);
                $hours = $checkOut->diffInMinutes($checkIn) / 60;

                $attendance->update([
                    'hours_worked' => round($hours, 2),
                    'overtime' => $hours > 8
                ]);

                session()->flash('success', 'Check-out recorded successfully at ' . now()->format('h:i A'));
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to record check-out: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $employees = Employee::inCompany()
            ->with(['designation.department', 'attendances' => function ($query) {
                $query->whereDate('date', $this->selectedDate);
            }])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->when($this->burnoutFilter, function ($query) {
                $query->whereHas('attendances', function ($q) {
                    $q->select('employee_id')
                        ->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])
                        ->groupBy('employee_id')
                        ->havingRaw('SUM(hours_worked) > 40');
                });
            })
            ->paginate(10);

        // Calculate burnout risks count for current company only
        $burnoutEmployees = Employee::inCompany()
            ->whereHas('attendances', function ($query) {
                $query->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])
                    ->groupBy('employee_id')
                    ->havingRaw('SUM(hours_worked) > 40');
            })
            ->count();

        return view('livewire.admin.attendance.index', [
            'employees' => $employees,
            'burnoutEmployees' => $burnoutEmployees
        ]);
    }
}
