<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'employee_id',
        'date',
        'check_in',
        'check_out',
        'hours_worked',
        'status',
        'notes',
        'overtime'
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'overtime' => 'boolean',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    // Calculate hours worked automatically
    public function calculateHoursWorked(): float
    {
        if ($this->check_in && $this->check_out) {
            $checkIn = \Carbon\Carbon::parse($this->check_in);
            $checkOut = \Carbon\Carbon::parse($this->check_out);
            
            $hours = $checkOut->diffInMinutes($checkIn) / 60;
            $this->hours_worked = round($hours, 2);
            
            // Check for overtime (more than 8 hours)
            $this->overtime = $hours > 8;
            
            return $this->hours_worked;
        }
        
        return 0;
    }

    // Check if employee is at burnout risk (more than 40 hours/week)
    public function isBurnoutRisk(): bool
    {
        $weeklyHours = self::where('employee_id', $this->employee_id)
            ->whereBetween('date', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])
            ->sum('hours_worked');
            
        return $weeklyHours > 40;
    }
}