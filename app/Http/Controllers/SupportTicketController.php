<?php
namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\TicketResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SupportTicketController extends Controller
{
    public function index(Request $request)
    {
        $statsBase = SupportTicket::query();
        if (!auth()->user()->hasRole(['super-admin', 'registrar'])) {
            $statsBase->where('user_id', auth()->id());
        }
        $stats = [
            'open'           => (clone $statsBase)->where('status', 'open')->count(),
            'in_progress'    => (clone $statsBase)->where('status', 'in_progress')->count(),
            'resolved_today' => (clone $statsBase)->where('status', 'resolved')->whereDate('updated_at', today())->count(),
            'avg_response'   => null,
        ];

        $query = SupportTicket::with(['submittedBy', 'assignedTo']);
        if (!auth()->user()->hasRole(['super-admin', 'registrar'])) {
            $query->where('user_id', auth()->id());
        }
        if ($request->status)   $query->where('status', $request->status);
        if ($request->priority) $query->where('priority', $request->priority);
        if ($request->category) $query->where('category', $request->category);
        if ($request->search)   $query->where('subject', 'like', '%' . $request->search . '%');
        $tickets = $query->latest()->paginate(20);
        return view('support.index', compact('tickets', 'stats'));
    }

    public function create()
    {
        return view('support.create');
    }

    public function store(Request $request)
    {
        $request->validate(['subject' => 'required|string|max:255', 'description' => 'required|string', 'category' => 'required|string', 'priority' => 'required|in:low,medium,high,urgent']);
        SupportTicket::create([
            'ticket_number' => 'TKT/' . date('Ymd') . '/' . strtoupper(Str::random(5)),
            'user_id' => auth()->id(),
            'subject' => $request->subject,
            'description' => $request->description,
            'category' => $request->category,
            'priority' => $request->priority,
            'status' => 'open',
        ]);
        return redirect()->route('support.index')->with('success', 'Support ticket created. We will respond shortly.');
    }

    public function show(SupportTicket $ticket)
    {
        $ticket->load(['user', 'assignedTo', 'responses.user']);
        $agents = User::role(['super-admin', 'registrar'])->get();
        return view('support.show', compact('ticket', 'agents'));
    }

    public function edit(SupportTicket $ticket) { return view('support.edit', compact('ticket')); }

    public function update(Request $request, SupportTicket $ticket)
    {
        $ticket->update($request->except('_token', '_method'));
        return redirect()->route('support.show', $ticket)->with('success', 'Ticket updated.');
    }

    public function destroy(SupportTicket $ticket)
    {
        $ticket->delete();
        return redirect()->route('support.index')->with('success', 'Ticket deleted.');
    }

    public function respond(Request $request, SupportTicket $ticket)
    {
        $request->validate(['response' => 'required|string']);
        TicketResponse::create(['support_ticket_id' => $ticket->id, 'user_id' => auth()->id(), 'response' => $request->response]);
        if ($ticket->status === 'open') $ticket->update(['status' => 'in_progress']);
        return back()->with('success', 'Response added.');
    }

    public function updateStatus(Request $request, SupportTicket $ticket)
    {
        $request->validate(['status' => 'required|in:open,in_progress,resolved,closed']);
        $ticket->update(['status' => $request->status]);
        return back()->with('success', 'Ticket status updated.');
    }

    public function close(SupportTicket $ticket)
    {
        $ticket->update(['status' => 'closed']);
        return back()->with('success', 'Ticket closed.');
    }

    public function assign(Request $request, SupportTicket $ticket)
    {
        $request->validate(['assigned_to' => 'required|exists:users,id']);
        $ticket->update(['assigned_to' => $request->assigned_to, 'status' => 'in_progress']);
        return back()->with('success', 'Ticket assigned.');
    }
}
