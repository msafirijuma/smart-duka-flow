<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $shopId = session('current_shop_id');
        // $shop = Shop::findOrFail($shopId);
        $shop = Shop::with('plan')->findOrFail($shopId);

        return view('settings.index', compact('shop'));
    }

    public function update(Request $request)
    {
        $shopId = session('current_shop_id');
        $shop = Shop::findOrFail($shopId);

        $request->validate([
            'name'           => 'required|string|max:255',
            'phone'          => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:255',
            'address'        => 'nullable|string|max:500',
            'receipt_footer' => 'nullable|string|max:255',
        ]);

        $shop->update([
            'name'           => $request->name,
            'phone'          => $request->phone,
            'email'          => $request->email,
            'address'        => $request->address,
            'receipt_footer' => $request->receipt_footer,
        ]);

        return redirect()->route('settings.index')
            ->with('success', 'Shop settings updated successfully.');
    }
}