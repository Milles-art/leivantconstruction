<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'provider_id',
        'account_type',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(Inquiry::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function hasAdminPermission(string $permission): bool
    {
        if (! $this->is_admin) {
            return false;
        }

        $role = $this->role ?: ($this->is_admin ? 'super_admin' : 'provider');

        if ($role === 'super_admin') {
            return true;
        }

        $permissions = [
            'manager' => [
                'dashboard.view',
                'pipeline.manage',
                'projects.manage',
                'catalog.manage',
                'providers.manage',
                'reviews.manage',
                'orders.view',
                'mail.manage',
            ],
            'sales' => [
                'dashboard.view',
                'pipeline.manage',
                'projects.manage',
                'reviews.manage',
                'mail.manage',
            ],
            'content' => [
                'dashboard.view',
                'catalog.manage',
                'providers.manage',
                'reviews.manage',
            ],
        ];

        return in_array($permission, $permissions[$role] ?? [], true);
    }
}

