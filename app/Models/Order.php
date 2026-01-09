<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'subtotal',
        'delivery_charge',
        'discount',
        'total_amount',
        'status',
        'delivery_agent_id',
        'pickup_proof',
        'coupon_code',
        'order_number',
        'coupon_discount',
        'cancel_reason',
        'cancel_comment',
        'cancelled_at',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
