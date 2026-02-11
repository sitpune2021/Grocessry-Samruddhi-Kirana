<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'flat_house',
        'floor',
        'area',
        'landmark',
        'city',
        'postcode',
        'phone',
        'latitude',
        'longitude',
        'type',
        'is_default',
    ];
}
