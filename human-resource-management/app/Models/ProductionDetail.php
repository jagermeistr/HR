<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionDetail extends Model
{
    protected $fillable = [
        'production_record_id',
        'farmer_id',
        'kgs_supplied',
    ];

    public function production(): BelongsTo
    {
        return $this->belongsTo(ProductionRecord::class);
    }

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(Farmer::class);
    }
}