<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'discount_type',
        'discount_value',
        'start_date',
        'end_date',
        'status',
        'category_id',
        'product_id',

    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'offer_product');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'offer_category');
    }

    public function scopeActive($query)
    {
        return $query->where('status', true)
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now());
    }
}
