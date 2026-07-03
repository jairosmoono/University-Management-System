@extends('layouts.app')
@section('title', $announcement->title)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('announcements.index') }}">Announcements</a></li>
            <li class="breadcrumb-item active">{{ Str::limit($announcement->title, 40) }}</li>
        </ol></nav>
    </div>
    @can('manage-announcements')
    <div class="d-flex gap-2">
        <a href="{{ route('announcements.edit', $announcement) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil me-1"></i>Edit</a>
        <form method="POST" action="{{ route('announcements.destroy', $announcement) }}" onsubmit="return confirm('Delete?')">
            @csrf @method('DELETE')
            <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash me-1"></i>Delete</button>
        </form>
    </div>
    @endcan
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex gap-2 mb-3">
                    @php $catColors = ['academic'=>'primary','event'=>'success','emergency'=>'danger','general'=>'secondary'] @endphp
                    <span class="badge bg-{{ $catColors[$announcement->category] ?? 'secondary' }}">{{ ucfirst($announcement->category) }}</span>
                    @if($announcement->priority === 'urgent')
                    <span class="badge bg-danger"><i class="bi bi-exclamation-triangle me-1"></i>Urgent</span>
                    @elseif($announcement->priority === 'high')
                    <span class="badge bg-warning">High Priority</span>
                    @endif
                </div>
                <h2 class="fw-bold mb-3">{{ $announcement->title }}</h2>
                <div class="d-flex gap-3 text-muted small mb-4">
                    <span><i class="bi bi-person me-1"></i>{{ optional($announcement->author)->name }}</span>
                    <span><i class="bi bi-calendar me-1"></i>{{ \Carbon\Carbon::parse($announcement->created_at)->format('d F Y') }}</span>
                    <span><i class="bi bi-eye me-1"></i><span id="viewCount">{{ $announcement->views_count ?? 0 }}</span> views</span>
                    <span><i class="bi bi-people me-1"></i>
                        @php $ta = $announcement->target_audience; @endphp
                        {{ is_array($ta) ? implode(', ', array_map('ucfirst', $ta)) : ucfirst($ta ?? 'all') }}
                    </span>
                </div>
                <hr>
                <div class="announcement-content" style="line-height:1.8">
                    {!! nl2br(e($announcement->content)) !!}
                </div>

                @if($announcement->attachments && count($announcement->attachments) > 0)
                <hr>
                <h6 class="fw-semibold">Attachments</h6>
                <div class="list-group">
                    @foreach($announcement->attachments as $attachment)
                    <a href="{{ asset('storage/' . $attachment) }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2" target="_blank">
                        <i class="bi bi-file-earmark text-primary"></i>
                        {{ basename($attachment) }}
                        <i class="bi bi-download ms-auto text-muted"></i>
                    </a>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">Announcement Details</h6>
                <table class="table table-sm table-borderless">
                    <tr><td class="text-muted">Category</td><td>{{ ucfirst($announcement->category) }}</td></tr>
                    <tr><td class="text-muted">Priority</td><td>{{ ucfirst($announcement->priority ?? 'normal') }}</td></tr>
                    <tr><td class="text-muted">Audience</td><td>
                        @php $ta = $announcement->target_audience; @endphp
                        {{ is_array($ta) ? implode(', ', array_map('ucfirst', $ta)) : ucfirst($ta ?? 'all') }}
                    </td></tr>
                    <tr><td class="text-muted">Published</td><td>{{ \Carbon\Carbon::parse($announcement->publish_date ?? $announcement->created_at)->format('d M Y') }}</td></tr>
                    @if($announcement->expiry_date)
                    <tr><td class="text-muted">Expires</td><td>{{ \Carbon\Carbon::parse($announcement->expiry_date)->format('d M Y') }}</td></tr>
                    @endif
                </table>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-3">
            <div class="card-body">
                <a href="{{ route('announcements.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-arrow-left me-1"></i> Back to Announcements
                </a>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
(function () {
    const el  = document.getElementById('viewCount');
    const url = '{{ route('announcements.views', $announcement) }}';
    if (!el) return;

    function animateTo(newVal) {
        const current = parseInt(el.textContent, 10);
        if (newVal === current) return;
        el.style.transition = 'opacity .25s';
        el.style.opacity = '0';
        setTimeout(() => {
            el.textContent = newVal;
            el.style.opacity = '1';
        }, 250);
    }

    function poll() {
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(data => animateTo(data.views))
            .catch(() => {});
    }

    // Poll every 30 seconds
    setInterval(poll, 30000);
})();
</script>
@endpush
@endsection
