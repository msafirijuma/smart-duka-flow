<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Shop; 
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with(['user', 'shop'])->latest();

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($qry) use ($q) {
                $qry->where('description', 'like', "%{$q}%")
                    ->orWhere('action', 'like', "%{$q}%");
            });
        }
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('shop_id')) {
            $query->where('shop_id', $request->shop_id);
        }

        $logs = $query->paginate(40)->withQueryString();

        // unique actions
        $actions = ActivityLog::whereNotNull('action')
            ->distinct()
            ->pluck('action');

        // 2. list of all shops
        $shops = Shop::all();
        
        return view('admin.activity.index', compact('logs', 'actions', 'shops'));
    }
}