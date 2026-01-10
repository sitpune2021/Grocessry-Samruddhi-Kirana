<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $table = 'addresses';

    protected $fillable = [
        'user_id',
        'name',
        'mobile',
        'address_line',
        'landmark',
        'city',
        'state',
        'pincode',
        'latitude',
        'longitude',
        'is_default'
    ];

    //  Address belongs to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
