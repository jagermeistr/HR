<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CollectionCenter;
use App\Models\ProductionDetail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ProductionRecord extends Model
{
    protected $fillable = [
        'company_id',
        'collection_center_id',
        'total_kgs',
        'production_date',
    ];

    public function collection_center(): BelongsTo
    {
        return $this->belongsTo(CollectionCenter::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(ProductionDetail::class);
    }

    
}
