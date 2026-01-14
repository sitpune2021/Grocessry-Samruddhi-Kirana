<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryNotificationSetting extends Model
{
    protected $fillable = [
        'user_id',
        'new_order',
        'chat',
        'updates',
        'promo',
        'app_updates',
    ];
}
