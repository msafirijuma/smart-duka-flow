@extends('layouts.app')

@section('title', $title ?? 'Page')

@section('content')
    <div class="text-center py-5">
        <div class="mb-3">
            <i class="bi bi-tools display-4 text-muted"></i>
        </div>
        <h4 class="fw-bold">{{ $title ?? 'Page' }}</h4>
        <p class="text-muted">This module is under construction. Coming soon.</p>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-primary btn-sm mt-2">
            ← Back to Dashboard
        </a>
    </div>
@endsection