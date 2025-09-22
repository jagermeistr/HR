<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Salary extends Model
{
    protected $fillable = ['payroll_id', 'employee_id', 'gross_salary'];

    public function payroll(): BelongsTo
    {
        return $this->belongsTo(related: Payroll::class);

    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(related: Employee::class);
    }
}
