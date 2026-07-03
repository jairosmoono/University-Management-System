@extends('layouts.app')
@section('title', '500 Server Error')
@section('page-title', 'Server Error')

@section('content')
<div class="text-center py-5">
    <div style="font-size:5rem; font-weight:800; color:#dee2e6; line-height:1">500</div>
    <h2 class="fw-bold mb-3 mt-2">Something Went Wrong</h2>
    <p class="text-muted mb-4 mx-auto" style="max-width:400px">An unexpected error occurred on our end. Please try again in a moment. If the problem persists, contact your system administrator.</p>
    <a href="{{ route('dashboard') }}" class="btn btn-primary text-white px-4">
        <i class="bi bi-house me-2"></i>Back to Dashboard
    </a>
</div>
@endsection
