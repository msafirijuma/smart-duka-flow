<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\Plan;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Shop::withCount('users')->latest();

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($qry) use ($q) {
                $qry->where('name', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $shops = $query->paginate(20)->withQueryString();

        return view('admin.shops.index', compact('shops'));
    }

    public function show(Shop $shop)
    {
        $shop->load([
            'plan',
            'users' => fn ($q) => $q->withPivot('role', 'is_default'),
            'subscriptionHistories' => fn ($q) => $q
                ->with(['plan', 'previousPlan', 'changedByUser'])
                ->latest()
                ->take(20),
        ]);

        $plans = \App\Models\Plan::where('is_active', true)->orderBy('sort_order')->get();

        $salesCount = $shop->sales()->where('status', 'completed')->count();
        $salesTotal = $shop->sales()->where('status', 'completed')->sum('total');
        $productsCount = $shop->products()->count();
        $customersCount = $shop->customers()->count();

        $lastSale = $shop->sales()->where('status', 'completed')->latest()->first();

        $salesThisMonth = $shop->sales()
            ->where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');

        return view('admin.shops.show', compact(
            'shop', 'plans', 'salesCount', 'salesTotal',
            'productsCount', 'customersCount', 'lastSale', 'salesThisMonth'
        ));
    }

    public function updatePlan(Request $request, Shop $shop)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'subscription_ends_at' => 'nullable|date',
            'note' => 'nullable|string|max:255',
        ]);

        $previousPlanId = $shop->plan_id;

        $shop->update([
            'plan_id' => $request->plan_id,
            'subscription_ends_at' => $request->subscription_ends_at,
        ]);

        // Only log if plan or end date actually matters
        SubscriptionHistory::create([
            'shop_id'              => $shop->id,
            'plan_id'              => $request->plan_id,
            'previous_plan_id'     => $previousPlanId,
            'changed_by'           => auth()->id(),
            'subscription_ends_at' => $request->subscription_ends_at,
            'note'                 => $request->note,
        ]);

        return back()->with('success', 'Plan updated successfully.');
    }

    public function toggle(Shop $shop)
    {
        $shop->update(['is_active' => !$shop->is_active]);

        $status = $shop->is_active ? 'activated' : 'suspended';

        return back()->with('success', "Shop {$status} successfully.");
    }
}