<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shop extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'phone',
        'email',
        'address',
        'receipt_footer',
        'currency',
        'logo',
        'is_active',
        'plan_id', 
        'subscription_ends_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'subscription_ends_at' => 'datetime',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
                    ->withPivot('role', 'is_default')
                    ->withTimestamps();
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function subscriptionHistories()
    {
        return $this->hasMany(SubscriptionHistory::class)->latest();
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function settings(): HasMany
    {
        return $this->hasMany(Setting::class);
    }
}