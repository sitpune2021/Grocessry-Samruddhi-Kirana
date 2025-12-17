<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductBatch extends Model
{
    use SoftDeletes;
    
     protected $fillable = [
        'category_id',
        'product_id',
        'batch_no',
        'expiry_date',
        'mfg_date',
        'quantity'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function movements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

}



