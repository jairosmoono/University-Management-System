@extends('layouts.app')
@section('title', '429 Too Many Requests')
@section('page-title', 'Too Many Requests')

@section('content')
<div class="text-center py-5">
    <div style="font-size:5rem; line-height:1">&#128683;</div>
    <h2 class="fw-bold mb-3 mt-2">Too Many Requests</h2>
    <p class="text-muted mb-4 mx-auto" style="max-width:400px">You have made too many requests in a short period. Please wait a moment before trying again.</p>
    <a href="{{ route('dashboard') }}" class="btn btn-primary text-white px-4">
        <i class="bi bi-house me-2"></i>Back to Dashboard
    </a>
</div>
@endsection
