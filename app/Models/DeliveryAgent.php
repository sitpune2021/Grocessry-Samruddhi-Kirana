<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryAgent extends Model
{
    protected $fillable = [
        'name',
        'mobile',
        'email',
        'address',
        'status',
        'created_by'
    ];
}
