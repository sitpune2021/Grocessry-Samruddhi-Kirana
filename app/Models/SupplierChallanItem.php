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
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function challan()
    {
        return $this->belongsTo(SupplierChallan::class, 'supplier_challan_id');
    }
}
