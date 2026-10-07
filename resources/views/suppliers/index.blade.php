@extends('layouts.app')
@section('title', 'Suppliers')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Suppliers</h4>
    <a href="{{ route('suppliers.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg"></i> Add Supplier
    </a>
</div>

<div class="card border-1 shadow-sm">
    <div class="card-body p-4">

        <!-- Search Bar Header -->
        <div class="card-header bg-transparent border-0 py-3">
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="position-relative">
                        <span class="position-absolute top-50 start-0 translate-middle-y ms-3 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" 
                            id="supplierSearchInput" 
                            class="form-control border-secondary ps-5 pe-5" 
                            placeholder="Search supplier by name or phone..." 
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
                        <th style="width: 80px; min-width: 80px">Name</th>
                        <th style="width: 80px; min-width: 80px">Phone</th>
                        <th style="width: 100px; min-width: 100px">Balance/Debt</th>
                        <th style="width: 130px; min-width: 130px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-semibold">{{ $supplier->name }}</td>
                            <td>{{ $supplier->phone ?? '—' }}</td>
                            <td>
                                @if($supplier->balance > 0)
                                    <span class="badge bg-warning text-dark">Owed</span>
                                    <div class="small fw-semibold text-danger">
                                        TZS {{ number_format($supplier->balance, 0) }}
                                    </div>
                                @else
                                    <span class="badge bg-success">Clear</span>
                                    <div class="small text-muted">TZS 0</div>
                                @endif
                            </td>
                            <td>
                                <!-- record payment -->
                                @if($supplier->balance > 0)
                                        <a href="{{ route('suppliers.payment', $supplier) }}" class="btn btn-sm btn-success">
                                            Pay
                                        </a>
                                    @endif
                                <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-outline-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Delete this supplier?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No suppliers yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($suppliers->hasPages())
        <div class="card-footer bg-white">{{ $suppliers->links() }}</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('supplierSearchInput');
    const clearBtn = document.getElementById('clearSearchBtn');

    if (searchInput) {
        // filter rows method
        function filterSupplierfs() {
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
        searchInput.addEventListener('keyup', filterSupplierfs);

        // clear search when X btn is clicked
        if (clearBtn) {
            clearBtn.addEventListener('click', function () {
                searchInput.value = '';
                filterSupplierfs(); // Re-filter
                searchInput.focus(); // return cursor to input
            });
        }
    }
});
</script>
@endpush