<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = ['cart_id', 'product_id', 'batch_id', 'qty', 'price', 'line_total'];

    public function product()
    {
        $dcId = session('dc_warehouse_id');

        return $this->belongsTo(Product::class)
            ->withStock($dcId);
    }

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }
}
