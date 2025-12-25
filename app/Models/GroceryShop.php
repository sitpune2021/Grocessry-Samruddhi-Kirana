<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/GroceryShop.php
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
}
