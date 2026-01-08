<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class GroceryShop extends Model
{
    protected $fillable = [
        'shop_name',
        'owner_name',
        'mobile_no',
        'address',
        'state_id',
        'district_id',
        'taluka_id',
        'pincode',
        'status',
    ];

    /* ========================
       Shop warehouse
    ======================== */
    public function warehouse()
    {
        return $this->hasOne(Warehouse::class, 'grocery_shop_id');
    }

    /* ========================
       Accessor: District Warehouse
    ======================== */
    public function getDistrictWarehouseAttribute()
    {
        return Warehouse::where('district_id', $this->district_id)
            ->whereNull('taluka_id')
            ->first();
    }

    /* ========================
       Accessor: Taluka Warehouse
    ======================== */
    public function getTalukaWarehouseAttribute()
    {
        return Warehouse::where('taluka_id', $this->taluka_id)
            ->whereNotNull('taluka_id')
            ->first();
    }
}

