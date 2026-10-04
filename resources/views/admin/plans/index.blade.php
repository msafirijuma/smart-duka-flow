@extends('layouts.admin')
@section('title', 'Plans')

@section('content')
<h4 class="fw-bold mb-4">Subscription Plans</h4>

<div class="row g-3">
    @foreach($plans as $plan)
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="fw-bold mb-0">{{ $plan->name }}</h5>
                        @if($plan->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </div>
                    <div class="text-primary fw-semibold mb-3">{{ $plan->price_label }}</div>

                    <ul class="list-unstyled small text-muted mb-3">
                        <li>Products:
                            <strong>{{ $plan->max_products === null ? 'Unlimited' : $plan->max_products }}</strong>
                        </li>
                        <li>Staff:
                            <strong>{{ $plan->max_staff === null ? 'Unlimited' : $plan->max_staff }}</strong>
                        </li>
                        <li>Shops:
                            <strong>{{ $plan->max_shops === null ? 'Unlimited' : $plan->max_shops }}</strong>
                        </li>
                        <li>Reports: {{ $plan->has_reports ? 'Yes' : 'No' }}</li>
                        <li>Exports: {{ $plan->has_exports ? 'Yes' : 'No' }}</li>
                    </ul>

                    <div class="text-muted small">
                        {{ $plan->shops_count }} shop(s) on this plan
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection