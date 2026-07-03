@extends('layouts.app')
@section('title', 'Add Department')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Add Department</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('academic.departments.index') }}">Departments</a></li>
            <li class="breadcrumb-item active">Add</li>
        </ol></nav>
    </div>
</div>
<div class="card border-0 shadow-sm" style="max-width:600px">
    <div class="card-body">
        <form action="{{ route('academic.departments.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Faculty <span class="text-danger">*</span></label>
                <select name="faculty_id" class="form-select @error('faculty_id') is-invalid @enderror" required>
                    <option value="">— Select Faculty —</option>
                    @foreach($faculties as $faculty)
                        <option value="{{ $faculty->id }}" {{ old('faculty_id') == $faculty->id ? 'selected' : '' }}>{{ $faculty->name }}</option>
                    @endforeach
                </select>
                @error('faculty_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Code <span class="text-danger">*</span></label>
                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}" required>
                @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Head of Department (HOD)</label>
                <select name="hod_id" class="form-select">
                    <option value="">— None / To Be Assigned —</option>
                    @foreach($staff as $s)
                    @php $sName = optional($s->user)->name ?: 'Staff #'.$s->id; @endphp
                        <option value="{{ $s->id }}" {{ old('hod_id') == $s->id ? 'selected' : '' }}>
                            {{ $sName }}{{ $s->designation ? ' — '.$s->designation : '' }}
                        </option>
                    @endforeach
                </select>
                <div class="form-text">Optional — can be assigned later.</div>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Save Department</button>
                <a href="{{ route('academic.departments.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
