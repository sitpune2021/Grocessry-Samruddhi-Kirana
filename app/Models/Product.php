<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'sku',
        'description',
        'base_price',
        'retailer_price',
        'mrp',
        'gst_percentage',
        'stock',
        'product_images'
    ];

    protected $casts = [
        'product_images' => 'array',
    ];

  
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
