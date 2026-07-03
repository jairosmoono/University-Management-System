@extends('layouts.app')
@section('title', '419 Session Expired')
@section('page-title', 'Session Expired')

@section('content')
<div class="text-center py-5">
    <div style="font-size:5rem; line-height:1">&#9200;</div>
    <h2 class="fw-bold mb-3 mt-2">Session Expired</h2>
    <p class="text-muted mb-4 mx-auto" style="max-width:400px">Your session has expired due to inactivity. Please go back and try your action again.</p>
    <a href="javascript:history.back()" class="btn btn-outline-secondary px-4 me-2">
        <i class="bi bi-arrow-left me-2"></i>Go Back
    </a>
    <a href="{{ route('login') }}" class="btn btn-primary text-white px-4">
        <i class="bi bi-box-arrow-in-right me-2"></i>Log In Again
    </a>
</div>
@endsection
