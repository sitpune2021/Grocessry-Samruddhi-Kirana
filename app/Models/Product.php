<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
    protected $appends = ['product_image_urls'];

    protected $fillable = [
        'category_id',
        'sub_category_id',
        'tax_id',
        'brand_id',
        'name',
        'sku',
        'description',
        'effective_date',
        'expiry_date',
        'base_price',
        'retailer_price',
        'mrp',
        'gst_percentage',
        'stock',
        'product_images',
        'discount_type',
        'discount_value',
        'warehouse_id',
    ];

    protected $casts = [
        'product_images' => 'array',
    ];


    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function batches()
    {
        return $this->hasMany(ProductBatch::class);
    }

    public function warehouseStocks()
    {
        return $this->hasMany(WarehouseStock::class);
    }
    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }
    public function getProductImageUrlsAttribute()
    {
        if (!$this->product_images) {
            return [];
        }

        $images = json_decode($this->product_images, true);

        return collect($images)->map(function ($image) {
            return asset('storage/products/' . $image);
        })->values();
    }
}
