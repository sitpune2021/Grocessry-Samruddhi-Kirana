<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Warehouse extends Model
{
    protected $fillable = [
        'parent_id',
        'district_id',
        'taluka_id',
        'name',
        'code',
        'address',
        'contact_person',
        'email',
        'contact_number',
        'status',
    ];
}
