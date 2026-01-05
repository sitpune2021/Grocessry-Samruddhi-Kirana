<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseTransferRequest extends Model
{
    protected $fillable = [
        'from_warehouse_id',
        'to_warehouse_id',
        'request_no',
        'request_date',
        'status'
    ];

    public function items()
    {
        return $this->hasMany(WarehouseTransferItem::class);
    }
}
