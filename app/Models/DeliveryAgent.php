<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryAgent extends Model
{
    protected $fillable = [
        'user_id',
        'shop_id',
        'address',
        'status',
        'dob',
        'gender',
        'address',
        'profile_image',
        'aadhaar_card',
        'driving_license',
        'created_by'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function shop()
    {
        return $this->belongsTo(GroceryShop::class, 'shop_id');
        // If table is `shops`, then:
        // return $this->belongsTo(Shop::class, 'shop_id');
    }
}
