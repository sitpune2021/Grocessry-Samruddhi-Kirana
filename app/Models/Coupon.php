<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    
    use SoftDeletes;
    protected $fillable = [
        'code',
        'type',
        'value',
        'min_cart_amount',
        'start_date',
        'end_date',
        'usage_limit',
        'per_user_limit',
        'is_active'
        
    ];
}
