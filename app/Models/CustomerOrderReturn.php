<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerOrderReturn extends Model
{
    protected $table = 'customer_order_returns';

    protected $fillable = [
        'order_id',
        'order_item_id',
        'product_id',
        'product_images',
        'customer_id ',
        'quantity',
        'reason',
        'status',
        'qc_status',
        'received_at'
    ];

    protected $casts = [
        'product_images' => 'array',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
