<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Farmer extends Model
{
    //
    public function productionDetails() {
    return $this->hasMany(ProductionDetail::class);
}

}
