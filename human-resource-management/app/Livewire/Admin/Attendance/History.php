<?php

namespace App\Livewire\Admin\Attendance;

use Livewire\Component;
use App\Models\Employee;
use App\Models\Attendance;
use Livewire\WithPagination;

class History extends Component
{
    use WithPagination;

    public $employeeId = '';
    public $startDate;
    public $endDate;
    public $statusFilter = '';
    public $employees;

    public function mount()
    {
        $this->employees = Employee::inCompany()->get();
        $this->startDate = now()->subDays(30)->format('Y-m-d'); // Default to last 30 days
        $this->endDate = now()->format('Y-m-d');
    }

    public function render()
    {
        $query = Attendance::with(['employee.designation.department'])
            ->when($this->employeeId, function ($query) {
                $query->where('employee_id', $this->employeeId);
            })
            ->when($this->startDate && $this->endDate, function ($query) {
                $query->whereBetween('date', [$this->startDate, $this->endDate]);
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->latest('date');

        $attendanceHistory = $query->paginate(20);

        // Calculate summary statistics
        $summary = [
            'total_days' => $attendanceHistory->total(),
            'present_days' => $attendanceHistory->where('status', 'present')->count(),
            'absent_days' => $attendanceHistory->where('status', 'absent')->count(),
            'late_days' => $attendanceHistory->where('status', 'late')->count(),
            'total_hours' => $attendanceHistory->sum('hours_worked'),
            'overtime_hours' => $attendanceHistory->where('overtime', true)->sum('hours_worked') - 
                               ($attendanceHistory->where('overtime', true)->count() * 8),
        ];

        return view('livewire.admin.attendance.history', [
            'attendanceHistory' => $attendanceHistory,
            'summary' => $summary,
        ]);
    }

    public function exportToCsv()
    {
        $query = Attendance::with(['employee.designation.department'])
            ->when($this->employeeId, function ($query) {
                $query->where('employee_id', $this->employeeId);
            })
            ->when($this->startDate && $this->endDate, function ($query) {
                $query->whereBetween('date', [$this->startDate, $this->endDate]);
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->latest('date')
            ->get();

        $fileName = 'attendance-history-' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function () use ($query) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'Date',
                'Employee',
                'Designation', 
                'Department',
                'Check In',
                'Check Out',
                'Hours Worked',
                'Status',
                'Overtime'
            ]);

            // Add data rows
            foreach ($query as $attendance) {
                fputcsv($file, [
                    $attendance->date->format('Y-m-d'),
                    $attendance->employee->name,
                    $attendance->employee->designation->name,
                    $attendance->employee->designation->department->name ?? 'N/A',
                    $attendance->check_in ? $attendance->check_in->format('H:i:s') : 'N/A',
                    $attendance->check_out ? $attendance->check_out->format('H:i:s') : 'N/A',
                    $attendance->hours_worked,
                    ucfirst($attendance->status),
                    $attendance->overtime ? 'Yes' : 'No'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}