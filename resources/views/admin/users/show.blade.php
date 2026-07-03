@extends('layouts.app')
@section('title', $user->name)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">{{ $user->name }}</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
            <li class="breadcrumb-item active">{{ $user->name }}</li>
        </ol></nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil me-1"></i>Edit</a>
        <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="d-inline">
            @csrf
            <button class="btn btn-sm btn-outline-{{ $user->is_active ? 'warning' : 'success' }}">
                {{ $user->is_active ? 'Deactivate' : 'Activate' }}
            </button>
        </form>
    </div>
</div>
<div class="row g-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center pt-4">
                <div class="avatar-lg bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:80px;height:80px;font-size:2rem">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <h5 class="mb-1">{{ $user->name }}</h5>
                <p class="text-muted mb-2">{{ $user->email }}</p>
                @foreach($user->roles as $role)
                    <span class="badge bg-primary me-1">{{ $role->name }}</span>
                @endforeach
                <div class="mt-2">
                    <span class="badge bg-{{ $user->is_active ? 'success' : 'secondary' }}">{{ $user->is_active ? 'Active' : 'Inactive' }}</span>
                </div>
                <hr>
                <dl class="row text-start mb-0">
                    <dt class="col-5 text-muted fw-normal">Phone</dt><dd class="col-7">{{ $user->phone ?? '—' }}</dd>
                    <dt class="col-5 text-muted fw-normal">Joined</dt><dd class="col-7">{{ $user->created_at->format('d M Y') }}</dd>
                    <dt class="col-5 text-muted fw-normal">Last Login</dt><dd class="col-7">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : '—' }}</dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        @if($user->student)
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <h6 class="text-muted mb-3">Student Profile</h6>
                <dl class="row mb-0">
                    <dt class="col-4 text-muted fw-normal">Student No.</dt><dd class="col-8"><code>{{ $user->student->student_number }}</code></dd>
                    <dt class="col-4 text-muted fw-normal">Program</dt><dd class="col-8">{{ optional($user->student->program)->name }}</dd>
                    <dt class="col-4 text-muted fw-normal">Year</dt><dd class="col-8">{{ $user->student->current_year }}</dd>
                </dl>
            </div>
        </div>
        @endif
        @if($user->staff)
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <h6 class="text-muted mb-3">Staff Profile</h6>
                <dl class="row mb-0">
                    <dt class="col-4 text-muted fw-normal">Staff No.</dt><dd class="col-8"><code>{{ $user->staff->staff_number }}</code></dd>
                    <dt class="col-4 text-muted fw-normal">Department</dt><dd class="col-8">{{ optional($user->staff->department)->name }}</dd>
                    <dt class="col-4 text-muted fw-normal">Position</dt><dd class="col-8">{{ $user->staff->position }}</dd>
                </dl>
            </div>
        </div>
        @endif
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted mb-3">Roles & Permissions</h6>
                @forelse($user->roles as $role)
                <div class="mb-2">
                    <span class="badge bg-primary fs-6">{{ $role->name }}</span>
                    <div class="mt-1">
                        @foreach($role->permissions as $perm)
                            <span class="badge bg-light text-dark me-1 mb-1">{{ $perm->name }}</span>
                        @endforeach
                    </div>
                </div>
                @empty
                <p class="text-muted">No roles assigned.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
