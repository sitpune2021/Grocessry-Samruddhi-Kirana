<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerOrder extends Model
{
    protected $table = 'customer_orders';

    protected $fillable = [
        'customer_id',
        'order_number',
        'subtotal',
        'delivery_charge',
        'total_amount',
        'status',
        'address',
        'accepted_by'
    ];


    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function customerOrderItems()
    {
        return $this->hasMany(CustomerOrderItem::class, 'order_id');
    }
}
