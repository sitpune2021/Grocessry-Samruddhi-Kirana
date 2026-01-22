<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\GroceryShop;
class WarehouseTransfer extends Model
{
    protected $primaryKey = 'id';

    protected $fillable = [
        'approved_by_warehouse_id',
        'requested_by_warehouse_id',
        'category_id',
        'product_id',
        'batch_id',
        'quantity',
        'status'
    ];

    // app/Models/WarehouseTransfer.php

    public function approvedByWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'approved_by_warehouse_id');
    }

    public function requestedByWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'requested_by_warehouse_id');
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function batch()
    {
        return $this->belongsTo(ProductBatch::class, 'batch_id')
                    ->withTrashed(); // IMPORTANT
    }

    public function challan()
    {
        return $this->belongsTo(TransferChallan::class, 'challan_id');
    }


}

