<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo',
        'status'
    ];

    public function categories()
    {
        return $this->hasMany(Category::class, 'brand_id');
    }
}
