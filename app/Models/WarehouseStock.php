<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class WarehouseStock extends Model
{
    use HasFactory;

    protected $table = 'warehouse_stock';

    protected $fillable = [
        'warehouse_id',
        'category_id',
        'product_id',
        'batch_id',
        'quantity',
    ];

    public function batch()
    {
        return $this->belongsTo(ProductBatch::class, 'batch_id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
