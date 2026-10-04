<?php

namespace App\Http\Middleware;

use App\Models\Shop;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureShopSelected
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1) Hakuna shop iliyochaguliwa → chagua default au create
        if (!session('current_shop_id')) {
            $user = $request->user();

            if ($user) {
                $defaultShop = $user->shops()->wherePivot('is_default', true)->first()
                    ?? $user->shops()->first();

                if ($defaultShop) {
                    session(['current_shop_id' => $defaultShop->id]);
                } else {
                    return redirect()->route('shops.create')
                        ->with('error', 'Please create or select a shop first.');
                }
            }
        }

        // 2) Shop ipo session — angalia kama IMESUSPENDIWA
        $shopId = session('current_shop_id');

        if ($shopId) {
            $shop = Shop::find($shopId);

            // Shop haipo tena
            if (!$shop) {
                session()->forget('current_shop_id');

                return redirect()->route('shops.index')
                    ->with('error', 'Shop not found. Please select another shop.');
            }

            // Shop imesuspendiwa na Super Admin
            if (isset($shop->is_active) && !$shop->is_active) {
                session()->forget('current_shop_id');

                return redirect()->route('shops.index')
                    ->with('error', 'This shop has been suspended. Contact support.');
            }
        }

        return $next($request);
    }
}