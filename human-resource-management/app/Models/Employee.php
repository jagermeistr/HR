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

    public function scopeSearchByName($query, $name): mixed
    {
        return $query->whereas('name', 'like', '%' . $name . '%');
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
        return $this->contracts()->where(column: 'start_date', operator: '<=', value: $start_date)->where(column: 'end_date', operator: '>=', value: $end_date)->first();
    }


}
