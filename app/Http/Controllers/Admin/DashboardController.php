<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Plan;
use App\Models\Shop;
use App\Models\SubscriptionHistory;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $totalShops     = Shop::count();
        $activeShops    = Shop::where('is_active', true)->count();
        $suspendedShops = Shop::where('is_active', false)->count();
        $totalUsers     = User::where(function ($q) {
            $q->where('is_admin', false)->orWhereNull('is_admin');
        })->count();

        // This month growth
        $newShopsMonth = Shop::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $newUsersMonth = User::where(function ($q) {
                $q->where('is_admin', false)->orWhereNull('is_admin');
            })
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Subscription / plan changes this month
        $planChangesMonth = 0;
        if (Schema::hasTable('subscription_histories')) {
            $planChangesMonth = SubscriptionHistory::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();
        } else {
            $planChangesMonth = ActivityLog::where('action', 'plan.changed')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();
        }

        $plansCount = Plan::where('is_active', true)->count();

        // Chart: new shops last 6 months
        $shopsChartLabels = [];
        $shopsChartData   = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $shopsChartLabels[] = $m->format('M Y');
            $shopsChartData[] = Shop::whereMonth('created_at', $m->month)
                ->whereYear('created_at', $m->year)
                ->count();
        }

        // Chart: new users last 6 months
        $usersChartLabels = [];
        $usersChartData   = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $usersChartLabels[] = $m->format('M Y');
            $usersChartData[] = User::where(function ($q) {
                    $q->where('is_admin', false)->orWhereNull('is_admin');
                })
                ->whereMonth('created_at', $m->month)
                ->whereYear('created_at', $m->year)
                ->count();
        }

        $planBreakdown = Plan::withCount('shops')->orderBy('sort_order')->get();
        $recentShops   = Shop::with('plan')->latest()->take(5)->get();
        $recentLogs    = ActivityLog::with(['user', 'shop'])->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalShops',
            'activeShops',
            'suspendedShops',
            'totalUsers',
            'newShopsMonth',
            'newUsersMonth',
            'planChangesMonth',
            'plansCount',
            'shopsChartLabels',
            'shopsChartData',
            'usersChartLabels',
            'usersChartData',
            'planBreakdown',
            'recentShops',
            'recentLogs'
        ));
    }
}