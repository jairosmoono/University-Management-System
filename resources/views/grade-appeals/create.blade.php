@extends('layouts.app')
@section('title', 'Submit Grade Appeal')
@section('page-title', 'Submit Grade Appeal')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-flag me-2" style="color:var(--secondary)"></i>Submit Grade Appeal</h1>
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('academic.grade-appeals.index') }}">Grade Appeals</a></li>
        <li class="breadcrumb-item active">Submit</li>
    </ol></nav>
</div>

<div class="row justify-content-center">
<div class="col-lg-7">
<div class="card">
    <div class="card-header py-3">
        <h5 class="mb-0 fw-semibold">Appeal a Grade</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-info" style="font-size:0.85rem">
            <i class="bi bi-info-circle me-2"></i>
            You may only appeal a result once. Ensure you provide a clear and detailed reason.
            Appeals are reviewed by the academic registrar.
        </div>

        <form action="{{ route('academic.grade-appeals.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label class="form-label fw-semibold">Select Result to Appeal <span class="text-danger">*</span></label>
                <select name="final_result_id" class="form-select @error('final_result_id') is-invalid @enderror" required>
                    <option value="">— Choose a course result —</option>
                    @foreach($results as $result)
                    <option value="{{ $result->id }}" {{ old('final_result_id') == $result->id ? 'selected' : '' }}>
                        {{ $result->courseOffering->course->name }}
                        ({{ $result->courseOffering->semester->name ?? 'N/A' }})
                        — Grade: {{ $result->grade }} ({{ $result->total_score }}%)
                    </option>
                    @endforeach
                </select>
                @error('final_result_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                @if($results->isEmpty())
                <div class="text-muted mt-2" style="font-size:0.83rem">No eligible results. Results with pending appeals are excluded.</div>
                @endif
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Reason for Appeal <span class="text-danger">*</span></label>
                <textarea name="reason" rows="6" class="form-control @error('reason') is-invalid @enderror"
                    placeholder="Describe in detail why you believe your grade should be reviewed. Include specific concerns about marking, assessment criteria, or any extenuating circumstances." required>{{ old('reason') }}</textarea>
                @error('reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="text-muted mt-1" style="font-size:0.78rem">Minimum 30 characters.</div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Supporting Document <span class="text-muted fw-normal">(Optional)</span></label>
                <input type="file" name="supporting_document" class="form-control @error('supporting_document') is-invalid @enderror"
                       accept=".pdf,.jpg,.jpeg,.png">
                @error('supporting_document')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="text-muted mt-1" style="font-size:0.78rem">PDF or image, max 5MB. E.g. doctor's note, scripts, correspondence.</div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-send me-1"></i>Submit Appeal</button>
                <a href="{{ route('academic.grade-appeals.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
</div>
</div>
@endsection
