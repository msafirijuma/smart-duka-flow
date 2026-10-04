<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Models\Product;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */

    public function boot(): void
    {
        // Paginator::useBootstrapFive();
        View::composer('layouts.partials.header', function ($view) {
            $lowCount = 0;
            $outCount = 0;

            if (Auth::check() && session('current_shop_id')) {
                $shopId = session('current_shop_id');
                $base = Product::where('shop_id', $shopId)->where('is_active', true);

                $lowCount = (clone $base)
                    ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
                    ->where('stock_quantity', '>', 0)
                    ->count();

                $outCount = (clone $base)->where('stock_quantity', '<=', 0)->count();
            }

            $view->with([
                'headerLowStock' => $lowCount,
                'headerOutStock' => $outCount,
                'headerStockAlerts' => $lowCount + $outCount,
            ]);
        });
    }
}
