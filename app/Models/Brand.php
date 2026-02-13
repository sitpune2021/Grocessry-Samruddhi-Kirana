<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
        use SoftDeletes;

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
