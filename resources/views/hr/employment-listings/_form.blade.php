@php $p = $prefix ?? ''; @endphp
<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label">Job Title <span class="text-danger">*</span></label>
        <input type="text" name="title" id="{{ $p }}title" class="form-control" required maxlength="255">
    </div>
    <div class="col-md-4">
        <label class="form-label">Vacancies <span class="text-danger">*</span></label>
        <input type="number" name="vacancies" id="{{ $p }}vacancies" class="form-control" required min="1" value="1">
    </div>
    <div class="col-md-4">
        <label class="form-label">Department</label>
        <select name="department_id" id="{{ $p }}department_id" class="form-select">
            <option value="">Any Department</option>
            @foreach($departments as $dept)
            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Employment Type <span class="text-danger">*</span></label>
        <select name="employment_type" id="{{ $p }}employment_type" class="form-select" required>
            <option value="full-time">Full-Time</option>
            <option value="part-time">Part-Time</option>
            <option value="contract">Contract</option>
            <option value="internship">Internship</option>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Status <span class="text-danger">*</span></label>
        <select name="status" id="{{ $p }}status" class="form-select" required>
            <option value="draft">Draft</option>
            <option value="open" selected>Open</option>
            <option value="closed">Closed</option>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Application Deadline</label>
        <input type="date" name="deadline" id="{{ $p }}deadline" class="form-control">
    </div>
    <div class="col-12">
        <label class="form-label">Job Description</label>
        <textarea name="description" id="{{ $p }}description" class="form-control" rows="3"></textarea>
    </div>
    <div class="col-12">
        <label class="form-label">Requirements</label>
        <textarea name="requirements" id="{{ $p }}requirements" class="form-control" rows="3"
                  placeholder="Qualifications, experience, skills…"></textarea>
    </div>
</div>
