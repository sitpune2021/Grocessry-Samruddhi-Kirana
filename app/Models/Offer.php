<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'category_id',
        'product_id',
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
        'offer_id',
        'coupon_code',
        'coupon_discount',
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
        return $query->where('status', true)
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now());
    }
}
