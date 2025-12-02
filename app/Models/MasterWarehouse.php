<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class MasterWarehouse extends Model
{
    protected $fillable = ['name', 'location'];

    public function districtWarehouses()
    {
        return $this->hasMany(DistrictWarehouse::class);
    }
}
