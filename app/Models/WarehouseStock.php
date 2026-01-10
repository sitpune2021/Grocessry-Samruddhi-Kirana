<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;


class WarehouseStock extends Model
{
    use HasFactory;

    use SoftDeletes;
    protected $table = 'warehouse_stock';

    protected $fillable = [
        'warehouse_id',
        'category_id',
        'sub_category_id',
        'product_id',
        'batch_id',
        'quantity',
        'supplier_id',
        'bill_no',
        'challan_no',
        'batch_no',

    ];

    public function batch()
    {
        return $this->belongsTo(ProductBatch::class, 'batch_no');
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

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
