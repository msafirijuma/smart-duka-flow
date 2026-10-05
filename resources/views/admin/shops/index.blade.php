@extends('layouts.admin')
@section('title', 'Shops')

@section('content')
<h4 class="fw-bold mb-4">All Shops</h4>

<form method="GET" class="card border-0 shadow-sm mb-3">
    <div class="card-body py-3">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <input type="text" name="q" class="form-control form-control-sm"
                       placeholder="Search name, phone..." value="{{ request('q') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All status</option>
                    <option value="active" @selected(request('status')==='active')>Active</option>
                    <option value="suspended" @selected(request('status')==='suspended')>Suspended</option>
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-sm btn-primary">Filter</button>
            </div>
        </div>
    </div>
</form>

<div class="card border-0 shadow-sm p-3">
    <div class="table-responsive">
        <table class="table table-striped table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Shop</th>
                    <th>Users</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($shops as $shop)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div class="fw-semibold">{{ $shop->name }}</div>
                            <small class="text-muted">{{ $shop->phone ?? $shop->email }}</small>
                        </td>
                        <td>{{ $shop->users_count }}</td>
                        <td>
                            @if($shop->is_active ?? true)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Suspended</span>
                            @endif
                        </td>
                        <td>{{ $shop->created_at->format('d M Y') }}</td>
                        <td class="text-nowrap">
                            <a href="{{ route('admin.shops.show', $shop) }}" class="btn btn-sm btn-outline-primary">View</a>
                            <form action="{{ route('admin.shops.toggle', $shop) }}"
                                method="POST"
                                class="d-inline"
                                onsubmit="return confirmToggle(event, this, '{{ ($shop->is_active ?? true) ? 'Suspend' : 'Activate' }} this shop?')">
                                @csrf
                                <button class="btn btn-sm {{ ($shop->is_active ?? true) ? 'btn-outline-danger' : 'btn-outline-success' }}">
                                    {{ ($shop->is_active ?? true) ? 'Suspend' : 'Activate' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No shops</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($shops->hasPages())
        <div class="card-footer bg-white">{{ $shops->links() }}</div>
    @endif
</div>
@endsection

@section('script')
    <script>
        function confirmToggle(e, form, message) {
        e.preventDefault();
        Swal.fire({
            title: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) form.submit();
        });
        return false;
    }
    </script>
@endsection