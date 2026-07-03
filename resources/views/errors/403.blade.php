@extends('layouts.app')
@section('title', '403 Forbidden')
@section('page-title', 'Access Denied')

@section('content')
<div class="text-center py-5">
    <div style="font-size:5rem; line-height:1">&#128274;</div>
    <h2 class="fw-bold mb-3 mt-2">Access Denied</h2>
    <p class="text-muted mb-4 mx-auto" style="max-width:400px">You don't have permission to access this resource. Please contact your administrator if you believe this is an error.</p>
    <a href="{{ route('dashboard') }}" class="btn btn-primary text-white px-4">
        <i class="bi bi-house me-2"></i>Back to Dashboard
    </a>
</div>
@endsection
