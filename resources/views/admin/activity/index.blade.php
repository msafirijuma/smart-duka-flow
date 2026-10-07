@extends('layouts.admin')
@section('title', 'Activity logs')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-0">Activity logs</h4>
    </div>
</div>

<form method="GET" class="card border-0 shadow-sm mb-3">
    <div class="card-body py-3">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small mb-1">Search</label>
                <input type="text" name="q" class="form-control form-control-sm"
                       value="{{ request('q') }}" placeholder="Description or action…">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Action</label>
                <select name="action" class="form-select form-select-sm">
                    <option value="">All actions</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" @selected(request('action') === $action)>
                            {{ $action }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Shop</label>
                <select name="shop_id" class="form-select form-select-sm">
                    <option value="">All shops</option>
                    @foreach($shops as $shop)
                        <option value="{{ $shop->id }}" @selected(request('shop_id') == $shop->id)>
                            {{ $shop->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-sm btn-primary w-100">Filter</button>
            </div>
        </div>
    </div>
</form>

<div class="card border-0 shadow-sm p-3">
    <div class="table-responsive">
        <table class="table table-striped table-hover table-sm mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>User</th>
                    <th>Shop</th>
                    <th>Action</th>
                    <th>Description</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="text-nowrap small">
                            {{ $log->created_at->format('d M Y H:i') }}
                        </td>
                        <td class="small">{{ $log->user->name ?? '—' }}</td>
                        <td class="small">{{ $log->shop->name ?? '—' }}</td>
                        <td>
                            <code class="small">{{ $log->action }}</code>
                        </td>
                        <td class="small">{{ $log->description }}</td>
                        <td class="small text-muted">{{ $log->ip ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No activity yet</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
        <div class="card-footer bg-white">{{ $logs->links() }}</div>
    @endif
</div>
@endsection