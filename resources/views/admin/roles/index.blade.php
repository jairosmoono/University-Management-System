@extends('layouts.app')
@section('title', 'Roles & Permissions')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Roles & Permissions</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Roles</li>
        </ol></nav>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRoleModal">
        <i class="bi bi-plus-circle me-1"></i> New Role
    </button>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 py-3">
                <h6 class="mb-0 fw-semibold">All Roles</h6>
            </div>
            <div class="list-group list-group-flush">
                @foreach($roles as $role)
                <a href="{{ route('admin.roles.show', $role) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">{{ ucwords(str_replace('-',' ',$role->name)) }}</span>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-info">{{ $role->permissions->count() }} perms</span>
                        <span class="badge bg-secondary">{{ $role->users->count() }} users</span>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        @if(isset($selectedRole))
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center py-3">
                <h6 class="mb-0 fw-semibold">Permissions for: <span class="text-primary">{{ ucwords(str_replace('-',' ',$selectedRole->name)) }}</span></h6>
                <small class="text-muted">{{ $selectedRole->permissions->count() }} permissions assigned</small>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.roles.sync-permissions', $selectedRole) }}">
                    @csrf
                    @foreach($permissionGroups as $group => $groupPermissions)
                    <div class="mb-4">
                        <h6 class="fw-semibold text-primary border-bottom pb-2 mb-3">{{ ucwords($group) }}</h6>
                        <div class="row g-2">
                            @foreach($groupPermissions as $permission)
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->name }}" id="perm_{{ $permission->id }}"
                                        {{ $selectedRole->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="perm_{{ $permission->id }}">
                                        {{ str_replace('-',' ', $permission->name) }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                    <button type="submit" class="btn btn-primary">Save Permissions</button>
                </form>
            </div>
        </div>
        @else
        <div class="card border-0 shadow-sm d-flex align-items-center justify-content-center" style="min-height:300px">
            <div class="text-center text-muted">
                <i class="bi bi-shield-check display-3"></i>
                <p class="mt-2">Select a role to manage its permissions</p>
            </div>
        </div>
        @endif
    </div>
</div>

<div class="modal fade" id="createRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.roles.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Create Role</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Role Name *</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. finance-officer" required>
                        <small class="text-muted">Use lowercase with hyphens (e.g. finance-officer)</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Guard</label>
                        <select name="guard_name" class="form-select">
                            <option value="web">Web</option>
                            <option value="sanctum">API (Sanctum)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Role</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
