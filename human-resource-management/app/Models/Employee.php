<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;


class Employee extends Model
{
    use Notifiable;

    protected $fillable =[
        'name',
        'email',
        'phone',
        'designation_id',
        'address'
    ];

    public function routeNotificationForMail()
    {
        return $this->email;
    }

    public function designation(): BelongsTo
    {
        return $this->belongsTo(related: Designation::class);
    }

    public function department(): mixed
    {
        return $this->designation->department;
    }

     // Rename this to avoid conflict with Eloquent expectations
    public function getDepartmentAttribute()
    {
        return $this->designation->department ?? null;
    }

    
    public function scopeInCompany($query):mixed
    {
        return $query->whereHas('designation', function ($q):void {
            $q->inCompany();
        });
    }

    public function scopeSearchByName($query, $name): mixed
    {
        return $query->where('name', 'like', '%' . $name . '%');
    }

    public function salaries(): HasMany
    {
        return $this->hasMany(related: Salary::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(related: Payment::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(related: Contract::class);
    }

    public function getActiveContract($start_date = null, $end_date = null): Contract|null
    {
        $start_date = $start_date ?? now();
        $end_date = $end_date ?? now();
        return $this->contracts()
            ->where('start_date', '<=', $end_date)
            ->where('end_date', '>=', $start_date)
            ->first();
    }

     public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    // Get weekly hours worked
    public function getWeeklyHoursAttribute(): float
    {
        return $this->attendances()
            ->whereBetween('date', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])
            ->sum('hours_worked');
    }

    // Check if employee is at burnout risk
    public function getIsBurnoutRiskAttribute(): bool
    {
        return $this->weekly_hours > 40;
    }

    // Get today's attendance
    public function getTodayAttendanceAttribute()
    {
        return $this->attendances()->whereDate('date', today())->first();
    }


}
