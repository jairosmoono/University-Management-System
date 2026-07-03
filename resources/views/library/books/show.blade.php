@extends('layouts.app')
@section('title', $book->title)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">{{ $book->title }}</h4>
        <p class="text-muted mb-0">by {{ $book->author }}</p>
    </div>
    <div class="d-flex gap-2">
        @can('manage-library')
        <a href="{{ route('library.books.edit', $book) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil me-1"></i>Edit</a>
        <form method="POST" action="{{ route('library.books.destroy', $book) }}" onsubmit="return confirm('Delete this book? This cannot be undone.')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash me-1"></i>Delete</button>
        </form>
        @endcan
    </div>
</div>
<div class="row g-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5 text-muted fw-normal">ISBN</dt><dd class="col-7">{{ $book->isbn ?? '—' }}</dd>
                    <dt class="col-5 text-muted fw-normal">Category</dt><dd class="col-7">{{ optional($book->category)->name ?? '—' }}</dd>
                    <dt class="col-5 text-muted fw-normal">Type</dt><dd class="col-7"><span class="badge bg-secondary">{{ ucfirst($book->type) }}</span></dd>
                    <dt class="col-5 text-muted fw-normal">Publisher</dt><dd class="col-7">{{ $book->publisher ?? '—' }}</dd>
                    <dt class="col-5 text-muted fw-normal">Year</dt><dd class="col-7">{{ $book->publication_year ?? '—' }}</dd>
                    <dt class="col-5 text-muted fw-normal">Total</dt><dd class="col-7">{{ $book->total_copies }}</dd>
                    <dt class="col-5 text-muted fw-normal">Available</dt><dd class="col-7"><span class="fw-bold text-{{ $book->available_copies > 0 ? 'success' : 'danger' }}">{{ $book->available_copies }}</span></dd>
                    <dt class="col-5 text-muted fw-normal">Status</dt><dd class="col-7"><span class="badge bg-{{ $book->status === 'available' ? 'success' : 'secondary' }}">{{ ucfirst($book->status) }}</span></dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent"><h6 class="mb-0">Recent Borrowings</h6></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th>Borrower</th><th>Borrow Date</th><th>Due Date</th><th>Return Date</th><th>Status</th></tr></thead>
                        <tbody>
                            @forelse($book->borrowings as $b)
                            <tr>
                                <td>{{ $b->borrower_type }} #{{ $b->borrower_id }}</td>
                                <td>{{ $b->borrow_date }}</td>
                                <td>{{ $b->due_date }}</td>
                                <td>{{ $b->return_date ?? '—' }}</td>
                                <td><span class="badge bg-{{ $b->status === 'returned' ? 'success' : ($b->status === 'overdue' ? 'danger' : 'warning text-dark') }}">{{ ucfirst($b->status) }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-3">No borrowing history.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
