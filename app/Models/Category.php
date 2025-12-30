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
        'name',
        'slug',
        'brand_id'
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
    public function offers()
    {
        return $this->belongsToMany(Offer::class, 'offer_category');
    }
}
