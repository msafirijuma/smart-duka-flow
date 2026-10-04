@extends('layouts.admin')
@section('title', 'Users')

@section('content')
<h4 class="fw-bold mb-4">All Users</h4>

<form method="GET" class="mb-3">
    <div class="input-group" style="max-width: 360px;">
        <input type="text" name="q" class="form-control form-control-sm" placeholder="Search..." value="{{ request('q') }}">
        <button class="btn btn-sm btn-primary">Search</button>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <table class="table table-striped table-hover mb-0">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Shops</th>
                <th>Joined</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="fw-semibold">{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->shops->count() }}</td>
                    <td>{{ $user->created_at->format('d M Y') }}</td>
                    <td>
                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-primary">View</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No users</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($users->hasPages())
        <div class="card-footer bg-white">{{ $users->links() }}</div>
    @endif
</div>
@endsection