<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payroll extends Model
{

    use HasFactory;

    protected $fillable = [
        'id',
        'company_id',
        'year',
        'month',
        'created_at',
        'updated_at',
        // Add only the columns that actually exist in your table
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'year' => 'integer',
        'month' => 'integer'
    ];

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

    public function scopeInCompany($query, $companyId = null): mixed
    {
        $companyId = $companyId ?? session('company_id');
        return $query->where('company_id', $companyId);
    }
}
