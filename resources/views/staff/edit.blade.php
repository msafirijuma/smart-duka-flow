@extends('layouts.app')
@section('title', 'Edit Staff')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-1">Edit Role</h5>
                <p class="text-muted small mb-4">{{ $user->name }} ({{ $user->email }})</p>

                <form method="POST" action="{{ route('staff.update', $user) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select" required>
                            <option value="cashier" @selected($role == 'cashier')>Cashier</option>
                            <option value="manager" @selected($role == 'manager')>Manager</option>
                            <option value="owner" @selected($role == 'owner')>Owner</option>
                        </select>
                    </div>

                    <div class="d-flex gap-2">
                        <button class="btn btn-primary">Update Role</button>
                        <a href="{{ route('staff.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection