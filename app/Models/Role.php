<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory;
   use SoftDeletes;
    // Fillable fields
   protected $fillable =
    ['name', 'description'];


    // Relationship: One role can have many users
    public function users()
    {
        return $this->hasMany(User::class);
    }

}
