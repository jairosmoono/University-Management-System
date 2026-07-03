@extends('layouts.app')
@section('title', 'New Graduation Ceremony')

@section('content')
<div class="mb-4">
    <h4 class="mb-1">New Graduation Ceremony</h4>
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('graduation.index') }}">Graduation</a></li>
        <li class="breadcrumb-item"><a href="{{ route('graduation.ceremonies.index') }}">Ceremonies</a></li>
        <li class="breadcrumb-item active">New</li>
    </ol></nav>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-4 pb-2 px-4">
                <h6 class="fw-semibold mb-0">Ceremony Details</h6>
            </div>
            <div class="card-body px-4 pb-4">
                <form method="POST" action="{{ route('graduation.ceremonies.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ceremony Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" placeholder="e.g. 35th Graduation Ceremony" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Academic Year <span class="text-danger">*</span></label>
                            <select name="academic_year_id" class="form-select @error('academic_year_id') is-invalid @enderror" required>
                                <option value="">Select…</option>
                                @foreach($academicYears as $ay)
                                <option value="{{ $ay->id }}" {{ old('academic_year_id') == $ay->id ? 'selected' : '' }}>
                                    {{ $ay->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('academic_year_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ceremony Date <span class="text-danger">*</span></label>
                            <input type="date" name="ceremony_date" class="form-control @error('ceremony_date') is-invalid @enderror"
                                   value="{{ old('ceremony_date') }}" required>
                            @error('ceremony_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Venue</label>
                        <input type="text" name="venue" class="form-control" value="{{ old('venue') }}" placeholder="e.g. University Main Hall">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Dress Code</label>
                            <input type="text" name="dress_code" class="form-control" value="{{ old('dress_code') }}" placeholder="e.g. Academic regalia">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Maximum Graduates</label>
                            <input type="number" name="max_graduates" class="form-control" value="{{ old('max_graduates') }}" min="1" placeholder="Leave blank for unlimited">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="planned" {{ old('status','planned') == 'planned' ? 'selected' : '' }}>Planned</option>
                            <option value="confirmed" {{ old('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" rows="3" class="form-control" placeholder="Any additional notes…">{{ old('notes') }}</textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-calendar-plus me-1"></i>Create Ceremony
                        </button>
                        <a href="{{ route('graduation.ceremonies.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
