<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'payroll_id', // Keep this but it can be null
        'amount',
        'payment_method',
        'payment_date',
        'transaction_id',
        'mpesa_receipt_number',
        'mpesa_response',
        'payment_status'
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'mpesa_response' => 'array',
        'amount' => 'decimal:2'
    ];

    // Set default values
    protected $attributes = [
        'payroll_id' => null, // Default to null
        'payment_status' => 'pending'
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function payroll(): BelongsTo
    {
        return $this->belongsTo(Payroll::class);
    }
}