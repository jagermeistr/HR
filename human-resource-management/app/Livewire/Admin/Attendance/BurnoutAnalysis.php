<?php

namespace App\Livewire\Admin\Attendance;

use Livewire\Component;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\CompanySetting;
use Carbon\Carbon;

class BurnoutAnalysis extends Component
{
    public $timeRange = 'current_week'; // current_week, last_week, last_month

    public function render()
    {
        $settings = CompanySetting::first();
        $burnoutThreshold = $settings->burnout_threshold ?? 48;

        // Get date range based on selection
        $dateRange = $this->getDateRange();

        // Comprehensive analysis data
        $analysisData = $this->getBurnoutAnalysis($dateRange, $burnoutThreshold);

        return view('livewire.admin.attendance.burnout-analysis', $analysisData);
    }

    private function getDateRange()
    {
        switch ($this->timeRange) {
            case 'last_week':
                return [
                    'start' => Carbon::now()->subWeek()->startOfWeek(),
                    'end' => Carbon::now()->subWeek()->endOfWeek(),
                    'label' => 'Last Week'
                ];
            case 'last_month':
                return [
                    'start' => Carbon::now()->subMonth()->startOfMonth(),
                    'end' => Carbon::now()->subMonth()->endOfMonth(),
                    'label' => 'Last Month'
                ];
            default:
                return [
                    'start' => Carbon::now()->startOfWeek(),
                    'end' => Carbon::now()->endOfWeek(),
                    'label' => 'Current Week'
                ];
        }
    }

    private function getBurnoutAnalysis($dateRange, $burnoutThreshold)
    {
        $employees = Employee::with(['designation.department', 'attendances' => function($query) use ($dateRange) {
            $query->whereBetween('date', [$dateRange['start'], $dateRange['end']]);
        }])->get();

        $analysis = [
            'criticalCount' => 0,
            'highRiskCount' => 0,
            'moderateRiskCount' => 0,
            'safeCount' => 0,
            'highRiskEmployees' => [],
            'riskDistribution' => [],
            'departmentRisks' => [],
            'weeklyPatterns' => [],
            'interventions' => $this->getInterventions(),
            'actionPlan' => $this->getActionPlan()
        ];

        foreach ($employees as $employee) {
            $weeklyHours = $employee->attendances->sum('hours_worked');
            $dailyAverage = $weeklyHours / 5; // Assuming 5-day work week
            $overtimeHours = max(0, $weeklyHours - ($burnoutThreshold - 10)); // OT over 38h for example

            $riskData = $this->calculateRiskLevel($weeklyHours, $burnoutThreshold, $dailyAverage, $overtimeHours);

            // Count risk levels
            switch ($riskData['level']) {
                case 'Critical': $analysis['criticalCount']++; break;
                case 'High': $analysis['highRiskCount']++; break;
                case 'Moderate': $analysis['moderateRiskCount']++; break;
                default: $analysis['safeCount']++; break;
            }

            // Add to high risk list if applicable
            if (in_array($riskData['level'], ['Critical', 'High'])) {
                $analysis['highRiskEmployees'][] = [
                    'name' => $employee->name,
                    'designation' => $employee->designation->name ?? 'No Designation',
                    'department' => $employee->designation->department->name ?? 'No Department',
                    'weekly_hours' => round($weeklyHours, 1),
                    'daily_avg' => round($dailyAverage, 1),
                    'overtime_hours' => round($overtimeHours, 1),
                    'risk_level' => $riskData['level'],
                    'risk_badge_color' => $riskData['badge_color'],
                    'hours_color' => $riskData['hours_color'],
                    'recommendation' => $riskData['recommendation']
                ];
            }
        }

        // Sort high risk employees by hours (highest first)
        usort($analysis['highRiskEmployees'], fn($a, $b) => $b['weekly_hours'] <=> $a['weekly_hours']);

        // Calculate risk distribution
        $totalEmployees = $employees->count();
        $analysis['riskDistribution'] = [
            ['level' => 'Critical', 'count' => $analysis['criticalCount'], 'percentage' => round(($analysis['criticalCount']/$totalEmployees)*100, 1), 'color' => 'bg-red-500'],
            ['level' => 'High', 'count' => $analysis['highRiskCount'], 'percentage' => round(($analysis['highRiskCount']/$totalEmployees)*100, 1), 'color' => 'bg-orange-500'],
            ['level' => 'Moderate', 'count' => $analysis['moderateRiskCount'], 'percentage' => round(($analysis['moderateRiskCount']/$totalEmployees)*100, 1), 'color' => 'bg-yellow-500'],
            ['level' => 'Safe', 'count' => $analysis['safeCount'], 'percentage' => round(($analysis['safeCount']/$totalEmployees)*100, 1), 'color' => 'bg-green-500'],
        ];

        // Add department analysis and weekly patterns
        $analysis['departmentRisks'] = $this->getDepartmentAnalysis($employees, $burnoutThreshold);
        $analysis['weeklyPatterns'] = $this->getWeeklyPatterns($analysis['highRiskEmployees']);

        return $analysis;
    }

    private function calculateRiskLevel($weeklyHours, $threshold, $dailyAverage, $overtimeHours)
    {
        if ($weeklyHours > 60) {
            return [
                'level' => 'Critical',
                'badge_color' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                'hours_color' => 'text-red-600 dark:text-red-400',
                'recommendation' => 'Immediate workload reduction required'
            ];
        } elseif ($weeklyHours > $threshold + 12) {
            return [
                'level' => 'High',
                'badge_color' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
                'hours_color' => 'text-orange-600 dark:text-orange-400',
                'recommendation' => 'Urgent review of workload and deadlines'
            ];
        } elseif ($weeklyHours > $threshold) {
            return [
                'level' => 'Moderate',
                'badge_color' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                'hours_color' => 'text-yellow-600 dark:text-yellow-400',
                'recommendation' => 'Monitor closely and consider task redistribution'
            ];
        } else {
            return [
                'level' => 'Safe',
                'badge_color' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                'hours_color' => 'text-green-600 dark:text-green-400',
                'recommendation' => 'Maintain current workload balance'
            ];
        }
    }

    private function getDepartmentAnalysis($employees, $threshold)
    {
        // Group by department and calculate metrics
        $departments = [];
        
        foreach ($employees as $employee) {
            $deptName = $employee->designation->department->name ?? 'No Department';
            $weeklyHours = $employee->attendances->sum('hours_worked');
            
            if (!isset($departments[$deptName])) {
                $departments[$deptName] = [
                    'total_hours' => 0,
                    'employee_count' => 0,
                    'at_risk_count' => 0
                ];
            }
            
            $departments[$deptName]['total_hours'] += $weeklyHours;
            $departments[$deptName]['employee_count']++;
            
            if ($weeklyHours > $threshold) {
                $departments[$deptName]['at_risk_count']++;
            }
        }

        $result = [];
        foreach ($departments as $deptName => $data) {
            $avgHours = $data['employee_count'] > 0 ? $data['total_hours'] / $data['employee_count'] : 0;
            $riskPercentage = $data['employee_count'] > 0 ? ($data['at_risk_count'] / $data['employee_count']) * 100 : 0;
            
            if ($riskPercentage > 50) {
                $riskLevel = 'High Risk';
                $riskColor = 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
            } elseif ($riskPercentage > 25) {
                $riskLevel = 'Medium Risk';
                $riskColor = 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300';
            } else {
                $riskLevel = 'Low Risk';
                $riskColor = 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
            }

            $result[] = [
                'department' => $deptName,
                'avg_hours' => round($avgHours, 1),
                'total_employees' => $data['employee_count'],
                'at_risk_count' => $data['at_risk_count'],
                'risk_level' => $riskLevel,
                'risk_color' => $riskColor
            ];
        }

        return $result;
    }

    private function getWeeklyPatterns($highRiskEmployees)
    {
        return [
            [
                'title' => 'Consistent Overtime',
                'description' => 'Employees working 10+ hours daily throughout the week',
                'employee_count' => count(array_filter($highRiskEmployees, fn($emp) => $emp['daily_avg'] >= 10)),
                'border_color' => 'border-red-400',
                'badge_color' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
            ],
            [
                'title' => 'Weekend Work Patterns',
                'description' => 'Significant hours logged on weekends',
                'employee_count' => '5', // You would calculate this from actual data
                'border_color' => 'border-orange-400',
                'badge_color' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300'
            ],
            [
                'title' => 'Late Night Work',
                'description' => 'Regular check-outs after 8 PM',
                'employee_count' => '8', // You would calculate this from actual data
                'border_color' => 'border-yellow-400',
                'badge_color' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300'
            ]
        ];
    }

    private function getInterventions()
    {
        return [
            [
                'title' => 'Workload Redistribution',
                'description' => 'Identify tasks that can be delegated or automated',
                'impact' => 'High impact on burnout reduction'
            ],
            [
                'title' => 'Flexible Scheduling',
                'description' => 'Implement compressed work weeks or flexible hours',
                'impact' => 'Medium impact, improves work-life balance'
            ],
            [
                'title' => 'Temporary Support',
                'description' => 'Provide temporary staff for peak periods',
                'impact' => 'Quick relief for critical situations'
            ],
            [
                'title' => 'Process Optimization',
                'description' => 'Streamline workflows to reduce time-consuming tasks',
                'impact' => 'Long-term sustainable improvement'
            ]
        ];
    }

    private function getActionPlan()
    {
        return [
            [
                'timeframe' => 'This Week',
                'title' => 'Immediate Interventions',
                'icon_bg' => 'bg-red-100 dark:bg-red-900/30',
                'icon_color' => 'text-red-600 dark:text-red-400',
                'text_color' => 'text-red-700 dark:text-red-300',
                'icon_path' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                'tasks' => [
                    'Meet with highest risk employees',
                    'Implement temporary task redistribution',
                    'Communicate available support resources'
                ]
            ],
            [
                'timeframe' => 'Next 2 Weeks',
                'title' => 'Structural Changes',
                'icon_bg' => 'bg-orange-100 dark:bg-orange-900/30',
                'icon_color' => 'text-orange-600 dark:text-orange-400',
                'text_color' => 'text-orange-700 dark:text-orange-300',
                'icon_path' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                'tasks' => [
                    'Review and adjust project timelines',
                    'Implement department-level workload monitoring',
                    'Train managers on burnout prevention'
                ]
            ],
            [
                'timeframe' => 'This Month',
                'title' => 'Long-term Strategy',
                'icon_bg' => 'bg-green-100 dark:bg-green-900/30',
                'icon_color' => 'text-green-600 dark:text-green-400',
                'text_color' => 'text-green-700 dark:text-green-300',
                'icon_path' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                'tasks' => [
                    'Develop sustainable workload policies',
                    'Implement automated burnout detection',
                    'Create career development pathways'
                ]
            ]
        ];
    }
}