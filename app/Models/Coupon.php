<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    protected $fillable = [
    'code',
    'title',
    'description',
    'discount_type',
    'discount_value',
    'start_date',
    'end_date',
    'min_amount',
    'max_usage',
    'terms_condition',
    'status',
    'category_id',
    'product_id',
];


    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1)
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now());
    }
}
