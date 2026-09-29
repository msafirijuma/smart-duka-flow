<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
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
        ];
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
}