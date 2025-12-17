<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

    // Fillable fields
    protected $fillable = [
        'name',
        'description',
    ];

    // Relationship: One role can have many users
    public function users()
    {
        return $this->hasMany(User::class);
    }

}
