<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
     protected $fillable = ['product_batch_id', 'warehouse_id', 'type', 'quantity'];

}
