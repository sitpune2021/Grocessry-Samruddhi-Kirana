<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'warehouse_id',
        'name',
        'slug',
        'category_images',
    ];

    protected $casts =[
        'category_images' => 'array',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    public function warehouseStocks()
    {
        return $this->hasMany(WarehouseStock::class);
    }
}
