<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Batch extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'supplier_id',
        'batch_no',
        'expiry_date',
        'quantity',
        'purchase_price',
        'mrp',
        'status'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
