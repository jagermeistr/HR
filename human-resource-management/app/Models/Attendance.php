<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use App\Models\CompanySetting;

class Attendance extends Model
{
    protected $fillable = [
        'employee_id',
        'date',
        'check_in',
        'check_out',
        'status',
        'hours_worked',
        'overtime_hours',
        'overtime',
        'is_late',
        'is_burnout_risk',
        'weekly_hours',
        'burnout_level'
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'overtime' => 'boolean',
        'is_late' => 'boolean',
        'is_burnout_risk' => 'boolean',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Check if employee arrived late
     */
    public function checkLateArrival()
    {
        if (!$this->check_in) {
            $this->is_late = false;
            return false;
        }

        try {
            $settings = CompanySetting::first();
            $lateThreshold = $settings->late_threshold ?? '09:15:00';

            $checkInTime = Carbon::parse($this->check_in);

            if ($lateThreshold instanceof Carbon) {
                $thresholdTime = $lateThreshold;
            } else {
                $thresholdTime = Carbon::parse($this->date->format('Y-m-d') . ' ' . $lateThreshold);
            }

            $isLate = $checkInTime->gt($thresholdTime);
            $this->is_late = $isLate;

            return $isLate;
        } catch (\Exception $e) {
            $defaultThreshold = Carbon::parse($this->date->format('Y-m-d') . ' 09:15:00');
            $isLate = Carbon::parse($this->check_in)->gt($defaultThreshold);
            $this->is_late = $isLate;

            return $isLate;
        }
    }

    /**
     * Calculate hours worked and overtime
     */
    public function calculateHoursWorked()
    {
        if (!$this->check_in || !$this->check_out) {
            $this->hours_worked = 0;
            $this->overtime_hours = 0;
            $this->overtime = false;
            return;
        }

        try {
            $checkIn = Carbon::parse($this->check_in);
            $checkOut = Carbon::parse($this->check_out);

            if ($checkOut->lte($checkIn) || $checkOut->diffInHours($checkIn) > 24) {
                $this->hours_worked = 0;
                $this->overtime_hours = 0;
                $this->overtime = false;
                return;
            }

            $totalMinutes = $checkOut->diffInMinutes($checkIn);
            $hoursWorked = max(0, min(24, $totalMinutes / 60));

            $settings = CompanySetting::first();
            $regularHours = $settings->regular_hours ?? 8;
            $overtimeHours = max(0, $hoursWorked - $regularHours);

            $this->hours_worked = round($hoursWorked, 2);
            $this->overtime_hours = round($overtimeHours, 2);
            $this->overtime = $overtimeHours > 0;
        } catch (\Exception $e) {
            $this->hours_worked = 0;
            $this->overtime_hours = 0;
            $this->overtime = false;
        }
    }

    /**
     * Calculate weekly hours for this employee
     */
    public function getWeeklyHours()
    {
        $startOfWeek = Carbon::parse($this->date)->startOfWeek();
        $endOfWeek = Carbon::parse($this->date)->endOfWeek();

        return (float) self::where('employee_id', $this->employee_id)
            ->whereBetween('date', [$startOfWeek, $endOfWeek])
            ->where('hours_worked', '>', 0)
            ->sum('hours_worked');
    }

    /**
     * Check if employee is at burnout risk for the week
     */
    public function isBurnoutRisk()
    {
        $weeklyHours = $this->getWeeklyHours();
        $settings = CompanySetting::first();
        $burnoutThreshold = $settings->burnout_threshold ?? 48;

        return $weeklyHours > $burnoutThreshold;
    }

    public function updateBurnoutStatus()
    {
        $weeklyHours = $this->getWeeklyHours();
        $settings = CompanySetting::first();
        $burnoutThreshold = $settings->burnout_threshold ?? 48;

        $isBurnoutRisk = $weeklyHours > $burnoutThreshold;

        // Determine burnout level
        $burnoutLevel = 'Safe';
        if ($weeklyHours > $burnoutThreshold + 20) {
            $burnoutLevel = 'Critical';
        } elseif ($weeklyHours > $burnoutThreshold + 10) {
            $burnoutLevel = 'High Risk';
        } elseif ($weeklyHours > $burnoutThreshold) {
            $burnoutLevel = 'At Risk';
        }

        // Update current and all attendance records for this week
        self::where('employee_id', $this->employee_id)
            ->whereBetween('date', [
                Carbon::parse($this->date)->startOfWeek(),
                Carbon::parse($this->date)->endOfWeek()
            ])
            ->update([
                'is_burnout_risk' => $isBurnoutRisk,
                'weekly_hours' => $weeklyHours,
                'burnout_level' => $burnoutLevel
            ]);

        return $isBurnoutRisk;
    }

    /**
     * Get burnout status with details
     */
    public function burnoutStatus()
    {
        $settings = CompanySetting::first();
        $threshold = $settings->burnout_threshold ?? 48;
        
        return [
            'hours' => $this->weekly_hours,
            'level' => $this->burnout_level,
            'threshold' => $threshold,
            'over_by' => max(0, $this->weekly_hours - $threshold)
        ];
    }

    /**
     * Automatically calculate everything when check-in/check-out is set
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($attendance) {
            if ($attendance->check_in && $attendance->check_out) {
                $attendance->calculateHoursWorked();
                $attendance->checkLateArrival();
            }
        });

        // Update burnout status when attendance is saved
        static::saved(function ($attendance) {
            if ($attendance->check_in && $attendance->check_out) {
                $attendance->updateBurnoutStatus();
            }
        });
    }
}