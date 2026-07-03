@extends('layouts.app')
@section('title', 'Timetable')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Timetable</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Timetable</li>
        </ol></nav>
    </div>
    @can('manage-academic')
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSlotModal">
        <i class="bi bi-plus-circle me-1"></i> Add Slot
    </button>
    @endcan
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <select name="program_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Programs</option>
                    @foreach($programs as $prog)
                    <option value="{{ $prog->id }}" {{ request('program_id') == $prog->id ? 'selected' : '' }}>{{ $prog->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="semester_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Semesters/Terms</option>
                    @foreach($semesters as $sem)
                    <option value="{{ $sem->id }}" {{ request('semester_id') == $sem->id ? 'selected' : '' }}>{{ $sem->name }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body table-responsive">
        @php
            $days = ['Monday','Tuesday','Wednesday','Thursday','Friday'];
            $hours = ['08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00'];
            $slotMap = [];
            foreach($timetable as $slot) {
                $day = ucfirst(strtolower($slot->day_of_week));
                $hour = substr($slot->start_time, 0, 5);
                $slotMap[$day][$hour][] = $slot;
            }
        @endphp
        <table class="table table-bordered text-center align-middle" style="min-width:900px">
            <thead>
                <tr class="table-dark">
                    <th style="width:80px">Time</th>
                    @foreach($days as $day)
                    <th>{{ $day }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($hours as $hour)
                <tr>
                    <td class="bg-light fw-semibold small">{{ $hour }}</td>
                    @foreach($days as $day)
                    <td class="p-1" style="min-height:60px">
                        @if(isset($slotMap[$day][$hour]))
                            @foreach($slotMap[$day][$hour] as $slot)
                            <div class="rounded p-1 mb-1 text-white text-start" style="background:var(--primary);font-size:0.75rem">
                                <strong>{{ optional(optional($slot->courseOffering)->course)->code }}</strong><br>
                                <span class="opacity-75">{{ $slot->venue ?? '' }}</span><br>
                                <span class="opacity-75">{{ optional(optional(optional($slot->courseOffering)->lecturer)->user)->name ?? '' }}</span>
                                @can('manage-academic')
                                <form method="POST" action="{{ route('academic.timetable.destroy', $slot) }}" class="d-inline" onsubmit="return confirm('Remove slot?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm p-0 text-white float-end" style="line-height:1"><i class="bi bi-x"></i></button>
                                </form>
                                @endcan
                            </div>
                            @endforeach
                        @endif
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@can('manage-academic')
<div class="modal fade" id="createSlotModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('academic.timetable.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Add Timetable Slot</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Course Offering *</label>
                        <select name="course_offering_id" class="form-select" required>
                            <option value="">Select Course</option>
                            @foreach($offerings as $o)
                            <option value="{{ $o->id }}">{{ optional($o->course)->code }} - {{ optional($o->course)->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Day *</label>
                        <select name="day_of_week" class="form-select" required>
                            @foreach(['monday','tuesday','wednesday','thursday','friday','saturday'] as $d)
                            <option value="{{ $d }}">{{ ucfirst($d) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label">Start Time *</label>
                            <input type="time" name="start_time" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">End Time *</label>
                            <input type="time" name="end_time" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3 mt-3">
                        <label class="form-label">Venue / Room</label>
                        <input type="text" name="venue" class="form-control" placeholder="e.g. LT1, Room 201">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Slot</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endcan
@endsection
