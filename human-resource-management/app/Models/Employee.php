<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Employee extends Model
{
    protected $fillable =[
        'name',
        'email',
        'phone',
        'designation_id',
        'address'
    ];

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


}
