<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RetailerCart extends Model
{
    use HasFactory;

    protected $table = 'retailer_carts';

    protected $fillable = [
        'retailer_id',
        'warehouse_id',
        'status',
    ];

    protected $casts = [
        'retailer_id'  => 'integer',
        'warehouse_id' => 'integer',
    ];


    /*
    |--------------------------------------------------------------------------
    | Retailer
    |--------------------------------------------------------------------------
    */

    public function retailer()
    {
        return $this->belongsTo(
            Retailer::class,
            'retailer_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Distribution Center
    |--------------------------------------------------------------------------
    */

    public function warehouse()
    {
        return $this->belongsTo(
            Warehouse::class,
            'warehouse_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Cart Items
    |--------------------------------------------------------------------------
    */

    public function items()
    {
        return $this->hasMany(
            RetailerCartItem::class,
            'cart_id'
        );
    }

    
}