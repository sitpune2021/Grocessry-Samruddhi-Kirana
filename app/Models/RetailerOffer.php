<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RetailerOffer extends Model
{
    protected $fillable = [
        'offer_name',
        'discount_type',
        'discount_value',
        'start_date',
        'end_date',
        'status',
        'role_id',
        'user_id'
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
      public function user()
    {
        return $this->belongsTo(User::class);
    }
}
