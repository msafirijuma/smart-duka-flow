<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::where('shop_id', session('current_shop_id'))
            ->latest()
            ->paginate(20);

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'phone'        => 'nullable|string|max:20',
            'email'        => 'nullable|email|max:255',
            'address'      => 'nullable|string|max:500',
            'credit_limit' => 'nullable|numeric|min:0',
        ]);

        Customer::create([
            'shop_id'      => session('current_shop_id'),
            'name'         => $request->name,
            'phone'        => $request->phone,
            'email'        => $request->email,
            'address'      => $request->address,
            'credit_limit' => $request->credit_limit ?? 0,
            'balance'      => 0,
        ]);

        return redirect()->route('customers.index')
            ->with('success', 'Customer created successfully.');
    }

    public function show(Customer $customer)
    {
        $this->authorizeShop($customer);

        $customer->load([
            'sales' => function ($q) {
                $q->latest()->take(10);
            },
            'payments' => function ($q) {
                $q->with('user')->latest()->take(10);
            },
        ]);

        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        $this->authorizeShop($customer);
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $this->authorizeShop($customer);

        $request->validate([
            'name'         => 'required|string|max:255',
            'phone'        => 'nullable|string|max:20',
            'email'        => 'nullable|email|max:255',
            'address'      => 'nullable|string|max:500',
            'credit_limit' => 'nullable|numeric|min:0',
        ]);

        $customer->update($request->only([
            'name', 'phone', 'email', 'address', 'credit_limit'
        ]));

        return redirect()->route('customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        $this->authorizeShop($customer);
        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Customer deleted successfully.');
    }

    private function authorizeShop(Customer $customer)
    {
        if ($customer->shop_id != session('current_shop_id')) {
            abort(403);
        }
    }

    public function paymentForm(Customer $customer)
    {
        $this->authorizeShop($customer);
        return view('customers.payment', compact('customer'));
    }

    public function recordPayment(Request $request, Customer $customer)
    {
        $this->authorizeShop($customer);

        $request->validate([
            'amount'         => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,mpesa,bank',
            'notes'          => 'nullable|string|max:500',
        ]);

        if ($request->amount > $customer->balance) {
            return back()->withErrors(['amount' => 'Amount cannot be more than the outstanding balance (TZS ' . number_format($customer->balance, 0) . ')']);
        }

        DB::transaction(function () use ($request, $customer) {
            CustomerPayment::create([
                'shop_id'        => session('current_shop_id'),
                'customer_id'    => $customer->id,
                'user_id'        => auth()->id(),
                'amount'         => $request->amount,
                'payment_method' => $request->payment_method,
                'notes'          => $request->notes,
            ]);

            $customer->decrement('balance', $request->amount);
        });

        // activity log
        ActivityLogger::log(
            'customer.payment',
            "Payment TZS " . number_format($amount, 0) . " from {$customer->name}",
            $customer,
            $shopId,
            ['amount' => $amount]
        );

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Payment recorded successfully.');
    }
}