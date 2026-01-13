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
        return $this->belongsTo(WarehouseStockReturnItem::class, 'stock_return_id', 'id');
    }

    public function WarehouseStockReturnItem()
    {
        return $this->hasMany(WarehouseStockReturnItem::class, 'stock_return_id');
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

    /* ================= BUTTON LOGIC ================= */

    public function canApprove($warehouseId)
    {
        return $this->status === 'draft'
            && $this->to_warehouse_id === $warehouseId;
    }

    public function canDispatch($warehouseId)
    {
        return $this->status === 'approved'
            && $this->from_warehouse_id === $warehouseId;
    }

    public function canReceive($warehouseId)
    {
        return $this->status === 'dispatched'
            && $this->to_warehouse_id === $warehouseId;
    }
}
