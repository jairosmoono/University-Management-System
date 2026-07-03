<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\NotificationPreference;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->latest()->paginate(20);
        return view('notifications.index', compact('notifications'));
    }

    public function markRead($id)
    {
        $notif = Notification::where('id', $id)->where('user_id', auth()->id())->first();
        if ($notif) {
            $notif->update(['is_read' => true]);
            if ($notif->action_url) {
                return redirect($notif->action_url);
            }
        }
        return back();
    }

    public function markAllRead()
    {
        Notification::where('user_id', auth()->id())->where('is_read', false)
            ->update(['is_read' => true]);
        return back()->with('success', 'All notifications marked as read.');
    }

    public function count()
    {
        $count = Notification::where('user_id', auth()->id())->where('is_read', false)->count();
        return response()->json(['count' => $count]);
    }

    public function preferences()
    {
        $types = NotificationPreference::allTypes();

        $rows     = NotificationPreference::where('user_id', auth()->id())->get();
        $existing = [];
        foreach ($rows as $row) {
            $existing[$row->type] = ['email' => $row->email_enabled, 'sms' => $row->sms_enabled];
        }

        return view('notifications.preferences', compact('types', 'existing'));
    }

    public function updatePreferences(Request $request)
    {
        $types = array_keys(NotificationPreference::allTypes());

        foreach ($types as $type) {
            NotificationPreference::updateOrCreate(
                ['user_id' => auth()->id(), 'type' => $type],
                [
                    'email_enabled' => $request->boolean("email_{$type}"),
                    'sms_enabled'   => $request->boolean("sms_{$type}"),
                ]
            );
        }

        return back()->with('success', 'Notification preferences saved.');
    }
}
