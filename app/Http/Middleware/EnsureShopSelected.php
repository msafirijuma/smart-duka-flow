<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureShopSelected
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session('current_shop_id')) {
            // Optional: try to set default shop
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

        return $next($request);
    }
}