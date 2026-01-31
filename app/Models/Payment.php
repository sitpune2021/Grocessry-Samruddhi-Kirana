<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';

    protected $fillable = [
        'order_id',
        'user_id',
        'payment_gateway',
        'payment_id',
        'attempt_no',
        'razorpay_order_id',
        'razorpay_signature',
        'amount',
        'status',
        'failure_reason',
        'meta'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'meta'   => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Payment belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
