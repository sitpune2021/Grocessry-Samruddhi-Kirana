<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferChallanItem extends Model
{
    
    protected $fillable = [
        'transfer_challan_id',
        'product_id',
        'quantity'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

}
