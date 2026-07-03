@extends('layouts.app')
@section('title', $scholarship->name)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">{{ $scholarship->name }}</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('finance.scholarships.index') }}">Scholarships</a></li>
            <li class="breadcrumb-item active">{{ $scholarship->name }}</li>
        </ol></nav>
    </div>
    <a href="{{ route('finance.scholarships.edit', $scholarship) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil me-1"></i>Edit</a>
</div>
<div class="row g-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5 text-muted fw-normal">Type</dt><dd class="col-7"><span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $scholarship->type)) }}</span></dd>
                    <dt class="col-5 text-muted fw-normal">Amount</dt><dd class="col-7 fw-bold">ZMW {{ number_format($scholarship->amount, 2) }}</dd>
                    <dt class="col-5 text-muted fw-normal">Academic Yr</dt><dd class="col-7">{{ optional($scholarship->academicYear)->name ?? '—' }}</dd>
                    <dt class="col-5 text-muted fw-normal">Awards</dt><dd class="col-7">{{ $scholarship->awards->count() }}</dd>
                </dl>
                @if($scholarship->eligibility_criteria)
                <hr><h6 class="small text-muted">Eligibility</h6>
                <p class="small mb-0">{{ $scholarship->eligibility_criteria }}</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent"><h6 class="mb-0">Award Recipients</h6></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th>Student</th><th>ID</th><th>Year</th><th>Amount</th><th>Status</th></tr></thead>
                        <tbody>
                            @forelse($scholarship->awards as $award)
                            <tr>
                                <td>{{ optional($award->student)->full_name }}</td>
                                <td><code>{{ optional($award->student)->student_number }}</code></td>
                                <td>{{ optional($award->academicYear)->name }}</td>
                                <td>ZMW {{ number_format($award->amount, 2) }}</td>
                                <td><span class="badge bg-{{ $award->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($award->status) }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-3">No awards yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
