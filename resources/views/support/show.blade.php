@extends('layouts.app')
@section('title', 'Ticket #' . $ticket->ticket_number)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Ticket #{{ $ticket->ticket_number }}</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('support.index') }}">Support</a></li>
            <li class="breadcrumb-item active">{{ $ticket->ticket_number }}</li>
        </ol></nav>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <!-- Original Message -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <h5 class="fw-bold mb-2">{{ $ticket->subject }}</h5>
                <div class="d-flex gap-2 mb-3">
                    @php $pc = ['low'=>'secondary','medium'=>'info','high'=>'warning','urgent'=>'danger'] @endphp
                    @php $sc = ['open'=>'danger','in_progress'=>'warning','resolved'=>'success','closed'=>'secondary'] @endphp
                    <span class="badge bg-{{ $pc[$ticket->priority] ?? 'secondary' }}">{{ ucfirst($ticket->priority) }} Priority</span>
                    <span class="badge bg-{{ $sc[$ticket->status] ?? 'secondary' }}">{{ ucfirst(str_replace('_',' ',$ticket->status)) }}</span>
                    <span class="badge bg-light text-dark">{{ $ticket->category }}</span>
                </div>
                <div class="d-flex gap-2 mb-3">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px;font-size:13px">
                        {{ strtoupper(substr(optional($ticket->submittedBy)->name, 0, 1)) }}
                    </div>
                    <div>
                        <strong>{{ optional($ticket->submittedBy)->name }}</strong>
                        <div class="text-muted small">{{ \Carbon\Carbon::parse($ticket->created_at)->format('d M Y H:i') }}</div>
                    </div>
                </div>
                <div style="line-height:1.8">{!! nl2br(e($ticket->description)) !!}</div>
            </div>
        </div>

        <!-- Responses -->
        @foreach($ticket->responses as $response)
        <div class="card border-0 shadow-sm mb-3 {{ $response->user_id !== $ticket->user_id ? 'border-start border-3 border-primary' : '' }}">
            <div class="card-body">
                <div class="d-flex gap-2 mb-2">
                    <div class="rounded-circle {{ $response->user_id !== $ticket->user_id ? 'bg-primary' : 'bg-secondary' }} text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px;font-size:13px">
                        {{ strtoupper(substr(optional($response->user)->name, 0, 1)) }}
                    </div>
                    <div>
                        <strong>{{ optional($response->user)->name }}</strong>
                        @if($response->user_id !== $ticket->user_id)
                        <span class="badge bg-primary ms-1 small">Support Staff</span>
                        @endif
                        <div class="text-muted small">{{ \Carbon\Carbon::parse($response->created_at)->format('d M Y H:i') }}</div>
                    </div>
                </div>
                <div>{!! nl2br(e($response->response)) !!}</div>
            </div>
        </div>
        @endforeach

        <!-- Reply Form -->
        @if($ticket->status !== 'closed')
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">Reply</h6>
                <form method="POST" action="{{ route('support.respond', ['ticket' => $ticket->id]) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <textarea name="response" class="form-control" rows="4" required placeholder="Type your reply..."></textarea>
                    </div>
                    <div class="mb-3">
                        <input type="file" name="attachment" class="form-control form-control-sm">
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-reply me-1"></i>Send Reply</button>
                </form>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">Ticket Info</h6>
                <table class="table table-sm table-borderless">
                    <tr><td class="text-muted">Ticket #</td><td><code>{{ $ticket->ticket_number }}</code></td></tr>
                    <tr><td class="text-muted">Status</td><td><span class="badge bg-{{ $sc[$ticket->status] ?? 'secondary' }}">{{ ucfirst(str_replace('_',' ',$ticket->status)) }}</span></td></tr>
                    <tr><td class="text-muted">Priority</td><td><span class="badge bg-{{ $pc[$ticket->priority] ?? 'secondary' }}">{{ ucfirst($ticket->priority) }}</span></td></tr>
                    <tr><td class="text-muted">Category</td><td>{{ $ticket->category }}</td></tr>
                    <tr><td class="text-muted">Created</td><td>{{ \Carbon\Carbon::parse($ticket->created_at)->format('d M Y') }}</td></tr>
                    @if($ticket->assignedTo)
                    <tr><td class="text-muted">Assigned To</td><td>{{ optional($ticket->assignedTo)->name }}</td></tr>
                    @endif
                </table>

                @can('manage-support')
                <div class="mt-3">
                    <form method="POST" action="{{ route('support.update-status', ['ticket' => $ticket->id]) }}">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label small">Change Status</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="open" {{ $ticket->status == 'open' ? 'selected' : '' }}>Open</option>
                                <option value="in_progress" {{ $ticket->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="resolved" {{ $ticket->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="closed" {{ $ticket->status == 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                        <button class="btn btn-sm btn-primary w-100">Update Status</button>
                    </form>
                </div>
                @endcan
            </div>
        </div>

        <a href="{{ route('support.index') }}" class="btn btn-outline-secondary w-100">
            <i class="bi bi-arrow-left me-1"></i> Back to Tickets
        </a>
    </div>
</div>
@endsection
