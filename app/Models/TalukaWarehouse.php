<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TalukaWarehouse extends Model
{
    protected $fillable = ['district_warehouse_id', 'name', 'location'];

    public function district()
    {
        return $this->belongsTo(DistrictWarehouse::class);
    }

     
}
