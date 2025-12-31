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
        'customer_id ',
        'quantity',
        'reason',
        'status',
        'qc_status',
        'received_at'
    ];

   public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function order()
    {
        return $this->belongsTo(CustomerOrder::class, 'order_id');
    }

    public function orderItem()
    {
        return $this->belongsTo(CustomerOrderItem::class, 'order_item_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
