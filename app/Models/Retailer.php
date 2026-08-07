<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Retailer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'shop_id',
        'name',
        'email',
        'mobile',
        'address',
        'dob',
        'gender',
        'gst_number',
        'shop_name',
        'is_active',
        'created_by',
    ];

    public function offers()
    {
        return $this->hasMany(RetailerOffer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shop()
    {
        return $this->belongsTo(Warehouse::class, 'shop_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }


}
