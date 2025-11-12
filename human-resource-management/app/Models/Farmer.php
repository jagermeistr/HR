<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Farmer extends Model
{
    protected $fillable =[
        'name',
        'email',
        'phone',
        'address'
    ];
    //
    public function productionDetails() {
    return $this->hasMany(ProductionDetail::class);
}

public function scopeSearchByName($query, $name): mixed
    {
        return $query->where('name', 'like', '%' . $name . '%');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(related: Payment::class);
    }

}
