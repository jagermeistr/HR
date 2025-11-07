<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payroll extends Model
{
    public function company(): BelongsTo
    {
        return $this->belongsTo(related: Company::class);
    }

    public function salaries(): HasMany
    {
        return $this->hasMany(related: Salary::class);
    }

    

    public function payments(): HasMany
    {
        return $this->hasMany(related: Payment::class);
    }

    public function getMonthYearAttribute(): string
    {
        return $this->year . '-' . $this->month;
    }

    public function getMonthStringAttribute(): string
    {
        return Carbon::parse(time: $this->month_year)->format(format: 'F Y');
    }

    public function scopeInCompany($query): mixed
    {
        return $query->where('company_id', $this->company_id);
    }
}
