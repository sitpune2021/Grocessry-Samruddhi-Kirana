<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RetailerOrderItem extends Model
{
    use HasFactory;

    protected $table = 'retailer_order_items';

    protected $fillable = [
        'order_id',
        'product_id',
        'category_id',
        'quantity',
        'price',         // locked retailer price (effective_price)
        'total',         // price * quantity
    ];

    /* ================= RELATIONSHIPS ================= */

    public function order()
    {
        return $this->belongsTo(RetailerOrder::class, 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /* ================= MODEL EVENTS ================= */

    protected static function booted()
    {
        static::creating(function ($item) {
            $item->total = $item->price * $item->quantity;
        });

        static::updating(function ($item) {
            $item->total = $item->price * $item->quantity;
        });
    }
}
