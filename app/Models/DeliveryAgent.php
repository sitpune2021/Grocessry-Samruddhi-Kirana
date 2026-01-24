<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class DeliveryAgent extends Model
{
    use HasApiTokens;

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
        'created_by',
        'name',
        'mobile',
        'email',
        'password',
        'otp',
        'otp_token',
        'otp_expiry',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function shop()
    {
        return $this->belongsTo(GroceryShop::class, 'shop_id');
       
    }


}
