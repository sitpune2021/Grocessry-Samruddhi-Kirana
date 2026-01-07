<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactDetail extends Model
{
    protected $fillable = [
        'name',
        'email',
        'message',
    ];
}
