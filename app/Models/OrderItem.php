<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{

    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_batch_id',
        'quantity',
        'is_picked',
        'price',
        'tax_percent',
        'tax_amount',
        'line_total',
        'total',
        'is_picked'
    ];
    protected $casts = [
        'is_picked' => 'boolean',
    ];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
