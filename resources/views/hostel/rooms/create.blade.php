@extends('layouts.app')
@section('title', 'Add Room')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Add Hostel Room</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('hostel.rooms.index') }}">Rooms</a></li>
            <li class="breadcrumb-item active">Add</li>
        </ol></nav>
    </div>
</div>
<div class="card border-0 shadow-sm" style="max-width:500px">
    <div class="card-body">
        <form action="{{ route('hostel.rooms.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Hostel <span class="text-danger">*</span></label>
                <select name="hostel_id" class="form-select @error('hostel_id') is-invalid @enderror" required>
                    <option value="">— Select Hostel —</option>
                    @foreach($hostels as $h)
                        <option value="{{ $h->id }}" {{ old('hostel_id') == $h->id ? 'selected' : '' }}>{{ $h->name }}</option>
                    @endforeach
                </select>
                @error('hostel_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Room Number <span class="text-danger">*</span></label>
                <input type="text" name="room_number" class="form-control @error('room_number') is-invalid @enderror" value="{{ old('room_number') }}" required>
                @error('room_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Type <span class="text-danger">*</span></label>
                    <select name="type" class="form-select" required>
                        @foreach(['single','double','triple','quad','dormitory'] as $t)
                            <option value="{{ $t }}" {{ old('type') === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Capacity <span class="text-danger">*</span></label>
                    <input type="number" name="capacity" class="form-control @error('capacity') is-invalid @enderror" value="{{ old('capacity', 1) }}" min="1" required>
                    @error('capacity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Flat</label>
                <input type="text" name="floor" class="form-control" value="{{ old('floor') }}" placeholder="e.g. Ground, 1st">
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Add Room</button>
                <a href="{{ route('hostel.rooms.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
