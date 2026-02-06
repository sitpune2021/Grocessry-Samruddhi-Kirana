<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
         
        'area',
        'flat_house',
        'floor',
        'city',
      
        'postcode',
        'landmark',     
        'is_default',
        'phone',
       
        'latitude',
        'longitude',
        'type',
    ];
}
