@extends('layouts.app')
@section('title', 'Edit Asset')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Edit Asset</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('assets.index') }}">Assets</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol></nav>
    </div>
</div>
<div class="card border-0 shadow-sm" style="max-width:600px">
    <div class="card-body">
        <form action="{{ route('assets.update', $asset) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">Asset Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $asset->name) }}" required>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select">
                        @foreach(['furniture','equipment','vehicle','electronics','building','other'] as $c)
                            <option value="{{ $c }}" {{ old('category', $asset->category) === $c ? 'selected' : '' }}>{{ ucfirst($c) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Serial Number</label>
                    <input type="text" name="serial_number" class="form-control" value="{{ old('serial_number', $asset->serial_number) }}">
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        @foreach(['active','maintenance','disposed','lost'] as $s)
                            <option value="{{ $s }}" {{ old('status', $asset->status) === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Current Value</label>
                    <div class="input-group">
                        <span class="input-group-text">ZMW</span>
                        <input type="number" name="current_value" class="form-control" value="{{ old('current_value', $asset->current_value) }}" step="0.01" min="0">
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Location</label>
                <input type="text" name="location" class="form-control" value="{{ old('location', $asset->location) }}">
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Update Asset</button>
                <a href="{{ route('assets.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
