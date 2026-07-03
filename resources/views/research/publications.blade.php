@extends('layouts.app')
@section('title', 'Publications')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Publications</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('research.index') }}">Research</a></li>
            <li class="breadcrumb-item active">Publications</li>
        </ol></nav>
    </div>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th>Title</th><th>Author(s)</th><th>Type</th><th>Published</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse($publications as $pub)
                    <tr>
                        <td>{{ $pub->title }}</td>
                        <td>{{ $pub->researchers->pluck('full_name')->join(', ') }}</td>
                        <td><span class="badge bg-secondary">{{ ucfirst($pub->type) }}</span></td>
                        <td>{{ $pub->published_date ? \Carbon\Carbon::parse($pub->published_date)->format('d M Y') : '—' }}</td>
                        <td><a href="{{ route('research.show', $pub) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a></td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No publications found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
{{ $publications->links() }}
@endsection
