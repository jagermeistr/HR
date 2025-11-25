<?php

namespace App\Livewire\Admin\Attendance;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Department;
use App\Models\CompanySetting;
use Carbon\Carbon;

class History extends Component
{
    use WithPagination;

    public $employeeId = '';
    public $startDate;
    public $endDate;
    public $statusFilter = '';
    public $employees;
    
    // Burnout Analysis Properties
    public $timeRange = 'current_week';
    public $criticalCount = 0;
    public $highRiskCount = 0;
    public $moderateRiskCount = 0;
    public $safeCount = 0;
    public $riskDistribution = [];
    public $departmentRisks = [];
    public $highRiskEmployees = [];
    public $weeklyPatterns = [];
    public $interventions = [];
    public $actionPlan = [];

    protected $rules = [
        'startDate'      => 'required|date',
        'endDate'        => 'required|date|after_or_equal:startDate',
        'employeeId'     => 'nullable|exists:employees,id',
        'statusFilter'   => 'nullable|in:present,absent,late,half_day',
    ];

    protected $queryString = [
        'timeRange' => ['except' => 'current_week']
    ];

    public function mount()
    {
        $this->employees = Employee::active()->orderBy('name')->get();
        $this->startDate = now()->subDays(30)->format('Y-m-d');
        $this->endDate   = now()->format('Y-m-d');
        $this->loadBurnoutData();
    }

    public function updated($property)
    {
        if (in_array($property, ['employeeId', 'startDate', 'endDate', 'statusFilter'])) {
            $this->validateOnly($property);
            $this->resetPage();
        }
        
        if ($property === 'timeRange') {
            $this->loadBurnoutData();
        }
    }

    public function applyFilters()
    {
        $this->validate();
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->employeeId = '';
        $this->startDate = now()->subDays(30)->format('Y-m-d');
        $this->endDate   = now()->format('Y-m-d');
        $this->statusFilter = '';
        $this->resetPage();
    }

    // Burnout Analysis Methods
    private function loadBurnoutData()
    {
        $dateRange = $this->getDateRange();
        
        $this->loadRiskCounts($dateRange);
        $this->loadRiskDistribution($dateRange);
        $this->loadDepartmentAnalysis($dateRange);
        $this->loadHighRiskEmployees($dateRange);
        $this->loadWeeklyPatterns($dateRange);
        $this->loadInterventions();
        $this->loadActionPlan();
    }

    private function getDateRange()
    {
        $now = Carbon::now();
        
        return match($this->timeRange) {
            'last_week' => [
                'start' => $now->copy()->subWeek()->startOfWeek(),
                'end' => $now->copy()->subWeek()->endOfWeek()
            ],
            'last_month' => [
                'start' => $now->copy()->subMonth()->startOfMonth(),
                'end' => $now->copy()->subMonth()->endOfMonth()
            ],
            default => [ // current_week
                'start' => $now->copy()->startOfWeek(),
                'end' => $now->copy()->endOfWeek()
            ]
        };
    }

    private function loadRiskCounts($dateRange)
    {
        $employees = Employee::with(['attendances' => function($query) use ($dateRange) {
            $query->whereBetween('date', [$dateRange['start'], $dateRange['end']])
                  ->where('hours_worked', '>', 0);
        }])->get();

        $settings = CompanySetting::first();
        $burnoutThreshold = $settings->burnout_threshold ?? 48;

        $this->criticalCount = 0;
        $this->highRiskCount = 0;
        $this->moderateRiskCount = 0;
        $this->safeCount = 0;

        foreach ($employees as $employee) {
            $weeklyHours = $employee->attendances->sum('hours_worked');
            
            if ($weeklyHours > $burnoutThreshold + 20) {
                $this->criticalCount++;
            } elseif ($weeklyHours > $burnoutThreshold + 10) {
                $this->highRiskCount++;
            } elseif ($weeklyHours > $burnoutThreshold) {
                $this->moderateRiskCount++;
            } else {
                $this->safeCount++;
            }
        }
    }

    private function loadRiskDistribution($dateRange)
    {
        $totalEmployees = Employee::count();
        if ($totalEmployees === 0) return;

        $this->riskDistribution = [
            [
                'level' => 'Critical',
                'count' => $this->criticalCount,
                'percentage' => round(($this->criticalCount / $totalEmployees) * 100, 1),
                'color' => 'bg-red-500'
            ],
            [
                'level' => 'High Risk',
                'count' => $this->highRiskCount,
                'percentage' => round(($this->highRiskCount / $totalEmployees) * 100, 1),
                'color' => 'bg-orange-500'
            ],
            [
                'level' => 'Moderate Risk',
                'count' => $this->moderateRiskCount,
                'percentage' => round(($this->moderateRiskCount / $totalEmployees) * 100, 1),
                'color' => 'bg-yellow-500'
            ],
            [
                'level' => 'Safe',
                'count' => $this->safeCount,
                'percentage' => round(($this->safeCount / $totalEmployees) * 100, 1),
                'color' => 'bg-green-500'
            ]
        ];
    }

    private function loadDepartmentAnalysis($dateRange)
    {
        $departments = Department::with(['employees.attendances' => function($query) use ($dateRange) {
            $query->whereBetween('date', [$dateRange['start'], $dateRange['end']])
                  ->where('hours_worked', '>', 0);
        }])->get();

        $settings = CompanySetting::first();
        $burnoutThreshold = $settings->burnout_threshold ?? 48;

        $this->departmentRisks = [];

        foreach ($departments as $department) {
            $totalEmployees = $department->employees->count();
            if ($totalEmployees === 0) continue;

            $totalHours = 0;
            $atRiskCount = 0;

            foreach ($department->employees as $employee) {
                $weeklyHours = $employee->attendances->sum('hours_worked');
                $totalHours += $weeklyHours;
                
                if ($weeklyHours > $burnoutThreshold) {
                    $atRiskCount++;
                }
            }

            $avgHours = $totalEmployees > 0 ? round($totalHours / $totalEmployees, 1) : 0;
            
            // Determine risk level
            if ($avgHours > $burnoutThreshold + 20) {
                $riskLevel = 'Critical';
                $riskColor = 'bg-red-100 text-red-800';
            } elseif ($avgHours > $burnoutThreshold + 10) {
                $riskLevel = 'High Risk';
                $riskColor = 'bg-orange-100 text-orange-800';
            } elseif ($avgHours > $burnoutThreshold) {
                $riskLevel = 'Moderate Risk';
                $riskColor = 'bg-yellow-100 text-yellow-800';
            } else {
                $riskLevel = 'Safe';
                $riskColor = 'bg-green-100 text-green-800';
            }

            $this->departmentRisks[] = [
                'department' => $department->name,
                'avg_hours' => $avgHours,
                'at_risk_count' => $atRiskCount,
                'total_employees' => $totalEmployees,
                'risk_level' => $riskLevel,
                'risk_color' => $riskColor
            ];
        }
    }

    private function loadHighRiskEmployees($dateRange)
    {
        $employees = Employee::with(['designation.department', 'attendances' => function($query) use ($dateRange) {
            $query->whereBetween('date', [$dateRange['start'], $dateRange['end']])
                  ->where('hours_worked', '>', 0);
        }])->get();

        $settings = CompanySetting::first();
        $burnoutThreshold = $settings->burnout_threshold ?? 48;

        $this->highRiskEmployees = [];

        foreach ($employees as $employee) {
            $weeklyHours = $employee->attendances->sum('hours_worked');
            $overtimeHours = max(0, $weeklyHours - ($settings->regular_hours ?? 8) * 5);
            
            if ($weeklyHours <= $burnoutThreshold) continue;

            // Determine risk level and colors
            if ($weeklyHours > $burnoutThreshold + 20) {
                $riskLevel = 'Critical';
                $riskBadgeColor = 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
                $hoursColor = 'text-red-600 dark:text-red-400';
            } elseif ($weeklyHours > $burnoutThreshold + 10) {
                $riskLevel = 'High Risk';
                $riskBadgeColor = 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300';
                $hoursColor = 'text-orange-600 dark:text-orange-400';
            } else {
                $riskLevel = 'Moderate Risk';
                $riskBadgeColor = 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
                $hoursColor = 'text-yellow-600 dark:text-yellow-400';
            }

            // Generate recommendations
            $recommendation = $this->generateRecommendation($weeklyHours, $overtimeHours, $riskLevel);

            $this->highRiskEmployees[] = [
                'name' => $employee->name,
                'designation' => $employee->designation->name ?? 'No Designation',
                'department' => $employee->designation->department->name ?? 'No Department',
                'weekly_hours' => round($weeklyHours, 1),
                'daily_avg' => round($weeklyHours / 5, 1),
                'overtime_hours' => round($overtimeHours, 1),
                'risk_level' => $riskLevel,
                'risk_badge_color' => $riskBadgeColor,
                'hours_color' => $hoursColor,
                'recommendation' => $recommendation
            ];
        }

        // Sort by highest risk (most hours)
        usort($this->highRiskEmployees, function($a, $b) {
            return $b['weekly_hours'] <=> $a['weekly_hours'];
        });
    }

    private function generateRecommendation($weeklyHours, $overtimeHours, $riskLevel)
    {
        if ($riskLevel === 'Critical') {
            return "Immediate workload reduction required. Consider mandatory time off and workload redistribution.";
        } elseif ($riskLevel === 'High Risk') {
            return "Schedule workload review meeting. Implement overtime restrictions and monitor closely.";
        } else {
            return "Monitor workload trends. Consider flexible scheduling and ensure proper breaks.";
        }
    }

    private function loadWeeklyPatterns($dateRange)
    {
        // This would typically analyze patterns across multiple weeks
        // For now, we'll provide some sample patterns
        $this->weeklyPatterns = [
            [
                'title' => 'Consistent Overtime',
                'description' => 'Employees regularly working 10+ hours beyond standard week',
                'employee_count' => $this->highRiskCount + $this->criticalCount,
                'border_color' => 'border-red-400',
                'badge_color' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
            ],
            [
                'title' => 'Weekend Work Culture',
                'description' => 'Frequent weekend work indicating poor work-life balance',
                'employee_count' => round(($this->highRiskCount + $this->criticalCount) * 0.6),
                'border_color' => 'border-orange-400',
                'badge_color' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300'
            ]
        ];
    }

    private function loadInterventions()
    {
        $this->interventions = [
            [
                'title' => 'Workload Assessment',
                'description' => 'Conduct individual workload assessments for high-risk employees',
                'impact' => 'High'
            ],
            [
                'title' => 'Overtime Policy Review',
                'description' => 'Review and enforce overtime policies with management oversight',
                'impact' => 'Medium'
            ],
            [
                'title' => 'Flexible Scheduling',
                'description' => 'Implement flexible work arrangements for better work-life balance',
                'impact' => 'Medium'
            ],
            [
                'title' => 'Wellness Program',
                'description' => 'Introduce employee wellness programs and mental health support',
                'impact' => 'High'
            ]
        ];
    }

    private function loadActionPlan()
    {
        $this->actionPlan = [
            [
                'timeframe' => 'This Week',
                'title' => 'Immediate Actions',
                'tasks' => [
                    'Review high-risk employee list',
                    'Schedule 1:1 meetings with critical cases',
                    'Implement immediate overtime restrictions'
                ],
                'icon_bg' => 'bg-purple-100 dark:bg-purple-900/30',
                'icon_color' => 'text-purple-600 dark:text-purple-400',
                'text_color' => 'text-purple-700 dark:text-purple-300'
            ],
            [
                'timeframe' => 'Next 2 Weeks',
                'title' => 'Short-term Solutions',
                'tasks' => [
                    'Complete workload assessments',
                    'Develop department-level action plans',
                    'Train managers on burnout prevention'
                ],
                'icon_bg' => 'bg-indigo-100 dark:bg-indigo-900/30',
                'icon_color' => 'text-indigo-600 dark:text-indigo-400',
                'text_color' => 'text-indigo-700 dark:text-indigo-300'
            ],
            [
                'timeframe' => 'Next 30 Days',
                'title' => 'Long-term Strategy',
                'tasks' => [
                    'Implement wellness program',
                    'Review and update company policies',
                    'Establish continuous monitoring system'
                ],
                'icon_bg' => 'bg-blue-100 dark:bg-blue-900/30',
                'icon_color' => 'text-blue-600 dark:text-blue-400',
                'text_color' => 'text-blue-700 dark:text-blue-300'
            ]
        ];
    }

    public function render()
    {
        $attendanceHistory = Attendance::with([
            'employee:id,name,designation_id,employee_id',
            'employee.designation:id,name,department_id',
            'employee.designation.department:id,name'
        ])
            ->when(
                $this->employeeId,
                fn($q) => $q->where('employee_id', $this->employeeId)
            )
            ->when(
                $this->startDate && $this->endDate,
                fn($q) => $q->whereBetween('date', [
                    Carbon::parse($this->startDate)->startOfDay(),
                    Carbon::parse($this->endDate)->endOfDay()
                ])
            )
            ->when(
                $this->statusFilter,
                fn($q) => $q->where('status', $this->statusFilter)
            )
            ->orderBy('date', 'desc')
            ->orderBy('check_in', 'desc')
            ->paginate(20);

        return view('livewire.admin.attendance.history', [
            'attendanceHistory' => $attendanceHistory,
            'summary' => $this->calculateSummary()
        ]);
    }

    private function calculateSummary()
    {
        $query = Attendance::query()
            ->when($this->employeeId, function ($query) {
                $query->where('employee_id', $this->employeeId);
            })
            ->when($this->startDate && $this->endDate, function ($query) {
                $query->whereBetween('date', [
                    Carbon::parse($this->startDate)->startOfDay(),
                    Carbon::parse($this->endDate)->endOfDay()
                ]);
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            });

        $data = $query->get();

        // Ensure all hours are positive
        $positiveHoursData = $data->map(function ($attendance) {
            $attendance->hours_worked = max(0, $attendance->hours_worked);
            $attendance->overtime_hours = max(0, $attendance->overtime_hours);
            return $attendance;
        });

        return [
            'total_records' => $positiveHoursData->count(),
            'present_days' => $positiveHoursData->where('status', 'present')->count(),
            'absent_days' => $positiveHoursData->where('status', 'absent')->count(),
            'late_days' => $positiveHoursData->where('status', 'late')->count(),
            'half_days' => $positiveHoursData->where('status', 'half_day')->count(),
            'total_hours' => round($positiveHoursData->sum('hours_worked'), 2),
            'overtime_hours' => round($positiveHoursData->sum('overtime_hours'), 2),
            'average_hours' => $positiveHoursData->count() > 0
                ? round($positiveHoursData->avg('hours_worked'), 2)
                : 0,
        ];
    }

    public function exportToCsv()
    {
        try {
            $this->validate();

            $records = Attendance::with([
                'employee',
                'employee.designation',
                'employee.designation.department'
            ])
                ->when(
                    $this->employeeId,
                    fn($q) => $q->where('employee_id', $this->employeeId)
                )
                ->when(
                    $this->startDate && $this->endDate,
                    fn($q) => $q->whereBetween('date', [
                        Carbon::parse($this->startDate)->startOfDay(),
                        Carbon::parse($this->endDate)->endOfDay()
                    ])
                )
                ->when(
                    $this->statusFilter,
                    fn($q) => $q->where('status', $this->statusFilter)
                )
                ->orderBy('date', 'desc')
                ->get();

            $fileName = 'attendance-history-' . now()->format('Y-m-d-H-i-s') . '.csv';

            return response()->streamDownload(function () use ($records) {
                $file = fopen('php://output', 'w');
                fwrite($file, "\xEF\xBB\xBF"); // UTF-8 BOM

                fputcsv($file, [
                    'Date',
                    'Employee ID',
                    'Employee Name',
                    'Designation',
                    'Department',
                    'Check In',
                    'Check Out',
                    'Hours Worked',
                    'OT Hours',
                    'Status',
                    'Late?'
                ]);

                foreach ($records as $a) {
                    fputcsv($file, [
                        $a->date?->format('Y-m-d'),
                        $a->employee->employee_id ?? 'N/A',
                        $a->employee->name ?? 'N/A',
                        $a->employee->designation->name ?? 'N/A',
                        $a->employee->designation->department->name ?? 'N/A',
                        $a->check_in?->format('H:i:s') ?? 'N/A',
                        $a->check_out?->format('H:i:s') ?? 'N/A',
                        $a->hours_worked,
                        $a->overtime_hours,
                        ucfirst($a->status),
                        $a->is_late ? 'Yes' : 'No'
                    ]);
                }

                fclose($file);
            }, $fileName);
        } catch (\Throwable $e) {
            session()->flash('error', 'CSV export failed: ' . $e->getMessage());
        }
    }
}