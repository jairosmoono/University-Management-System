@extends('layouts.app')
@section('title', 'SMS Notifications')
@section('page-title', 'SMS Notifications')

@section('content')
<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h1><i class="bi bi-phone me-2" style="color:var(--secondary)"></i>SMS Notifications</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">SMS Notifications</li>
        </ol></nav>
    </div>
</div>

@if(!$configured)
<div class="alert alert-warning d-flex align-items-start gap-3 mb-4">
    <i class="bi bi-exclamation-triangle-fill fs-4 mt-1 flex-shrink-0"></i>
    <div>
        <strong>SMS gateway not fully configured.</strong>
        Driver is set to <code>{{ $driver }}</code> but credentials are missing.<br>
        <span class="text-muted" style="font-size:0.88rem">
            In <code>.env</code>, set <code>SMS_DRIVER=africastalking</code>, <code>SMS_USERNAME</code>, <code>SMS_API_KEY</code>,
            and optionally <code>SMS_SENDER_ID</code>.<br>
            Until configured, messages will be written to the <strong>Laravel log</strong> instead of delivered.
        </span>
    </div>
</div>
@endif

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-4">

    {{-- Compose Panel --}}
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header py-3">
                <h5 class="mb-0 fw-semibold"><i class="bi bi-chat-dots me-2"></i>Compose SMS</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.sms-notifications.send') }}" method="POST" id="smsForm">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Recipients</label>
                        <select name="recipient_type" id="recipientType" class="form-select" onchange="toggleOptions(this.value)">
                            <option value="all">All Active Users (with phone)</option>
                            <option value="students">All Students</option>
                            <option value="staff">All Staff</option>
                            <option value="department">By Department</option>
                            <option value="role">By Role</option>
                            <option value="individual">Specific Users (by ID)</option>
                        </select>
                    </div>

                    <div class="mb-3 d-none" id="deptOption">
                        <label class="form-label fw-semibold">Department</label>
                        <select name="department_id" class="form-select">
                            <option value="">Select department…</option>
                            @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3 d-none" id="roleOption">
                        <label class="form-label fw-semibold">Role</label>
                        <select name="role" class="form-select">
                            <option value="">Select role…</option>
                            @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ ucfirst(str_replace('-', ' ', $role->name)) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3 d-none" id="individualOption">
                        <label class="form-label fw-semibold">User IDs <small class="text-muted">(comma-separated)</small></label>
                        <input type="text" name="user_ids" class="form-control" placeholder="e.g. 5, 12, 34">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold d-flex justify-content-between">
                            Message
                            <span class="text-muted fw-normal" id="charCount" style="font-size:0.8rem">0 / 160</span>
                        </label>
                        <textarea name="message" id="smsMessage" rows="5" maxlength="160"
                                  class="form-control @error('message') is-invalid @enderror"
                                  placeholder="Type your SMS message (max 160 characters)…"
                                  oninput="updateCount(this)">{{ old('message') }}</textarea>
                        @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="text-muted mt-1" style="font-size:0.78rem">
                            Only users with a phone number on record will receive SMS.
                        </div>
                    </div>

                    <div class="alert alert-info py-2 mb-3" style="font-size:0.83rem">
                        <i class="bi bi-info-circle me-1"></i>
                        Only users who have <strong>SMS enabled</strong> for their notification type in preferences will receive this message.
                    </div>

                    <button type="submit" class="btn btn-success w-100" id="sendBtn">
                        <i class="bi bi-send me-1"></i>Send SMS
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Sent Log --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                <h5 class="mb-0 fw-semibold"><i class="bi bi-clock-history me-2"></i>Sent Log</h5>
                <span class="badge bg-secondary">{{ $logs->total() }} total</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Message</th>
                            <th>Recipients</th>
                            <th>Sent / Failed</th>
                            <th>Status</th>
                            <th>Sent By</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td style="max-width:220px">
                                <div class="text-truncate" style="font-size:0.88rem" title="{{ $log->message }}">
                                    {{ $log->message }}
                                </div>
                            </td>
                            <td style="font-size:0.83rem">{{ $log->recipient_label }}</td>
                            <td style="font-size:0.85rem">
                                <span class="text-success fw-semibold">{{ $log->sent_count }}</span>
                                @if($log->failed_count)
                                / <span class="text-danger">{{ $log->failed_count }}</span>
                                @endif
                            </td>
                            <td>
                                @if($log->status === 'sent')
                                    <span class="badge bg-success">Sent</span>
                                @elseif($log->status === 'partial')
                                    <span class="badge bg-warning text-dark">Partial</span>
                                @else
                                    <span class="badge bg-danger">Failed</span>
                                @endif
                            </td>
                            <td style="font-size:0.83rem">{{ $log->sender?->name ?? '—' }}</td>
                            <td style="font-size:0.8rem;white-space:nowrap" class="text-muted">
                                {{ $log->created_at->format('d M Y') }}<br>
                                {{ $log->created_at->format('H:i') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="bi bi-phone-flip fs-3 d-block mb-2 opacity-25"></i>
                                No SMS messages sent yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($logs->hasPages())
            <div class="card-footer d-flex align-items-center justify-content-between py-2">
                <div class="text-muted" style="font-size:0.85rem">
                    Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ $logs->total() }}
                </div>
                {{ $logs->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function toggleOptions(val) {
    ['deptOption','roleOption','individualOption'].forEach(id => {
        document.getElementById(id).classList.add('d-none');
    });
    if (val === 'department') document.getElementById('deptOption').classList.remove('d-none');
    if (val === 'role')       document.getElementById('roleOption').classList.remove('d-none');
    if (val === 'individual') document.getElementById('individualOption').classList.remove('d-none');
}

function updateCount(el) {
    document.getElementById('charCount').textContent = el.value.length + ' / 160';
}

// Init count
document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('smsMessage');
    if (el) updateCount(el);
});

document.getElementById('smsForm').addEventListener('submit', function() {
    document.getElementById('sendBtn').disabled = true;
    document.getElementById('sendBtn').innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending…';
});
</script>
@endsection
