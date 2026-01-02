<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'warehouse_id',
        'supplier_id',
        'po_number',
        'po_date',
        'subtotal',
        'tax',
        'shipping_charge',
        'discount',
        'grand_total'
    ];

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }
}
