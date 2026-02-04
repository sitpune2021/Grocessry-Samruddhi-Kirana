<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerProductReturn extends Model
{
    protected $fillable = [
        'customer_id',
        'order_id',
        'order_item_id',
        'product_id',
        'warehouse_id',
        'delivery_agent_id',
        'quantity',
        'reason',
        'comment',
        'status',
        'picked_at',
        'return_started_at',
        'returned_at',
        'start_latitude',
        'start_longitude',
    ];
    // CustomerProductReturn.php

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
