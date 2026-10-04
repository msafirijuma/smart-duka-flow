<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::withCount('shops')
            ->orderBy('sort_order')
            ->get();

        return view('admin.plans.index', compact('plans'));
    }
}