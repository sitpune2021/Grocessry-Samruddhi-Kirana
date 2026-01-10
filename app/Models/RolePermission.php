<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    use HasFactory;
    
    protected $casts = [
        'permissions' => 'array'
    ];

    protected $table = 'role_permissions';

    protected $fillable = [
        'admin_id',
        'role_id',
        'permissions',
    ];
}
