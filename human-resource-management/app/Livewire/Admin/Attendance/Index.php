<?php

namespace App\Livewire\Admin\Attendance;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\CompanySetting;
use Carbon\Carbon;

class Index extends Component
{
    use WithPagination;

    public $selectedDate;
    public $search = '';
    public $burnoutFilter = false;
    public $exportEmployees;
    public $burnoutEmployees = 0; // ADD THIS

    // ADD THESE SETTINGS PROPERTIES
    public $showSettings = false;
    public $lateThreshold;
    public $regularHours;
    public $burnoutThreshold;
    public $workStartTime;
    public $workEndTime;

    protected $rules = [
        'selectedDate' => 'required|date|before_or_equal:today',
    ];

    public function mount()
    {
        $this->selectedDate = today()->format('Y-m-d');

        $this->exportEmployees = Employee::active()
            ->orderBy('name')
            ->select('id', 'name', 'employee_id')
            ->get();

        $this->calculateBurnoutEmployees(); // ADD THIS
        $this->loadSettings(); // ADD THIS
    }

    // ADD THIS METHOD
    public function calculateBurnoutEmployees()
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        
        $this->burnoutEmployees = Employee::whereHas('attendances', function ($query) use ($startOfWeek, $endOfWeek) {
            $query->whereBetween('date', [$startOfWeek, $endOfWeek])
                  ->where('is_burnout_risk', true);
        })->distinct()->count();
    }

    // ADD THIS METHOD
    public function loadSettings()
    {
        $settings = CompanySetting::first();
        if ($settings) {
            $this->lateThreshold = $settings->late_threshold->format('H:i:s');
            $this->regularHours = $settings->regular_hours;
            $this->burnoutThreshold = $settings->burnout_threshold;
            $this->workStartTime = $settings->work_start_time->format('H:i:s');
            $this->workEndTime = $settings->work_end_time->format('H:i:s');
        }
    }

    public function updated($property)
    {
        if ($property === 'selectedDate') {
            $this->validateOnly('selectedDate');
            $this->resetPage();
        }
    }

    /* ---------------------------------------------------------
     | ATTENDANCE OPERATIONS - THESE ARE CORRECT
     * --------------------------------------------------------- */

      public function updatedBurnoutFilter()
    {
        $this->resetPage();
    }



    public function checkIn($employeeId)
    {
        try {
            $this->validate();
            $employee = Employee::find($employeeId);
            if (!$employee) {
                session()->flash('error', 'Employee not found.');
                return;
            }

            if (Carbon::parse($this->selectedDate)->isFuture()) {
                session()->flash('error', 'Cannot check-in for future dates.');
                return;
            }

            $attendance = Attendance::firstOrCreate(
                [
                    'employee_id' => $employeeId,
                    'date' => $this->selectedDate,
                ],
                [
                    'status' => 'present'
                ]
            );

            if (!$attendance->check_in) {
                $attendance->update([
                    'check_in' => now(),
                    'status' => 'present'
                ]);

                $attendance->checkLateArrival();
                
                session()->flash('success', 'Check-in recorded successfully for ' . $employee->name . '.');
            } else {
                session()->flash('error', 'Check-in already recorded for today.');
            }

            // UPDATE BURNOUT COUNT
            $this->calculateBurnoutEmployees();

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to record check-in: ' . $e->getMessage());
        }
    }

    

    public function checkOut($employeeId)
    {
        try {
            $this->validate();
            $employee = Employee::find($employeeId);
            if (!$employee) {
                session()->flash('error', 'Employee not found.');
                return;
            }

            $attendance = Attendance::where('employee_id', $employeeId)
                ->whereDate('date', $this->selectedDate)
                ->first();

            if (!$attendance) {
                session()->flash('error', 'No attendance record found for today.');
                return;
            }

            if ($attendance->check_in && !$attendance->check_out) {
                if (now()->lte($attendance->check_in)) {
                    session()->flash('error', 'Check-out time must be after check-in time.');
                    return;
                }

                $attendance->update([
                    'check_out' => now()
                ]);

                $attendance->calculateHoursWorked();

                session()->flash('success', 'Check-out recorded successfully for ' . $employee->name . '.');

                // UPDATE BURNOUT COUNT
                $this->calculateBurnoutEmployees();
                
            } elseif ($attendance->check_out) {
                session()->flash('error', 'Check-out already recorded for today.');
            } else {
                session()->flash('error', 'No check-in found to check out from.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to record check-out: ' . $e->getMessage());
        }
    }

    public function markAbsent($employeeId)
    {
        try {
            $this->validate();
            $employee = Employee::find($employeeId);
            if (!$employee) {
                session()->flash('error', 'Employee not found.');
                return;
            }

            $attendance = Attendance::updateOrCreate(
                [
                    'employee_id' => $employeeId,
                    'date' => $this->selectedDate,
                ],
                [
                    'status' => 'absent',
                    'check_in' => null,
                    'check_out' => null,
                    'hours_worked' => 0,
                    'overtime' => false,
                    'overtime_hours' => 0,
                    'is_late' => false,
                    'is_burnout_risk' => false,
                    'weekly_hours' => 0,
                    'burnout_level' => 'Safe'
                ]
            );

            // UPDATE BURNOUT STATUS FOR THE WEEK
            $attendance->updateBurnoutStatus();
            $this->calculateBurnoutEmployees();

            session()->flash('success', $employee->name . ' marked as absent.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to mark as absent: ' . $e->getMessage());
        }
    }


    public function saveSettings()
    {
        $this->validate([
            'lateThreshold' => 'required',
            'regularHours' => 'required|numeric|min:1|max:24',
            'burnoutThreshold' => 'required|numeric|min:1|max:168',
            'workStartTime' => 'required',
            'workEndTime' => 'required',
        ]);

        CompanySetting::updateOrCreate([], [
            'late_threshold' => $this->lateThreshold,
            'regular_hours' => $this->regularHours,
            'burnout_threshold' => $this->burnoutThreshold,
            'work_start_time' => $this->workStartTime,
            'work_end_time' => $this->workEndTime,
        ]);

        $this->showSettings = false;
        session()->flash('success', 'Settings updated successfully!');
        
        // RECALCULATE BURNOUT STATUS FOR ALL EMPLOYEES WITH NEW THRESHOLD
        $this->recalculateAllBurnoutStatus();
        $this->calculateBurnoutEmployees();
    }

    // ADD THIS METHOD TO RECALCULATE ALL BURNOUT STATUS
    private function recalculateAllBurnoutStatus()
    {
        $attendances = Attendance::whereNotNull('check_out')
            ->whereNotNull('check_in')
            ->get();

        foreach ($attendances as $attendance) {
            $attendance->updateBurnoutStatus();
        }
    }

    public function render()
    {
        $employees = Employee::with([
                'designation:id,name,department_id',
                'designation.department:id,name',
                'attendances' => fn ($q) =>
                    $q->whereDate('date', $this->selectedDate)
            ])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhere('employee_id', 'like', "%{$this->search}%");
                });
            })
            ->when($this->burnoutFilter, function ($query) {
                $query->whereHas('attendances', fn ($q) =>
                    $q->where('is_burnout_risk', true)
                       ->whereBetween('date', [
                           Carbon::now()->startOfWeek(),
                           Carbon::now()->endOfWeek()
                       ])
                );
            })
            ->orderBy('name')
            ->paginate(10);

        $settings = CompanySetting::first();

        return view('livewire.admin.attendance.index', [
            'employees'       => $employees,
            'settings'        => $settings,
            'exportEmployees' => $this->exportEmployees,
        ]);
    }
}