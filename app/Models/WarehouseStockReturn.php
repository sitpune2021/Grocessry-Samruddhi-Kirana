<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseStockReturn extends Model
{
    protected $fillable = [
        'return_number',
        'from_warehouse_id',
        'to_warehouse_id',
        'return_reason',
        'status',
        'created_by',
        'approved_by',
        'remarks',
        'dispatched_at',
        'received_at'
    ];

    public function items()
    {
        return $this->belongsTo(WarehouseStockReturn::class, 'stock_return_id','id');
    }

    public function fromWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    public function toWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
