<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerOrderItem extends Model
{
    protected $table = 'customer_order_items';

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'total',
        'customer_otp'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
