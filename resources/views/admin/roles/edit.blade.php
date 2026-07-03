@extends('layouts.app')
@section('title', 'Edit Role')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Edit Role: {{ $role->name }}</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol></nav>
    </div>
</div>
<form action="{{ route('admin.roles.update', $role) }}" method="POST">
    @csrf @method('PUT')
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Role Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $role->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $role->description) }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Update Role</button>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Permissions</h6>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="checkAll(true)">Select All</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="checkAll(false)">Clear All</button>
                    </div>
                </div>
                <div class="card-body">
                    @foreach($permissions as $module => $perms)
                    <div class="mb-3">
                        <h6 class="text-uppercase text-muted small fw-semibold mb-2">{{ $module }}</h6>
                        <div class="row g-2">
                            @foreach($perms as $perm)
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input perm-check" type="checkbox" name="permissions[]" value="{{ $perm->name }}" id="perm_{{ $perm->id }}"
                                           {{ in_array($perm->name, old('permissions', $rolePermissions)) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="perm_{{ $perm->id }}">{{ $perm->name }}</label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</form>
@push('scripts')
<script>function checkAll(v){document.querySelectorAll('.perm-check').forEach(c=>c.checked=v)}</script>
@endpush
@endsection
