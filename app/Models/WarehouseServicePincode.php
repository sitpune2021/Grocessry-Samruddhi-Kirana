<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseServicePincode extends Model
{
    protected $fillable = ['warehouse_id', 'pincode'];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
