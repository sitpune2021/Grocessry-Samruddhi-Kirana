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
        'order_type',
        'subtotal',
        'delivery_charge',
        'created_by',
        'discount',
        'total_amount',
        'channel',
        'warehouse_id',
        'status',
        'payment_method',
        'payment_status',
        'delivery_agent_id',
        'pickup_proof',
        'coupon_code',
        'coupon_discount',
        'cancel_reason',
        'cancel_comment',
        'cancelled_at',
        'customer_rating',
        'customer_rating_tags',
        'delivered_at',

    ];
    protected $casts = [
        'customer_rating_tags' => 'array',
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
    // public function customerAddress()
    // {
    //     return $this->hasOne(UserAddress::class, 'user_id', 'user_id');
    // }
    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function deliveryAddress()
    {
        return $this->hasOne(UserAddress::class, 'user_id', 'user_id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
