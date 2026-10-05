<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Services\PlanLimitService;

class StaffController extends Controller
{
    public function index()
    {
        $shopId = session('current_shop_id');
        $shop = Shop::with(['users' => function ($q) {
            $q->withPivot('role', 'is_default');
        }])->findOrFail($shopId);

        return view('staff.index', compact('shop'));
    }

    public function create()
    {
        return view('staff.create');
    }

    public function store(Request $request)
    {
        $shopId = session('current_shop_id');

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'role'  => 'required|in:manager,cashier',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($msg = (new PlanLimitService)->canAddStaff()) {
            return back()->with('error', $msg)->withInput();
        }

        // Check if user already exists
        $user = User::where('email', $request->email)->first();

        if ($user) {
            // Already in this shop?
            if ($user->shops()->where('shops.id', $shopId)->exists()) {
                return back()->withErrors(['email' => 'This user is already a staff member of this shop.']);
            }
        } else {
            // Create new user
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);
        }

        // Attach to shop
        $user->shops()->attach($shopId, [
            'role'       => $request->role,
            'is_default' => false,
        ]);

        // Assign Spatie role
        $user->assignRole($request->role);

        // activity log
        ActivityLogger::log(
            'staff.created',
            "Staff {$user->name} added as {$role}",
            $user,
            $shopId,
            ['role' => $role]
        );

        return redirect()->route('staff.index')
            ->with('success', 'Staff member added successfully.');
    }

    public function edit(User $user)
    {
        $shopId = session('current_shop_id');

        $pivot = $user->shops()->where('shops.id', $shopId)->first();

        if (!$pivot) {
            abort(403);
        }

        return view('staff.edit', [
            'user' => $user,
            'role' => $pivot->pivot->role,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $shopId = session('current_shop_id');

        $request->validate([
            'role' => 'required|in:manager,cashier,owner',
        ]);

        // Update pivot role
        $user->shops()->updateExistingPivot($shopId, [
            'role' => $request->role,
        ]);

        // Sync Spatie role
        $user->syncRoles([$request->role]);

        // activity log
        ActivityLogger::log('staff.updated', "Staff {$user->name} updated", $user, $shopId);

        return redirect()->route('staff.index')
            ->with('success', 'Staff role updated successfully.');
    }

    public function destroy(User $user)
    {
        $shopId = session('current_shop_id');

        // Prevent removing yourself
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot remove yourself.');
        }

        // activity log
        ActivityLogger::log('staff.deleted', "Staff {$name} removed", null, $shopId);

        $user->shops()->detach($shopId);

        return redirect()->route('staff.index')
            ->with('success', 'Staff member removed from this shop.');
    }
}