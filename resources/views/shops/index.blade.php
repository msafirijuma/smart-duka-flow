@extends('layouts.app')

@section('title', 'My Shops')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">My Shops</h4>
    <a href="{{ route('shops.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg"></i> New Shop
    </a>
</div>

<div class="row g-3">
    @forelse($shops as $shop)
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="fw-bold mb-0">{{ $shop->name }}</h5>
                        @if(session('current_shop_id') == $shop->id)
                            <span class="badge bg-success">Current</span>
                        @endif
                    </div>

                    <p class="text-muted small mb-3">
                        {{ $shop->address ?? 'No address' }}
                    </p>

                    <div class="d-flex gap-2">
                        @if(session('current_shop_id') != $shop->id)
                            <form action="{{ route('shops.switch', $shop) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-primary btn-sm">
                                    Switch to this shop
                                </button>
                            </form>
                        @else
                            <button class="btn btn-success btn-sm" disabled>Active</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-info">
                You don’t have any shop yet.
                <a href="{{ route('shops.create') }}">Create your first shop</a>
            </div>
        </div>
    @endforelse
</div>
@endsection