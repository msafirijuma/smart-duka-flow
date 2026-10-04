<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\User;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $shopsCount = Shop::count();
        $activeShops = Shop::where('is_active', true)->count();
        $usersCount = User::where('is_admin', false)->count();
        $salesToday = Sale::whereDate('created_at', today())
            ->where('status', 'completed')
            ->sum('total');

        $recentShops = Shop::with('users')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'shopsCount',
            'activeShops',
            'usersCount',
            'salesToday',
            'recentShops'
        ));
    }
}