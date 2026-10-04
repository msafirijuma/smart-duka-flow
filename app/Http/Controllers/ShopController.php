<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShopController extends Controller
{
    public function index()
    {
        $shops = Auth::user()->shops;

        return view('shops.index', compact('shops'));
    }

    public function create()
    {
        return view('shops.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'nullable|string|max:20',
            'email'   => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        $freePlanId = \App\Models\Plan::where('slug', 'free')->value('id');

        // Unique slug from name
        $baseSlug = Str::slug($request->name);
        $slug = $baseSlug;
        $i = 1;
        while (\App\Models\Shop::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $i;
            $i++;
        }

        DB::transaction(function () use ($request, $freePlanId, $slug) {
            $shop = \App\Models\Shop::create([
                'name'      => $request->name,
                'slug'      => $slug,          // ← hii ilikuwepo missing
                'phone'     => $request->phone,
                'email'     => $request->email,
                'address'   => $request->address,
                'is_active' => true,
                'plan_id'   => $freePlanId,
            ]);

            $shop->users()->attach(auth()->id(), [
                'role'       => 'owner',
                'is_default' => true,
            ]);

            session(['current_shop_id' => $shop->id]);
        });

        return redirect()->route('dashboard')
            ->with('success', 'Shop created successfully.');
    }

    public function switch(Shop $shop)
    {
        // Check if user belongs to this shop
        $belongs = Auth::user()->shops()->where('shops.id', $shop->id)->exists();

        if (!$belongs) {
            abort(403, 'You do not have access to this shop.');
        }

        session(['current_shop_id' => $shop->id]);

        return redirect()->route('dashboard')
            ->with('success', 'Switched to ' . $shop->name);
    }
}