@extends('layouts.app')
@section('title', 'Edit Hostel')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Edit Hostel</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('hostel.hostels.index') }}">Hostels</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol></nav>
    </div>
</div>
<div class="card border-0 shadow-sm" style="max-width:600px">
    <div class="card-body">
        <form action="{{ route('hostel.hostels.update', $hostel) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">Hostel Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $hostel->name) }}" required>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select">
                        @foreach(['male','female','mixed'] as $g)
                            <option value="{{ $g }}" {{ old('type', $hostel->type) === $g ? 'selected' : '' }}>{{ ucfirst($g) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="active" {{ old('status', $hostel->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $hostel->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Warden</label>
                <select name="warden_id" class="form-select">
                    <option value="">— None —</option>
                    @foreach($wardens as $w)
                        <option value="{{ $w->id }}" {{ old('warden_id', $hostel->warden_id) == $w->id ? 'selected' : '' }}>{{ $w->full_name ?? optional($w->user)->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Location / Address</label>
                <input type="text" name="location" class="form-control" value="{{ old('location', $hostel->location) }}">
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Update Hostel</button>
                <a href="{{ route('hostel.hostels.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
