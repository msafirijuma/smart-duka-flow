@extends('layouts.app')
@section('title', 'Categories')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Categories</h4>
    <a href="{{ route('categories.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg"></i> Add Category
    </a>
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
                            id="categoriesSearchInput" 
                            class="form-control border-secondary ps-5 pe-5" 
                            placeholder="Search category by name, status or description..." 
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
            <table class="table table-striped table-hover table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 30px; min-width: 30px">#</th>
                        <th style="width: 80px; min-width: 80px">Name</th>
                        <th style="width: 150px; min-width: 150px">Description</th>
                        <th style="width: 80px; min-width: 80px">Status</th>
                        <th style="width: 80px; min-width: 80px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-semibold">{{ $category->name }}</td>
                            <td>{{ $category->description ?? '—' }}</td>
                            <td>
                                <span class="badge {{ $category->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $category->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this category?')">
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
                            <td colspan="5" class="text-center text-muted py-4">
                                No categories found. <a class="text-decoration-none" href="{{ route('categories.create') }}">Add your first category</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('categoriesSearchInput');
    const clearBtn = document.getElementById('clearSearchBtn');

    if (searchInput) {
        // filter rows method
        function filterCategories() {
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
        searchInput.addEventListener('keyup', filterCategories);

        // clear search when X btn is clicked
        if (clearBtn) {
            clearBtn.addEventListener('click', function () {
                searchInput.value = '';
                filterCategories(); // Re-filter
                searchInput.focus(); // return cursor to input
            });
        }
    }
});
</script>
@endpush

