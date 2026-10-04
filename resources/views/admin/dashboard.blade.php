@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
<h4 class="fw-bold mb-4">Platform Overview</h4>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Total Shops</div>
                <div class="fs-3 fw-bold">{{ $shopsCount }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Active Shops</div>
                <div class="fs-3 fw-bold text-success">{{ $activeShops }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Users</div>
                <div class="fs-3 fw-bold">{{ $usersCount }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Sales today (all)</div>
                <div class="fs-5 fw-bold text-primary">TZS {{ number_format($salesToday, 0) }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-semibold">Recent shops</div>
    <div class="card-body p-0">
        <table class="table table-striped table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentShops as $shop)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="fw-semibold">{{ $shop->name }}</td>
                        <td>{{ $shop->phone ?? '—' }}</td>
                        <td>
                            @if($shop->is_active ?? true)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Suspended</span>
                            @endif
                        </td>
                        <td>{{ $shop->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('admin.shops.show', $shop) }}" class="btn btn-sm btn-outline-primary">View</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-3">No shops yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection