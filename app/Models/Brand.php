<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = [
        'category_id',
        'sub_category_id',
        'name',
        'slug',
        'description',
        'logo',
        'status'
    ];

    public function subCategories()
    {
        return $this->belongsTo(SubCategory::class);
    }
}
