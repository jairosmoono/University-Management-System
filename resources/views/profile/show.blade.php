@extends('layouts.app')
@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-person-circle me-2" style="color:var(--secondary)"></i>My Profile</h1>
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">My Profile</li>
    </ol></nav>
</div>

<div class="row g-3">
    <!-- Profile Card -->
    <div class="col-lg-4">
        <div class="card text-center p-4">
            <img src="{{ auth()->user()->avatar_url }}" class="rounded-circle mx-auto mb-3"
                 style="width:100px;height:100px;object-fit:cover;border:4px solid #dee2e6" alt="">
            <h5 class="fw-bold mb-1">{{ auth()->user()->name }}</h5>
            <div class="text-muted mb-2" style="font-size:0.85rem">{{ auth()->user()->email }}</div>
            <div class="mt-1 mb-3">
                <span class="badge bg-primary px-3 py-2">{{ ucwords(str_replace('-', ' ', auth()->user()->full_role)) }}</span>
            </div>
            <hr>
            <form action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="avatar" id="avatar" class="d-none" accept="image/*" onchange="this.form.submit()">
                <label for="avatar" class="btn btn-outline-secondary btn-sm w-100 mb-2">
                    <i class="bi bi-camera me-1"></i>Change Photo
                </label>
            </form>
            <div class="text-muted mt-1" style="font-size:0.75rem">JPG, PNG. Max 2MB</div>
        </div>

        <!-- Account Info -->
        <div class="card mt-3 p-3">
            <h6 class="fw-semibold mb-3"><i class="bi bi-info-circle me-2 text-primary"></i>Account Information</h6>
            <div style="font-size:0.85rem">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Member Since</span>
                    <strong>{{ auth()->user()->created_at->format('M Y') }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Last Login</span>
                    <strong>{{ auth()->user()->last_login_at?->diffForHumans() ?? 'N/A' }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Status</span>
                    <span class="badge bg-success">Active</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Forms -->
    <div class="col-lg-8">
        <!-- Profile Information -->
        <div class="card mb-3">
            <div class="card-header py-3 border-bottom">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-person me-2"></i>Profile Information</h6>
            </div>
            <div class="card-body">
                @if(session('profile_updated'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle me-2"></i>Profile updated successfully.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', auth()->user()->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', auth()->user()->email) }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone Number</label>
                            <input type="text" name="phone" class="form-control"
                                   value="{{ old('phone', auth()->user()->phone) }}" placeholder="+260 XXX XXXXXX">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Alternative Email</label>
                            <input type="email" name="alt_email" class="form-control"
                                   value="{{ old('alt_email', auth()->user()->alt_email) }}" placeholder="Optional">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Address</label>
                            <textarea name="address" class="form-control" rows="2">{{ old('address', auth()->user()->address) }}</textarea>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary text-white">
                            <i class="bi bi-save me-1"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Change Password -->
        <div class="card">
            <div class="card-header py-3 border-bottom">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-key me-2"></i>Change Password</h6>
            </div>
            <div class="card-body">
                @if(session('password_updated'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle me-2"></i>Password changed successfully.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif
                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Current Password <span class="text-danger">*</span></label>
                            <input type="password" name="current_password"
                                   class="form-control @error('current_password') is-invalid @enderror" required>
                            @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">New Password <span class="text-danger">*</span></label>
                            <input type="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-warning text-white">
                            <i class="bi bi-key me-1"></i>Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
