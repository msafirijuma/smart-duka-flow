@extends('layouts.app')
@section('title', 'Staff')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Staff Members</h4>
    <a href="{{ route('staff.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg"></i> Add Staff
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($shop->users as $user)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-semibold">
                                {{ $user->name }}
                                @if($user->id === auth()->id())
                                    <span class="badge bg-info">You</span>
                                @endif
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge bg-primary text-uppercase">
                                    {{ $user->pivot->role }}
                                </span>
                            </td>
                            <td>
                                @if($user->id !== auth()->id())
                                    <a href="{{ route('staff.edit', $user) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form action="{{ route('staff.destroy', $user) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Remove this staff from the shop?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Remove</button>
                                    </form>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No staff members yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection