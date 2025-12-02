<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class DistrictWarehouse extends Model
{
    protected $fillable = ['master_warehouse_id', 'name', 'location'];

    public function master()
    {
        return $this->belongsTo(MasterWarehouse::class);
    }

    public function talukaWarehouses()
    {
        return $this->hasMany(TalukaWarehouse::class);
    }
}
