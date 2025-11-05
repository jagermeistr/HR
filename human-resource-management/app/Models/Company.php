<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Company extends Model
{
    protected $fillable = [
        'name',
        'email',
        'logo',
        'website',

    ];

    public function users(): BelongstoMany
    {
        return $this->belongstoMany(related: User::class, table: 'company_User');
    }

    public function departments(): HasMany
    {
        return $this->hasMany(related: department::class);
    }

    public function designations(): mixed
    {
        return $this->throughDepartments()->hasDesignations();
    }

    public function getLogoUrlAttribute(): string
    {
        return $this->logo ? asset(path: 'storage/' . $this->logo): asset(path: 'images/default-logo.png');
    }

    public function scopeForUser($query): mixed
    {
        return $query->whereHas('users', function ($q) {
            $q->where('id', Auth::user()->id);
        });
    }
    
}
