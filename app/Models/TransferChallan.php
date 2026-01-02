<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferChallan extends Model
{
    protected $fillable = [
        'challan_no',
        'from_warehouse_id',
        'to_warehouse_id',
        'transfer_date',
        'status',
        'created_by'
    ];

    public function items()
    {
        return $this->hasMany(TransferChallanItem::class, 'transfer_challan_id');
    }

    public function fromWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    public function toWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }
}
