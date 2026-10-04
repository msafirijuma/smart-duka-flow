@extends('layouts.admin')
@section('title', $user->name)

@section('content')
<div class="d-flex justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0">Name: {{ $user->name }}</h4>
        <small class="text-muted">Email: {{ $user->email }}</small>
    </div>
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i> Back
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-semibold">Shops</div>
    <div class="card-body p-3">
        <table class="table table-hover table-striped mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Shop</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($user->shops as $shop)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $shop->name }}</td>
                        <td><span class="badge bg-light text-dark">{{ $shop->pivot->role }}</span></td>
                        <td>
                            <a href="{{ route('admin.shops.show', $shop) }}" class="btn btn-sm btn-outline-primary">View shop</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-3">No shops</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection