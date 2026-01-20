<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierChallanItem extends Model
{
    protected $fillable = [
        'supplier_challan_id',
        'ordered_qty',
        'product_id',
        'ordered_qty',
        'challan_date',
        'received_qty',
        'rate'
    ];
}
