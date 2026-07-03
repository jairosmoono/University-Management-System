<?php
namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\BookBorrowing;
use App\Models\LibraryBook;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BorrowingController extends Controller
{
    private function dailyFineRate(): float
    {
        $settings = Storage::exists('settings.json')
            ? (json_decode(Storage::get('settings.json'), true) ?? [])
            : [];
        return max(0, (float) ($settings['library_fine_rate'] ?? 2.00));
    }

    public function index(Request $request)
    {
        $query = BookBorrowing::with(['book', 'student.user', 'user']);
        if ($request->status) $query->where('status', $request->status);
        if ($request->from_date) $query->where('issue_date', '>=', $request->from_date);
        if ($request->to_date)   $query->where('issue_date', '<=', $request->to_date);
        if ($request->search) {
            $q = $request->search;
            $query->where(function ($q2) use ($q) {
                $q2->whereHas('book', fn($b) => $b->where('title', 'like', "%$q%"))
                   ->orWhereHas('student.user', fn($b) => $b->where('name', 'like', "%$q%"));
            });
        }
        $borrowings = $query->latest()->paginate(20);

        $rate           = $this->dailyFineRate();
        $overdueQuery   = BookBorrowing::where('status', 'borrowed')->where('due_date', '<', today());
        $finesAccruing  = (clone $overdueQuery)->get()->sum(fn($b) => $b->calculateFine($rate));

        $stats = [
            'active'          => BookBorrowing::where('status', 'borrowed')->count(),
            'overdue'         => (clone $overdueQuery)->count(),
            'returned_today'  => BookBorrowing::where('status', 'returned')->whereDate('return_date', today())->count(),
            'fines_collected' => BookBorrowing::where('fine_paid', true)->sum('fine_amount'),
            'fines_pending'   => BookBorrowing::where('fine_amount', '>', 0)->where('fine_paid', false)->where('fine_waived', false)->sum('fine_amount'),
            'fines_accruing'  => $finesAccruing,
            'fines_waived'    => BookBorrowing::where('fine_waived', true)->count(),
            'daily_rate'      => $rate,
        ];

        $students       = Student::with('user')->whereHas('user')->orderBy('id')->get();
        $availableBooks = LibraryBook::where('copies_available', '>', 0)->orderBy('title')->get();

        return view('library.borrowings.index', compact('borrowings', 'stats', 'students', 'availableBooks'));
    }

    public function issue(Request $request)
    {
        $request->validate([
            'library_book_id' => 'required|exists:library_books,id',
            'student_id'      => 'required|exists:students,id',
            'issue_date'      => 'required|date',
            'due_date'        => 'required|date|after:issue_date',
        ]);

        $book = LibraryBook::findOrFail($request->library_book_id);
        if ($book->copies_available < 1) return back()->with('error', 'No copies available.');

        BookBorrowing::create([
            'library_book_id' => $request->library_book_id,
            'student_id'      => $request->student_id,
            'issue_date'      => $request->issue_date,
            'due_date'        => $request->due_date,
            'status'          => 'borrowed',
            'issued_by'       => auth()->id(),
        ]);

        $book->decrement('copies_available');
        return back()->with('success', 'Book issued successfully.');
    }

    public function return(BookBorrowing $borrowing)
    {
        $rate        = $this->dailyFineRate();
        $fine        = $borrowing->calculateFine($rate);
        $overdueDays = $fine > 0 ? (int) $borrowing->due_date->diffInDays(today()) : 0;

        $borrowing->update([
            'return_date' => today(),
            'status'      => 'returned',
            'fine_amount' => $fine > 0 ? $fine : $borrowing->fine_amount,
            'returned_to' => auth()->id(),
        ]);
        $borrowing->book?->increment('copies_available');

        if ($fine > 0) {
            $msg = "Book returned {$overdueDays} day(s) late. Fine of " . formatCurrency($fine)
                 . " (K{$rate}/day × {$overdueDays} days) applied.";
            return back()->with('warning', $msg);
        }

        return back()->with('success', 'Book returned successfully.');
    }

    public function renew(BookBorrowing $borrowing)
    {
        if ($borrowing->status !== 'borrowed') {
            return back()->with('error', 'This borrowing cannot be renewed.');
        }
        $newDue = ($borrowing->due_date->isFuture() ? $borrowing->due_date : today())->addDays(14);
        $borrowing->update(['due_date' => $newDue]);
        return back()->with('success', 'Borrowing renewed. New due date: ' . $newDue->format('d M Y'));
    }

    public function overdue(Request $request)
    {
        $query = BookBorrowing::with(['book', 'student.user'])
            ->where('status', 'borrowed')
            ->where('due_date', '<', today());

        if ($request->search) {
            $q = $request->search;
            $query->where(function ($q2) use ($q) {
                $q2->whereHas('book', fn($b) => $b->where('title', 'like', "%$q%"))
                   ->orWhereHas('student.user', fn($b) => $b->where('name', 'like', "%$q%"));
            });
        }

        $borrowings = $query->orderBy('due_date')->paginate(25);

        $rate       = $this->dailyFineRate();
        $overdueBase = BookBorrowing::whereIn('status', ['borrowed', 'overdue'])->where('due_date', '<', today());
        $oldestDue   = (clone $overdueBase)->orderBy('due_date')->value('due_date');

        $stats = [
            'total'       => (clone $overdueBase)->count(),
            'fines_due'   => (clone $overdueBase)->get()->sum(fn($b) => $b->calculateFine($rate)),
            'oldest_days' => $oldestDue ? today()->diffInDays($oldestDue) : 0,
            'daily_rate'  => $rate,
        ];

        return view('library.borrowings.overdue', compact('borrowings', 'stats'));
    }

    public function fines(Request $request)
    {
        $query = BookBorrowing::with(['book', 'student.user', 'fineCollectedBy'])
            ->where(fn($q) => $q->where('fine_amount', '>', 0)->orWhere('fine_waived', true));

        if ($request->fine_status === 'paid')   $query->where('fine_paid', true)->where('fine_waived', false);
        if ($request->fine_status === 'unpaid') $query->where('fine_paid', false)->where('fine_waived', false);
        if ($request->fine_status === 'waived') $query->where('fine_waived', true);

        if ($request->search) {
            $q = $request->search;
            $query->where(function ($q2) use ($q) {
                $q2->whereHas('book', fn($b) => $b->where('title', 'like', "%$q%"))
                   ->orWhereHas('student.user', fn($b) => $b->where('name', 'like', "%$q%"));
            });
        }

        $borrowings = $query->latest()->paginate(25);

        $stats = [
            'total_unpaid'   => BookBorrowing::where('fine_amount', '>', 0)->where('fine_paid', false)->where('fine_waived', false)->sum('fine_amount'),
            'total_collected'=> BookBorrowing::where('fine_paid', true)->sum('fine_amount'),
            'count_unpaid'   => BookBorrowing::where('fine_amount', '>', 0)->where('fine_paid', false)->where('fine_waived', false)->count(),
            'count_waived'   => BookBorrowing::where('fine_waived', true)->count(),
        ];

        return view('library.borrowings.fines', compact('borrowings', 'stats'));
    }

    public function collectFine(Request $request, BookBorrowing $borrowing)
    {
        if ($borrowing->fine_paid || $borrowing->fine_waived) {
            return back()->with('error', 'This fine has already been settled.');
        }
        if ($borrowing->fine_amount <= 0) {
            return back()->with('error', 'No fine amount recorded for this borrowing.');
        }

        $request->validate([
            'amount_paid' => 'required|numeric|min:0.01|max:' . $borrowing->fine_amount,
        ]);

        $partial = (float) $request->amount_paid < (float) $borrowing->fine_amount;

        $borrowing->update([
            'fine_paid'         => !$partial,
            'fine_paid_at'      => $partial ? null : now(),
            'fine_collected_by' => auth()->id(),
            'fine_amount'       => $partial ? $borrowing->fine_amount - (float) $request->amount_paid : $borrowing->fine_amount,
        ]);

        if ($partial) {
            return back()->with('warning', 'Partial payment of ' . formatCurrency($request->amount_paid) . ' recorded. Remaining: ' . formatCurrency($borrowing->fine_amount - (float) $request->amount_paid));
        }

        return back()->with('success', 'Fine of ' . formatCurrency($borrowing->fine_amount) . ' collected successfully.');
    }

    public function waiveFine(Request $request, BookBorrowing $borrowing)
    {
        if ($borrowing->fine_paid || $borrowing->fine_waived) {
            return back()->with('error', 'This fine has already been settled.');
        }

        $request->validate(['reason' => 'required|string|min:5|max:255']);

        $borrowing->update([
            'fine_waived'      => true,
            'fine_waive_reason'=> $request->reason,
            'fine_collected_by'=> auth()->id(),
            'fine_paid_at'     => now(),
        ]);

        return back()->with('success', 'Fine waived successfully.');
    }

    public function adjustFine(Request $request, BookBorrowing $borrowing)
    {
        if ($borrowing->fine_paid || $borrowing->fine_waived) {
            return back()->with('error', 'Cannot adjust a settled fine.');
        }

        $request->validate(['fine_amount' => 'required|numeric|min:0']);

        $borrowing->update(['fine_amount' => $request->fine_amount]);

        return back()->with('success', 'Fine adjusted to ' . formatCurrency($request->fine_amount) . '.');
    }
}
