@extends('layouts.app')
@section('title', 'Support Tickets')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Support Tickets</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Support</li>
        </ol></nav>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTicketModal">
        <i class="bi bi-plus-circle me-1"></i> New Ticket
    </button>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card border-0 shadow-sm text-center p-3"><h4 class="text-danger fw-bold">{{ $stats['open'] }}</h4><small class="text-muted">Open</small></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm text-center p-3"><h4 class="text-warning fw-bold">{{ $stats['in_progress'] }}</h4><small class="text-muted">In Progress</small></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm text-center p-3"><h4 class="text-success fw-bold">{{ $stats['resolved_today'] }}</h4><small class="text-muted">Resolved Today</small></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm text-center p-3"><h4 class="text-info fw-bold">{{ $stats['avg_response'] ?? '—' }}</h4><small class="text-muted">Avg Response</small></div></div>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2">
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="priority" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Priority</option>
                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                    <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search tickets..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-sm btn-primary">Search</button>
                <a href="{{ route('support.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table class="table datatable table-hover">
            <thead class="table-light">
                <tr>
                    <th>Ticket #</th><th>Subject</th><th>Submitted By</th><th>Category</th><th>Priority</th><th>Status</th><th>Created</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tickets as $ticket)
                <tr>
                    <td><code>{{ $ticket->ticket_number }}</code></td>
                    <td class="fw-semibold">{{ $ticket->subject }}</td>
                    <td>{{ optional($ticket->submittedBy)->name }}</td>
                    <td>{{ $ticket->category }}</td>
                    <td>
                        @php $pc = ['low'=>'secondary','medium'=>'info','high'=>'warning','urgent'=>'danger'] @endphp
                        <span class="badge bg-{{ $pc[$ticket->priority] ?? 'secondary' }}">{{ ucfirst($ticket->priority) }}</span>
                    </td>
                    <td>
                        @php $sc = ['open'=>'danger','in_progress'=>'warning','resolved'=>'success','closed'=>'secondary'] @endphp
                        <span class="badge bg-{{ $sc[$ticket->status] ?? 'secondary' }}">{{ ucfirst(str_replace('_',' ',$ticket->status)) }}</span>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($ticket->created_at)->format('d M Y') }}</td>
                    <td>
                        <a href="{{ route('support.show', $ticket) }}" class="btn btn-sm btn-outline-primary">View</a>
                        @can('manage-support')
                        @if($ticket->status === 'open')
                        <form method="POST" action="{{ route('support.close', $ticket) }}" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-outline-secondary">Close</button>
                        </form>
                        @endif
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $tickets->withQueryString()->links() }}
    </div>
</div>

<div class="modal fade" id="createTicketModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('support.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Submit Support Ticket</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Subject *</label>
                        <input type="text" name="subject" class="form-control" required>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label">Category *</label>
                            <select name="category" class="form-select" required>
                                <option value="academic">Academic</option>
                                <option value="finance">Finance</option>
                                <option value="hostel">Hostel</option>
                                <option value="technical">Technical</option>
                                <option value="library">Library</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Priority</label>
                            <select name="priority" class="form-select">
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 mt-2">
                        <label class="form-label">Description *</label>
                        <textarea name="description" class="form-control" rows="4" required placeholder="Describe your issue in detail..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Attachment</label>
                        <input type="file" name="attachment" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Ticket</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
