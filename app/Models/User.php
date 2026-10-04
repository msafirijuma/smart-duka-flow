<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'is_admin' => 'boolean',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    public function shops(): BelongsToMany
    {
        return $this->belongsToMany(Shop::class)
                    ->withPivot('role', 'is_default')
                    ->withTimestamps();
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    // Helper: get current shop from session
    public function currentShop()
    {
        $shopId = session('current_shop_id');

        if ($shopId) {
            return $this->shops()->where('shops.id', $shopId)->first();
        }

        // fallback to default shop
        return $this->shops()->wherePivot('is_default', true)->first()
            ?? $this->shops()->first();
    }

    public function roleInCurrentShop(): ?string
    {
        $shopId = session('current_shop_id');
        if (!$shopId) {
            return null;
        }

        $shop = $this->shops()->where('shops.id', $shopId)->first();

        return $shop?->pivot?->role;
    }

    public function isOwner(): bool
    {
        return $this->roleInCurrentShop() === 'owner';
    }

    public function isManager(): bool
    {
        return in_array($this->roleInCurrentShop(), ['owner', 'manager']);
    }

    public function isCashier(): bool
    {
        return $this->roleInCurrentShop() === 'cashier';
    }

    public function canManageStaff(): bool
    {
        return $this->isOwner();
    }

    public function canManageSettings(): bool
    {
        return $this->isOwner();
    }

    public function canManageProducts(): bool
    {
        return $this->isManager(); // owner + manager
    }

    public function canManageExpenses(): bool
    {
        return $this->isManager();
    }

}