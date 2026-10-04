<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $fillable = [
        'name', 'slug', 'price_label', 'price',
        'max_products', 'max_staff', 'max_shops',
        'has_reports', 'has_exports', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'has_reports' => 'boolean',
        'has_exports' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function shops(): HasMany
    {
        return $this->hasMany(Shop::class);
    }

    public function isUnlimited(string $field): bool
    {
        return $this->{$field} === null;
    }
}