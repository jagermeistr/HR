<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Services\NetPayCalculationsService;

class Salary extends Model
{
    protected $fillable = ['payroll_id', 'employee_id', 'gross_salary'];

    public function payroll(): BelongsTo
    {
        return $this->belongsTo(Payroll::class); // Remove "related:"
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class); // Remove "related:"
    }

    // Additional accessors for individual deductions
    public function getNssfAttribute(): float
    {
        return $this->breakdown->getNssfDeduction();
    }

    public function getBreakdownAttribute(): NetPayCalculationsService
    {
        return new NetPayCalculationsService(gross_salary: $this->gross_salary);
    }

    public function getDeductionsAttribute(): mixed
    {
        return $this->breakdown->getDeductions();
    }

    public function getNetPayAttribute()
    {
        return $this->breakdown->getNetPay();
    }
}