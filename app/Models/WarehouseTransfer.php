<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\GroceryShop;
class WarehouseTransfer extends Model
{
    protected $primaryKey = 'id';

    protected $fillable = [
        'from_warehouse_id',
        'to_warehouse_id',
        'category_id',
        'product_id',
        'batch_id',
        'quantity',
        'status'
    ];

    // app/Models/WarehouseTransfer.php

    public function fromWarehouse() {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    public function toWarehouse() {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }

    // public function batch() {
    //     return $this->belongsTo(ProductBatch::class, 'batch_id');
    // }

    public function batch()
    {
        return $this->belongsTo(ProductBatch::class, 'batch_id')
                    ->withTrashed(); // IMPORTANT
    }

    


}

