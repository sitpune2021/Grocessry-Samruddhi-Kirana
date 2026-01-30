<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OnSaleProduct extends Model
{
    use HasFactory;

    protected $table = 'on_sale_products';

    protected $fillable = [
        'product_id',
        'product_batch_id',
        'warehouse_id',
        'mrp',
        'original_price',
        'sale_price',
        'discount_percent',
        'sale_start_date',
        'sale_end_date',
        'channel',
        'status',
    ];

    protected $casts = [
        'sale_start_date' => 'date',
        'sale_end_date'   => 'date',
        'original_price'  => 'decimal:2',
        'sale_price'      => 'decimal:2',
    ];

    /* ---------------- Relationships ---------------- */

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function batch()
    {
        return $this->belongsTo(ProductBatch::class, 'product_batch_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /* ---------------- Scopes ---------------- */

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->whereDate('sale_start_date', '<=', now())
            ->whereDate('sale_end_date', '>=', now());
    }

    public function scopeOnline($query)
    {
        return $query->where('channel', 'online');
    }
}
