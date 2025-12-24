<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasApiTokens, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'mobile',
        'role_id',
        'warehouse_id',
        'password',
        'profile_photo',
        'email_verified_at',
        'remember_token',
        'created_at',
        'updated_at',
        'deleted_at',
        'status',
        'last_login_at',
        'warehouse_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */

    protected $casts = [
        'permissions' => 'array', // IMPORTANT
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
 
    public function permissions()
    {
        return $this->role->permissions();
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
        // OR: return $this->role->name === $role; (if role relation)
    }

    public function hasPermission(string $permission): bool
    {
        if (!$this->permissions) {
            return false;
        }

        $permissions = is_array($this->permissions)
            ? $this->permissions
            : json_decode($this->permissions, true);

        return in_array($permission, $permissions);
    }

    // public function hasPermission(string $permission): bool
    // {

    //     if ($this->role == 2) {
    //         return true;
    //     }

    //     if (!empty($this->permissions) && in_array($permission, $this->permissions)) {
    //         return true;
    //     }

    //     $rolePerm = RolePermission::where('role_id', $this->role_id)->first();

    //     if (!$rolePerm) {
    //         return false;
    //     }

    //     return in_array($permission, $rolePerm->permissions ?? []);
    // }
}
