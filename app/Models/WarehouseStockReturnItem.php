<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseStockReturnItem extends Model
{
    protected $fillable = [
        'stock_return_id',
        'product_id',
        'batch_no',
        'return_qty',
        'received_qty',
        'damaged_qty',
        'condition',
        'condition_image'
    ];

    public function stockReturn()
    {
        return $this->belongsTo(WarehouseStockReturn::class,'stock_return_id', 'id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }
}
