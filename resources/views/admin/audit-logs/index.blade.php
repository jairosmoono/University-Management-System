@extends('layouts.app')
@section('title', 'Audit Logs')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Audit Logs</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Audit Logs</li>
        </ol></nav>
    </div>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search action, model..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <input type="date" name="date" class="form-control form-control-sm" value="{{ request('date') }}">
            </div>
            <div class="col-auto"><button class="btn btn-sm btn-outline-secondary" type="submit">Filter</button></div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Time</th><th>User</th><th>Action</th><th>Model</th><th>Record ID</th><th>IP</th></tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td><small>{{ optional($log->created_at)->format('d M Y H:i') ?? '—' }}</small></td>
                        <td>{{ optional($log->user)->name ?? 'System' }}</td>
                        <td><span class="badge bg-{{ $log->action === 'delete' ? 'danger' : ($log->action === 'create' ? 'success' : 'secondary') }}">{{ $log->action }}</span></td>
                        <td><code>{{ class_basename($log->model_type ?? '') }}</code></td>
                        <td>{{ $log->model_id }}</td>
                        <td><small class="text-muted">{{ $log->ip_address }}</small></td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No audit logs found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
{{ $logs->links() }}
@endsection
