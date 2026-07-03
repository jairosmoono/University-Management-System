@extends('layouts.auth')
@section('title', 'Forgot Password')
@section('content')
@if(!empty($__uni['logo_path']))
<div class="mb-3">
    <img src="{{ asset('storage/' . $__uni['logo_path']) }}" alt="Logo"
         style="height:56px;max-width:60px;object-fit:contain;border-radius:12px">
</div>
@else
<div class="auth-logo" style="font-size:1.6rem">&#128273;</div>
@endif
<h2 class="fw-700 mb-1" style="font-size:1.6rem; font-weight:700">Reset Password</h2>
<p class="text-muted mb-4">Enter your email and we'll send you a reset link.</p>
@if(session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
<form method="POST" action="{{ route('password.email') }}">
    @csrf
    <div class="mb-3">
        <label class="form-label fw-semibold">Email Address</label>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="you@university.com" value="{{ old('email') }}" required>
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <button type="submit" class="btn btn-primary btn-login text-white">
        <i class="bi bi-envelope me-2"></i>Send Reset Link
    </button>
</form>
<div class="mt-3 text-center"><a href="{{ route('login') }}" class="text-muted"><i class="bi bi-arrow-left me-1"></i>Back to Login</a></div>
@endsection
