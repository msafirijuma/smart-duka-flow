@extends('layouts.admin')
@section('title', 'Plans')

@section('content')
<div class="d-flex justify-content-between mb-4">
    <h4 class="fw-bold mb-0">Subscription Plans</h4>
    <a href="{{ route('admin.plans.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg"></i> Add plan
    </a>
</div>

<div class="card border-0 shadow-sm p-3">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Plan</th>
                    <th>Price</th>
                    <th>Limits</th>
                    <th>Exports</th>
                    <th>Shops</th>
                    <th>Status</th>
                    <th width="140"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($plans as $plan)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div class="fw-semibold">{{ $plan->name }}</div>
                            <small class="text-muted">{{ $plan->slug }}</small>
                        </td>
                        <td>{{ $plan->price_label }}</td>
                        <td class="small">
                            Products: {{ $plan->max_products ?? '∞' }}<br>
                            Staff: {{ $plan->max_staff ?? '∞' }}
                        </td>
                        <td>{{ $plan->has_exports ? 'Yes' : 'No' }}</td>
                        <td>{{ $plan->shops_count }}</td>
                        <td>
                            @if($plan->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Off</span>
                            @endif
                        </td>
                        <td class="text-nowrap">
                            <a href="{{ route('admin.plans.edit', $plan) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="{{ route('admin.plans.destroy', $plan) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Delete this plan?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"
                                    @if($plan->shops_count > 0) disabled title="Reassign shops first" @endif>
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection