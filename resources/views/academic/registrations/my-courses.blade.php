@extends('layouts.app')
@section('title', 'My Courses')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">My Courses</h4>
        <p class="text-muted mb-0">{{ optional($semester)->name }}</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show"><i class="bi bi-exclamation-triangle me-2"></i>{{ session('warning') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(!$registrationOpen)
    <div class="alert alert-warning"><i class="bi bi-lock-fill me-2"></i><strong>Course registration is currently closed.</strong> Waitlist confirmations are disabled until it is reopened by an administrator.</div>
@endif

{{-- Waitlist notice --}}
@if($waitlistEntries->isNotEmpty())
<div class="card border-warning mb-4">
    <div class="card-header bg-warning bg-opacity-10 py-2 d-flex align-items-center justify-content-between">
        <span class="fw-semibold"><i class="bi bi-hourglass-split me-2 text-warning"></i>Waitlist ({{ $waitlistEntries->count() }})</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Course</th><th>Code</th><th>Position</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @foreach($waitlistEntries as $wl)
                <tr>
                    <td style="font-size:0.88rem">{{ optional(optional($wl->courseOffering)->course)->name }}</td>
                    <td><code>{{ optional(optional($wl->courseOffering)->course)->code }}</code></td>
                    <td><span class="badge bg-secondary">#{{ $wl->position }}</span></td>
                    <td>
                        @if($wl->status === 'notified')
                            <span class="badge bg-success">Spot Available!</span>
                        @else
                            <span class="badge bg-warning text-dark">Waiting</span>
                        @endif
                    </td>
                    <td>
                        @if($wl->status === 'notified')
                        <form action="{{ route('academic.registrations.waitlist.confirm', $wl) }}" method="POST" onsubmit="return confirm('Confirm enrollment in this course?')">
                            @csrf
                            <button class="btn btn-sm btn-success" {{ !$registrationOpen ? 'disabled' : '' }}>Confirm Enrollment</button>
                        </form>
                        @if(!$registrationOpen)
                        <div class="text-danger" style="font-size:0.75rem">Registration is closed.</div>
                        @endif
                        @else
                        <span class="text-muted" style="font-size:0.8rem">You'll be notified when a spot opens.</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
        <h6 class="mb-0">Registered Courses</h6>
        <small class="text-muted">Contact the registrar to add or change your courses.</small>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th>Course</th><th>Code</th><th>Credits</th><th>Lecturer</th><th>Schedule</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($registrations as $reg)
                    <tr>
                        <td>{{ optional(optional($reg->courseOffering)->course)->name }}</td>
                        <td><code>{{ optional(optional($reg->courseOffering)->course)->code }}</code></td>
                        <td>{{ optional(optional($reg->courseOffering)->course)->credits }}</td>
                        <td>{{ optional(optional(optional($reg->courseOffering)->lecturer)->user)->name ?? '—' }}</td>
                        <td>
                            @foreach(optional($reg->courseOffering)->timetables ?? [] as $tt)
                                <small class="d-block">{{ ucfirst($tt->day_of_week) }} {{ $tt->start_time }}–{{ $tt->end_time }}</small>
                            @endforeach
                        </td>
                        @php $regColors = ['registered'=>'success','completed'=>'info','dropped'=>'danger','failed'=>'dark'] @endphp
                        <td><span class="badge bg-{{ $regColors[$reg->status] ?? 'secondary' }}">{{ ucfirst($reg->status) }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No courses registered for this semester/term. Contact the registrar's office.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
