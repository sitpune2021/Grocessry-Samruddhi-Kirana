<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = [
        'name',
        'short_name',
        'status',
        'created_by'
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
    public function productBatches()
    {
        return $this->hasMany(ProductBatch::class);
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
