@extends('layouts.app')
@section('title', isset($announcement) ? 'Edit Announcement' : 'Create Announcement')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">{{ isset($announcement) ? 'Edit' : 'Create' }} Announcement</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('announcements.index') }}">Announcements</a></li>
            <li class="breadcrumb-item active">{{ isset($announcement) ? 'Edit' : 'Create' }}</li>
        </ol></nav>
    </div>
</div>

<form method="POST" action="{{ isset($announcement) ? route('announcements.update', $announcement) : route('announcements.store') }}" enctype="multipart/form-data">
    @csrf
    @if(isset($announcement)) @method('PUT') @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Title *</label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $announcement->title ?? '') }}" required>
                        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Content *</label>
                        <textarea name="content" class="form-control @error('content') is-invalid @enderror" rows="10" required>{{ old('content', $announcement->content ?? '') }}</textarea>
                        @error('content') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Attachments</label>
                        <input type="file" name="attachments[]" class="form-control" multiple accept=".pdf,.doc,.docx,.jpg,.png">
                        <small class="text-muted">Supported: PDF, Word documents, Images</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-transparent border-0 py-3">
                    <h6 class="mb-0 fw-semibold">Settings</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Category *</label>
                        <select name="category" class="form-select" required>
                            @foreach(['general'=>'General','academic'=>'Academic','event'=>'Event','finance'=>'Finance','emergency'=>'Emergency','urgent'=>'Urgent'] as $val => $lbl)
                            <option value="{{ $val }}" {{ old('category', $announcement->category ?? 'general') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Priority</label>
                        <select name="priority" class="form-select">
                            <option value="normal" {{ old('priority', $announcement->priority ?? 'normal') == 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="high" {{ old('priority', $announcement->priority ?? '') == 'high' ? 'selected' : '' }}>High</option>
                            <option value="urgent" {{ old('priority', $announcement->priority ?? '') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Target Audience</label>
                        <div>
                            @foreach(['all'=>'Everyone','students'=>'Students Only','staff'=>'Staff Only','lecturers'=>'Lecturers Only'] as $value => $label)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="target_audience" value="{{ $value }}" id="audience_{{ $value }}"
                                    {{ old('target_audience', $announcement->target_audience ?? 'all') == $value ? 'checked' : '' }}>
                                <label class="form-check-label" for="audience_{{ $value }}">{{ $label }}</label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Publish Date</label>
                        <input type="datetime-local" name="publish_date" class="form-control" value="{{ old('publish_date', isset($announcement) ? \Carbon\Carbon::parse($announcement->publish_date)->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Expiry Date</label>
                        <input type="datetime-local" name="expiry_date" class="form-control" value="{{ old('expiry_date', isset($announcement) && $announcement->expiry_date ? \Carbon\Carbon::parse($announcement->expiry_date)->format('Y-m-d\TH:i') : '') }}">
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="is_published" value="1" id="is_published"
                            {{ old('is_published', $announcement->is_published ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="is_published">Publish immediately</label>
                        <div class="text-muted small">Uncheck to save as draft</div>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="bi bi-send me-1"></i>
                    {{ isset($announcement) ? 'Update Announcement' : 'Save Announcement' }}
                </button>
                <a href="{{ route('announcements.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </div>
    </div>
</form>
@push('scripts')
<script>
(function () {
    const cb  = document.getElementById('is_published');
    const btn = document.getElementById('submitBtn');
    if (!cb || !btn) return;
    const isEdit = {{ isset($announcement) ? 'true' : 'false' }};

    function updateLabel() {
        if (isEdit) {
            btn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Update Announcement';
        } else if (cb.checked) {
            btn.innerHTML = '<i class="bi bi-send me-1"></i> Publish Now';
        } else {
            btn.innerHTML = '<i class="bi bi-floppy me-1"></i> Save as Draft';
        }
    }

    cb.addEventListener('change', updateLabel);
    updateLabel();
})();
</script>
@endpush
@endsection
