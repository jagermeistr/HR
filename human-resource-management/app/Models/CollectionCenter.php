<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CollectionCenter extends Model
{
    protected $fillable = [
        'name',
        'location',
        'manager_name',
        'contact',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function production_records(): HasMany
    {
        return $this->hasMany(ProductionRecord::class);
    }
}
