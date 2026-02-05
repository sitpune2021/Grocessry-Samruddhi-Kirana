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
        'area',
        'flat_house',
        'floor',
        'city',
        'country',
        'postcode',
        'landmark',     
        'is_default',
        'phone',
        'email',
        'latitude',
        'longitude',
        'type',
    ];
}
