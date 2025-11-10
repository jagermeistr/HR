<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workers extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'salary',
        'mpesa_receipt_number',
        'payment_status',
        'payment_response',
        'last_payment_date'
    ];

    protected $casts = [
        'salary' => 'decimal:2',
        'last_payment_date' => 'datetime',
        'payment_response' => 'array'
    ];
}