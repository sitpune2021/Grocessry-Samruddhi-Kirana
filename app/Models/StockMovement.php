<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
     protected $fillable = [
          'product_batch_id',
          'warehouse_id',
          'type',
          'quantity'
     ];

     public function warehouse()
     {
          return $this->belongsTo(Warehouse::class, 'warehouse_id');
     }

     // ✅ Batch relation
     public function batch()
     {
          return $this->belongsTo(ProductBatch::class, 'product_batch_id');
     }


     public function product()
     {
          return $this->hasOneThrough(
               Product::class,
               ProductBatch::class,
               'id',          // FK on product_batches
               'id',          // FK on products
               'product_batch_id',
               'product_id'
          );
     }

     public function category()
     {
          return $this->hasOneThrough(
               Category::class,
               Product::class,
               'id',
               'id',
               'product_id',
               'category_id'
          );
     }
}
