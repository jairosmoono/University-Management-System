<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookBorrowing extends Model {
    use HasFactory;

    // Actual DB columns: library_book_id, student_id, user_id, issue_date, due_date,
    // return_date, fine_amount, fine_paid, status, issued_by, returned_to
    protected $fillable = [
        'library_book_id', 'student_id', 'user_id',
        'issue_date', 'due_date', 'return_date',
        'fine_amount', 'fine_paid', 'fine_paid_at', 'fine_collected_by',
        'fine_waived', 'fine_waive_reason',
        'status', 'issued_by', 'returned_to',
    ];
    protected $casts = [
        'issue_date'      => 'date',
        'due_date'        => 'date',
        'return_date'     => 'date',
        'fine_paid_at'    => 'datetime',
        'fine_amount'     => 'decimal:2',
        'fine_paid'       => 'boolean',
        'fine_waived'     => 'boolean',
    ];

    public function book() { return $this->belongsTo(LibraryBook::class, 'library_book_id'); }
    public function student() { return $this->belongsTo(Student::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function issuedBy() { return $this->belongsTo(User::class, 'issued_by'); }
    public function returnedTo() { return $this->belongsTo(User::class, 'returned_to'); }
    public function fineCollectedBy() { return $this->belongsTo(User::class, 'fine_collected_by'); }

    public function getBorrowerNameAttribute() {
        if ($this->student_id) return optional($this->student?->user)->name ?? '(Student)';
        return optional($this->user)->name ?? '(Staff)';
    }

    public function getIsOverdueAttribute(): bool
    {
        return in_array($this->status, ['borrowed', 'overdue'])
            && $this->due_date
            && now()->isAfter($this->due_date);
    }

    public function getOverdueDaysAttribute(): int
    {
        if (!$this->due_date) return 0;
        $reference = $this->return_date ?? now()->startOfDay();
        // due_date->diffInDays(reference) = reference − due_date → positive when overdue
        return max(0, (int) $this->due_date->diffInDays($reference));
    }

    public function calculateFine(float $dailyRate = 2.00): float
    {
        if (!in_array($this->status, ['borrowed', 'overdue'])) return 0;
        if (!$this->due_date || !now()->isAfter($this->due_date)) return 0;
        // due_date->diffInDays(today) = today − due_date → positive when overdue
        $days = max(0, (int) $this->due_date->diffInDays(now()->startOfDay()));
        return round($days * $dailyRate, 2);
    }
}
