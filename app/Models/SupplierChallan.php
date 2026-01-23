<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierChallan extends Model
{
    protected $fillable = [
        'challan_no',
        'purchase_order_id',
        'supplier_id',
        'warehouse_id',
        'challan_date',
        'status',
        'created_by'

    ];
    public function items()
    {
        return $this->hasMany(SupplierChallanItem::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

  
}
