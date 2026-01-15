<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'address',
        'city',
        'country',
        'postcode',
        'landmark',     
        'is_default',
        'phone',
        'email',
        'latitude',
        'longitude',
    ];
}
