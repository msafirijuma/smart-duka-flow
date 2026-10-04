<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Shop;
use App\Models\User;

class PlanLimitService
{
    public function shop(): ?Shop
    {
        $id = session('current_shop_id');
        if (!$id) return null;

        return Shop::with('plan')->find($id);
    }

    /** @return string|null  error message or null if OK */
    public function canAddProduct(): ?string
    {
        $shop = $this->shop();
        if (!$shop?->plan) return null;

        $max = $shop->plan->max_products;
        if ($max === null) return null; // unlimited

        $count = Product::where('shop_id', $shop->id)->count();
        if ($count >= $max) {
            return "Product limit reached ({$max}). Upgrade your plan to add more.";
        }

        return null;
    }

    public function canAddStaff(): ?string
    {
        $shop = $this->shop();
        if (!$shop?->plan) return null;

        $max = $shop->plan->max_staff;
        if ($max === null) return null;

        // Count users on this shop (all roles)
        $count = $shop->users()->count();
        if ($count >= $max) {
            return "Staff limit reached ({$max}). Upgrade your plan to add more.";
        }

        return null;
    }

    public function canExport(): ?string
    {
        $shop = $this->shop();
        if (!$shop?->plan) return null;

        if (!$shop->plan->has_exports) {
            return 'CSV export is available on Pro and Business plans. Please upgrade.';
        }

        return null;
    }
}