@extends('layouts.app')
@section('title', 'Edit Room')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Edit Room {{ $room->room_number }}</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('hostel.rooms.index') }}">Rooms</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol></nav>
    </div>
</div>
<div class="card border-0 shadow-sm" style="max-width:500px">
    <div class="card-body">
        <form action="{{ route('hostel.rooms.update', $room) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">Hostel</label>
                <select name="hostel_id" class="form-select" required>
                    @foreach($hostels as $h)
                        <option value="{{ $h->id }}" {{ old('hostel_id', $room->hostel_id) == $h->id ? 'selected' : '' }}>{{ $h->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Room Number</label>
                <input type="text" name="room_number" class="form-control" value="{{ old('room_number', $room->room_number) }}" required>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select">
                        @foreach(['single','double','triple','quad','dormitory'] as $t)
                            <option value="{{ $t }}" {{ old('type', $room->type) === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Capacity</label>
                    <input type="number" name="capacity" class="form-control" value="{{ old('capacity', $room->capacity) }}" min="1">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    @foreach(['available','occupied','full','maintenance'] as $s)
                        <option value="{{ $s }}" {{ old('status', $room->status) === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Update Room</button>
                <a href="{{ route('hostel.rooms.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
