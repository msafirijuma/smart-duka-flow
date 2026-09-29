<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

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

        $shop = Shop::create([
            'name'     => $request->name,
            'slug'     => Str::slug($request->name) . '-' . Str::random(5),
            'phone'    => $request->phone,
            'email'    => $request->email,
            'address'  => $request->address,
            'currency' => 'TZS',
        ]);

        // Attach current user as owner
        $shop->users()->attach(Auth::id(), [
            'role'       => 'owner',
            'is_default' => Auth::user()->shops()->count() === 0, // first shop = default
        ]);

        // Set as current shop
        session(['current_shop_id' => $shop->id]);

        return redirect()->route('dashboard')
            ->with('success', 'Shop created successfully!');
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