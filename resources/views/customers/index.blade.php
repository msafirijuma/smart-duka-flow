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

<div class="card border-1 shadow-sm">
    <div class="card-body p-3">

        <!-- Search Bar Header -->
        <div class="card-header bg-transparent border-0 py-3">
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="position-relative">
                        <span class="position-absolute top-50 start-0 translate-middle-y ms-3 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" 
                            id="customerSearchInput" 
                            class="form-control border-secondary ps-5 pe-5" 
                            placeholder="Search customer by name or phone..." 
                            autocomplete="off">

                        <!-- Clear (X) Button (Hidden by default) -->
                        <button type="button" 
                                id="clearSearchBtn" 
                                class="btn-close btn-close-white position-absolute top-50 end-0 translate-middle-y me-3 d-none" 
                                aria-label="Clear search">
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- table -->
        <div class="table-responsive">
            <table class="table table-striped table-hover table-sm mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px; min-width: 50px">#</th>
                        <th style="width: 130px; min-width: 130px">Name</th>
                        <th style="width: 130px; min-width: 130px">Phone</th>
                        <th style="width: 130px; min-width: 130px" class="text-end">Credit Limit</th>
                        <th style="width: 80px; min-width: 80px" class="text-end">Balance/Debt</th>
                        <th style="width: 180px; min-width: 180px" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-semibold">{{ $customer->name }}</td>
                            <td>{{ $customer->phone ?? '—' }}</td>
                            <td class="text-end">TZS {{ number_format($customer->credit_limit, 0) }}</td>
                            <td class="text-end">
                                @if($customer->balance <= 0)
                                    <span class="badge bg-success">Clear</span>
                                    <div class="small text-muted">TZS 0</div>
                                @else
                                    <span class="badge bg-danger" >Has Debt</span>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('customerSearchInput');
    const clearBtn = document.getElementById('clearSearchBtn');

    if (searchInput) {
        // filter rows method
        function filterCustomers() {
            const filter = searchInput.value.toLowerCase().trim();
            const rows = document.querySelectorAll('tbody tr');

            // show/hide (X) icon
            if (filter.length > 0) {
                clearBtn.classList.remove('d-none');
            } else {
                clearBtn.classList.add('d-none');
            }

            // Filter table rows
            rows.forEach(row => {
                if (row.id === 'noDataRow') return;

                const text = row.textContent.toLowerCase();
                if (text.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // keyup handler
        searchInput.addEventListener('keyup', filterCustomers);

        // clear search when X btn is clicked
        if (clearBtn) {
            clearBtn.addEventListener('click', function () {
                searchInput.value = '';
                filterCustomers(); // Re-filter
                searchInput.focus(); // return cursor to input
            });
        }
    }
});
</script>
@endpush