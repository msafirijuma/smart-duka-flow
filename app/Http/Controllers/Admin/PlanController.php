<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::withCount('shops')->orderBy('sort_order')->get();
        return view('admin.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.plans.form', ['plan' => new Plan()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
        Plan::create($data);
        return redirect()->route('admin.plans.index')->with('success', 'Plan created.');
    }

    public function edit(Plan $plan)
    {
        return view('admin.plans.form', compact('plan'));
    }

    public function update(Request $request, Plan $plan)
    {
        $data = $this->validated($request);
        // keep slug stable unless name change — optional regenerate
        $plan->update($data);
        return redirect()->route('admin.plans.index')->with('success', 'Plan updated.');
    }

    public function destroy(Plan $plan)
    {
        if ($plan->shops()->exists()) {
            return back()->with('error', 'Cannot delete: shops are still on this plan. Reassign them first.');
        }
        $plan->delete();
        return back()->with('success', 'Plan deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'price_label' => 'nullable|string|max:50',
            'price' => 'required|numeric|min:0',
            'max_products' => 'nullable|integer|min:1',
            'max_staff' => 'nullable|integer|min:1',
            'max_shops' => 'nullable|integer|min:1',
            'has_reports' => 'sometimes|boolean',
            'has_exports' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);
        $data['has_reports'] = $request->boolean('has_reports');
        $data['has_exports'] = $request->boolean('has_exports');
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;
        return $data;
    }
}