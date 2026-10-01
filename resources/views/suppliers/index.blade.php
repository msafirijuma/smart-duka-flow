@extends('layouts.app')
@section('title', 'Suppliers')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Suppliers</h4>
    <a href="{{ route('suppliers.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg"></i> Add Supplier
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Balance (Debt)</th>
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
                            <td colspan="4" class="text-center text-muted py-4">No suppliers yet.</td>
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