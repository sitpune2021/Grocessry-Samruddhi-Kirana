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
        'customer_id',
        'quantity',
        'reason_id',
        'reason',
        'return_type',
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
    public function getProductImagesAttribute($value)
    {
        // ✅ Normalize value to array
        if (empty($value)) {
            return [];
        }

        // If value is JSON string, decode it
        if (is_string($value)) {
            $value = json_decode($value, true) ?? [];
        }

        // If still not array, force empty array
        if (!is_array($value)) {
            return [];
        }

        return array_map(function ($img) {
            return asset('storage/' . $img);
        }, $value);
    }
}
