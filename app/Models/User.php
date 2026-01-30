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
    use HasApiTokens, Notifiable;

    
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
        'warehouse_id',
        'otp',
        'otp_expires_at',
        'is_online',
        'duty_start_time',
        'duty_paused_at',
        'total_duty_minutes'
    ];

   
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'duty_start_time' => 'datetime',
        'duty_paused_at' => 'datetime',
        'is_online' => 'boolean',
    ];
    

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
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

    public function deliveryNotifications()
    {
        return $this->hasMany(
            DeliveryNotification::class,
            'delivery_agent_id'
        );
    }

    public function notificationSettings()
    {
        return $this->hasOne(
            DeliveryNotificationSetting::class,
            'delivery_agent_id'
        );
    }

   
}
