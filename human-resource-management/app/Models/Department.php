<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    
    protected $fillable = [
        'company_id'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(related: company::class);
    }

    public function designations(): HasMany
    {
        return $this->hasMany(related: Designation::class);
    }

    public function employees(): mixed
    {
        return $this->throughDesignations()->hasEmployees();
    }

    public function scopeinCompany($query): mixed
    {
        return $query->where('company_id', session(key: 'company_id'));
    }

}
