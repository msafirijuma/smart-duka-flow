<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Supplier;
use App\Models\SupplierPayment;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::where('shop_id', session('current_shop_id'))
            ->latest()
            ->paginate(20);

        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'nullable|string|max:20',
            'email'   => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        Supplier::create([
            'shop_id' => session('current_shop_id'),
            'name'    => $request->name,
            'phone'   => $request->phone,
            'email'   => $request->email,
            'address' => $request->address,
            'balance' => 0,
        ]);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier created successfully.');
    }

    public function show(Supplier $supplier)
    {
        $this->authorizeShop($supplier);

        $supplier->load([
            'purchases' => fn ($q) => $q->with('user')->latest('purchase_date')->take(15),
            'payments'  => fn ($q) => $q->with('user')->latest()->take(15),
        ]);

        $totalPurchases = $supplier->purchases()->sum('total');
        $totalCredit = $supplier->purchases()
            ->where('payment_method', 'credit')
            ->sum('total');

        return view('suppliers.show', compact('supplier', 'totalPurchases', 'totalCredit'));
    }

    public function edit(Supplier $supplier)
    {
        $this->authorizeShop($supplier);
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $this->authorizeShop($supplier);

        $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'nullable|string|max:20',
            'email'   => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        $supplier->update($request->only(['name', 'phone', 'email', 'address']));

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    public function paymentForm(Supplier $supplier)
    {
        $this->authorizeShop($supplier);

        if ($supplier->balance <= 0) {
            return redirect()->route('suppliers.show', $supplier)
                ->with('error', 'This supplier has no outstanding balance.');
        }

        return view('suppliers.payment', compact('supplier'));
    }

    public function recordPayment(Request $request, Supplier $supplier)
    {
        $this->authorizeShop($supplier);

        $request->validate([
            'amount'         => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,mpesa,bank',
            'notes'          => 'nullable|string|max:500',
        ]);

        if ($request->amount > $supplier->balance) {
            return back()->withErrors([
                'amount' => 'Amount cannot exceed outstanding balance (TZS ' .
                    number_format($supplier->balance, 0) . ').',
            ])->withInput();
        }

        DB::transaction(function () use ($request, $supplier) {
            SupplierPayment::create([
                'shop_id'        => session('current_shop_id'),
                'supplier_id'    => $supplier->id,
                'user_id'        => auth()->id(),
                'amount'         => $request->amount,
                'payment_method' => $request->payment_method,
                'notes'          => $request->notes,
            ]);

            $supplier->decrement('balance', $request->amount);
        });

        return redirect()->route('suppliers.show', $supplier)
            ->with('success', 'Payment to supplier recorded successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        $this->authorizeShop($supplier);
        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }

    private function authorizeShop(Supplier $supplier)
    {
        if ($supplier->shop_id != session('current_shop_id')) {
            abort(403);
        }
    }
}