<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Retailer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'mobile',
        'address',
        'is_active',
    ];
    public function offers()
    {
        return $this->hasMany(RetailerOffer::class);
    }
}
