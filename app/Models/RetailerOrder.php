<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RetailerOrder extends Model
{
    use HasFactory;

    protected $table = 'retailer_orders';

    protected $fillable = [
        'order_no',
        'retailer_id',
        'warehouse_id',
        'status',
        'total_amount',
    ];

    /* ================= RELATIONSHIPS ================= */

    public function retailer()
    {
        return $this->belongsTo(Retailer::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items()
    {
        return $this->hasMany(RetailerOrderItem::class, 'retailer_order_id');
    }

    /* ================= SCOPES ================= */

    // Active / Pending orders
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /* ================= HELPERS ================= */

    // Total quantity of items
    public function totalQuantity()
    {
        return $this->items()->sum('quantity');
    }

    // Recalculate total amount (safe)
    public function recalculateTotal()
    {
        $total = $this->items()->sum('total');

        $this->update([
            'total_amount' => $total
        ]);

        return $total;
    }
}
