@extends('layouts.app')
@section('title', 'Holds — ' . $student->full_name)
@section('page-title', 'Student Holds')

@section('content')
<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h1><i class="bi bi-slash-circle me-2" style="color:var(--secondary)"></i>Holds: {{ $student->full_name }}</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('students.show', $student) }}">{{ $student->student_id }}</a></li>
            <li class="breadcrumb-item active">Holds</li>
        </ol></nav>
    </div>
    <a href="{{ route('academic.student-holds.index') }}" class="btn btn-outline-secondary btn-sm">All Holds</a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header py-3"><h5 class="mb-0 fw-semibold">Place New Hold</h5></div>
            <div class="card-body">
                <form action="{{ route('academic.student-holds.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Hold Type</label>
                        <select name="type" class="form-select" required>
                            @foreach($types as $t)
                            <option value="{{ $t }}">{{ \App\Models\StudentHold::typeLabel($t) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Reason</label>
                        <textarea name="reason" rows="3" class="form-control" required></textarea>
                    </div>
                    <div class="mb-3 form-check">
                        <input class="form-check-input" type="checkbox" name="blocks_registration" value="1" id="blocksReg" checked>
                        <label class="form-check-label" for="blocksReg">Block Course Registration</label>
                    </div>
                    <button type="submit" class="btn btn-danger w-100"><i class="bi bi-slash-circle me-1"></i>Place Hold</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header py-3"><h5 class="mb-0 fw-semibold">Hold History</h5></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light"><tr><th>Type</th><th>Reason</th><th>Placed</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        @forelse($holds as $hold)
                        <tr>
                            <td><span class="badge {{ \App\Models\StudentHold::typeBadgeClass($hold->type) }}">{{ \App\Models\StudentHold::typeLabel($hold->type) }}</span></td>
                            <td style="font-size:0.85rem">{{ $hold->reason }}</td>
                            <td style="font-size:0.82rem">{{ $hold->placedBy?->name }}<br><span class="text-muted">{{ $hold->created_at->format('d M Y') }}</span></td>
                            <td>
                                @if($hold->is_active)
                                    <span class="badge bg-danger">Active</span>
                                @else
                                    <span class="badge bg-success">Released</span>
                                    <div class="text-muted" style="font-size:0.75rem">{{ $hold->released_at?->format('d M Y') }}</div>
                                @endif
                            </td>
                            <td>
                                @if($hold->is_active)
                                <form action="{{ route('academic.student-holds.release', $hold) }}" method="POST" onsubmit="return confirm('Release this hold?')">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-success">Release</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No holds on this student.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
