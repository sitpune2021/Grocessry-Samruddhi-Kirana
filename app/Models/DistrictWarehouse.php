<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class DistrictWarehouse extends Model
{
    protected $fillable =
    [
        'state_id',
        'name',
    ];

    public function master()
    {
        return $this->belongsTo(MasterWarehouse::class);
    }

    public function talukaWarehouses()
    {
        return $this->hasMany(TalukaWarehouse::class);
    }
}
