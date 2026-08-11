<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RetailerCartItem extends Model
{
    use HasFactory;

    protected $table = 'retailer_cart_items';

    protected $fillable = [
        'cart_id',
        'product_id',
        'category_id',
        'quantity',
        'price',
        'discount_amount',
        'total',
    ];

    protected $casts = [
        'cart_id'          => 'integer',
        'product_id'      => 'integer',
        'category_id'     => 'integer',
        'quantity'        => 'integer',
        'price'           => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total'            => 'decimal:2',
    ];


    /*
    |--------------------------------------------------------------------------
    | Cart
    |--------------------------------------------------------------------------
    */

    public function cart()
    {
        return $this->belongsTo(
            RetailerCart::class,
            'cart_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Product
    |--------------------------------------------------------------------------
    */

    public function product()
    {
        return $this->belongsTo(
            Product::class,
            'product_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Category
    |--------------------------------------------------------------------------
    */

    public function category()
    {
        return $this->belongsTo(
            Category::class,
            'category_id'
        );
    }

    
}