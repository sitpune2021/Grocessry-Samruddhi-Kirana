<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseTransferItem extends Model
{
    protected $fillable = [
        'warehouse_transfer_request_id',
        'product_id',
        'requested_qty',
        'approved_qty'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

