@extends('layouts.app')
@section('title', 'Customers')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Customers</h4>
    @if(auth()->user()->isManager())
        <a href="{{ route('customers.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Add Customer
        </a>
    @endif
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th style="width: 130px; min-width: 130px">Credit Limit</th>
                        <th style="width: 130px; min-width: 130px">Balance (Debt)</th>
                        <th style="width: 180px; min-width: 180px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-semibold">{{ $customer->name }}</td>
                            <td>{{ $customer->phone ?? '—' }}</td>
                            <td>TZS {{ number_format($customer->credit_limit, 0) }}</td>
                            <td>
                                @if($customer->balance <= 0)
                                    <span class="badge bg-success">Clear</span>
                                    <div class="small text-muted">TZS 0</div>
                                @else
                                    <span class="badge bg-danger">Has Debt</span>
                                    <div class="small fw-semibold text-danger">
                                        TZS {{ number_format($customer->balance, 0) }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div  class="d-flex flex-wrap justify-content-center align-items-center gap-2">
                                    <!-- Record Payment – everyone, only if has debt -->
                                    @if($customer->balance > 0)
                                        <a href="{{ route('customers.payment', $customer) }}" class="btn btn-sm btn-success">
                                            Pay
                                        </a>
                                    @endif
                                    <a href="{{ route('customers.show', $customer) }}" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(auth()->user()->isManager())
                                        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-outline-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                    @if(auth()->user()->isOwner())
                                        <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="d-inline"
                                            onsubmit="return confirm('Delete this customer?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No customers yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($customers->hasPages())
        <div class="card-footer bg-white">{{ $customers->links() }}</div>
    @endif
</div>
@endsection