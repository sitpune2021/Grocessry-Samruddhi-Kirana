<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverVehicle extends Model
{
    protected $fillable = [
        'driver_id',
        'vehicle_no',
        'vehicle_type',
        'license_no',
        'active'
    ];

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}
