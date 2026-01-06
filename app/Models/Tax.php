<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $fillable = [
        'name',
        'cgst',
        'sgst',
        'igst',
        'is_active',
    ];
    protected $casts = [
        'cgst' => 'float',
        'sgst' => 'float',
        'igst' => 'float',
        'is_active' => 'boolean',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
