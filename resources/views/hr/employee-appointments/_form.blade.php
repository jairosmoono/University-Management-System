@php $p = $prefix ?? ''; @endphp
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Employee <span class="text-danger">*</span></label>
        <select name="employee_id" id="{{ $p }}employee_id" class="form-select" required>
            <option value="">— Select Employee —</option>
            @foreach($employees as $emp)
            <option value="{{ $emp->id }}">{{ optional($emp->user)->name ?: 'Employee #'.$emp->id }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Department</label>
        <select name="department_id" id="{{ $p }}department_id" class="form-select">
            <option value="">— Select Department —</option>
            @foreach($departments as $dept)
            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-8">
        <label class="form-label">Position / Title <span class="text-danger">*</span></label>
        <input type="text" name="position" id="{{ $p }}position" class="form-control" required maxlength="255">
    </div>
    <div class="col-md-4">
        <label class="form-label">Contract Type <span class="text-danger">*</span></label>
        <select name="contract_type" id="{{ $p }}contract_type" class="form-select" required>
            <option value="permanent">Permanent</option>
            <option value="contract">Contract</option>
            <option value="probation">Probation</option>
            <option value="acting">Acting</option>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Appointment Date <span class="text-danger">*</span></label>
        <input type="date" name="appointment_date" id="{{ $p }}appointment_date" class="form-control" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Start Date <span class="text-danger">*</span></label>
        <input type="date" name="start_date" id="{{ $p }}start_date" class="form-control" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">End Date</label>
        <input type="date" name="end_date" id="{{ $p }}end_date" class="form-control">
        <div class="form-text">Leave blank for open-ended.</div>
    </div>
    <div class="col-md-4">
        <label class="form-label">Salary</label>
        <input type="number" name="salary" id="{{ $p }}salary" class="form-control" min="0" step="0.01">
    </div>
    <div class="col-md-4">
        <label class="form-label">Status <span class="text-danger">*</span></label>
        <select name="status" id="{{ $p }}status" class="form-select" required>
            <option value="active">Active</option>
            <option value="expired">Expired</option>
            <option value="terminated">Terminated</option>
        </select>
    </div>
    <div class="col-12">
        <label class="form-label">Notes</label>
        <textarea name="notes" id="{{ $p }}notes" class="form-control" rows="2"></textarea>
    </div>
</div>
