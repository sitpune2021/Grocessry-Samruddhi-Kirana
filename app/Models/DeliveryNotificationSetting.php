<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryNotificationSetting extends Model
{
    protected $fillable = [
        'delivery_agent_id',
        'user_id',
        'new_order',
        'updates',
        'chat',
        'promo',
        'app_updates'
    ];
}
