<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $shopId = session('current_shop_id');

        $query = Expense::with('user')
            ->where('shop_id', $shopId)
            ->latest('expense_date');

        if ($request->filled('from')) {
            $query->whereDate('expense_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('expense_date', '<=', $request->to);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $expenses = $query->paginate(20)->withQueryString();

        $totalExpenses = Expense::where('shop_id', $shopId)->sum('amount');
        $monthExpenses = Expense::where('shop_id', $shopId)
            ->whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');

        return view('expenses.index', compact('expenses', 'totalExpenses', 'monthExpenses'));
    }

    public function create()
    {
        return view('expenses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'          => 'required|string|max:255',
            'amount'         => 'required|numeric|min:0.01',
            'category'       => 'nullable|string|max:100',
            'expense_date'   => 'required|date',
            'payment_method' => 'required|in:cash,mpesa,bank',
            'description'    => 'nullable|string',
        ]);

        Expense::create([
            'shop_id'        => session('current_shop_id'),
            'user_id'        => auth()->id(),
            'title'          => $request->title,
            'amount'         => $request->amount,
            'category'       => $request->category,
            'expense_date'   => $request->expense_date,
            'payment_method' => $request->payment_method,
            'description'    => $request->description,
        ]);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense recorded successfully.');
    }

    public function edit(Expense $expense)
    {
        $this->authorizeShop($expense);
        return view('expenses.edit', compact('expense'));
    }

    public function update(Request $request, Expense $expense)
    {
        $this->authorizeShop($expense);

        $request->validate([
            'title'          => 'required|string|max:255',
            'amount'         => 'required|numeric|min:0',
            'category'       => 'nullable|string|max:100',
            'expense_date'   => 'required|date',
            'payment_method' => 'required|in:cash,mpesa,bank',
            'description'    => 'nullable|string',
        ]);

        $expense->update($request->only([
            'title', 'amount', 'category', 'expense_date', 'payment_method', 'description'
        ]));

        return redirect()->route('expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        $this->authorizeShop($expense);
        $expense->delete();

        return redirect()->route('expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }

    private function authorizeShop(Expense $expense)
    {
        if ($expense->shop_id != session('current_shop_id')) {
            abort(403);
        }
    }
}