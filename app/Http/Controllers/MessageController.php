<?php
namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $folder = $request->get('folder', 'inbox');

        if ($folder === 'sent') {
            $messages = Message::where('sender_id', auth()->id())
                ->where('sender_deleted', false)
                ->with('receiver')
                ->latest()->paginate(20);
        } else {
            $messages = Message::where('receiver_id', auth()->id())
                ->where('receiver_deleted', false)
                ->with('sender')
                ->latest()->paginate(20);
        }

        $unreadCount = Message::where('receiver_id', auth()->id())
            ->where('is_read', false)
            ->where('receiver_deleted', false)
            ->count();

        $selectedMessage = null;
        if ($request->has('message')) {
            $selectedMessage = Message::with(['sender', 'receiver'])
                ->find($request->get('message'));
            if ($selectedMessage && $selectedMessage->receiver_id === auth()->id() && !$selectedMessage->is_read) {
                $selectedMessage->markAsRead();
            }
        }

        return view('messages.index', compact('messages', 'unreadCount', 'selectedMessage', 'folder'));
    }

    public function compose()
    {
        $users = User::where('id', '!=', auth()->id())
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        return view('messages.compose', compact('users'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'subject'     => 'required|string|max:255',
            'content'     => 'required|string',
        ]);
        Message::create([
            'sender_id'   => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'subject'     => $request->subject,
            'content'     => $request->content,
            'parent_id'   => $request->parent_id,
        ]);
        return redirect()->route('messages.index', ['folder' => 'sent'])->with('success', 'Message sent.');
    }

    public function reply(Request $request, Message $message)
    {
        $request->validate(['content' => 'required|string']);
        Message::create([
            'sender_id'   => auth()->id(),
            'receiver_id' => $message->sender_id,
            'subject'     => 'Re: ' . $message->subject,
            'content'     => $request->content,
            'parent_id'   => $message->id,
        ]);
        return back()->with('success', 'Reply sent.');
    }

    public function show(Message $message)
    {
        if ($message->receiver_id === auth()->id() && !$message->is_read) {
            $message->markAsRead();
        }
        $replies = Message::where('parent_id', $message->id)->with('sender')->get();
        return view('messages.show', compact('message', 'replies'));
    }

    public function destroy(Message $message)
    {
        if ($message->sender_id === auth()->id())   $message->update(['sender_deleted' => true]);
        elseif ($message->receiver_id === auth()->id()) $message->update(['receiver_deleted' => true]);
        return back()->with('success', 'Message deleted.');
    }
}
