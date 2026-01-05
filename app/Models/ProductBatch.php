<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductBatch extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'warehouse_id',
        'category_id',
        'sub_category_id',
        'product_id',
        'batch_no',
        'expiry_date',
        'mfg_date',
        'quantity',
        'unit_id'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function movements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function stocks()
    {
        return $this->hasMany(WarehouseStock::class, 'batch_id');
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    // Warehouse relation
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
