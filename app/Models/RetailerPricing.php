<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RetailerPricing extends Model
{
    protected $fillable = [
        'retailer_id',
        'warehouse_id',
        'product_id',
        'category_id',
        'base_price',
        'discount_percent',
        'discount_amount',
        'effective_price',
        'effective_from',
        'effective_to',
        'is_active',
    ];

    public function retailer()
    {
        return $this->belongsTo(Retailer::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}