<?php
namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $query = Announcement::with('author');
        if ($request->category) $query->where('category', $request->category);
        if ($request->search) $query->where('title', 'like', '%' . $request->search . '%');
        $announcements = $query->latest()->paginate(15);
        return view('announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('announcements.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'         => 'required|string|max:255',
            'content'       => 'required|string',
            'category'      => 'required|in:general,academic,event,finance,emergency,urgent',
            'attachments.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,gif|max:10240',
        ]);

        $paths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $paths[] = $file->store('announcements', 'public');
            }
        }

        $announcement = Announcement::create([
            'user_id'         => auth()->id(),
            'title'           => $request->title,
            'content'         => $request->content,
            'category'        => $request->category,
            'priority'        => $request->priority ?? 'normal',
            'target_audience' => $request->target_audience ? (array)$request->target_audience : ['all'],
            'published_by'    => auth()->id(),
            'is_published'    => $request->boolean('is_published'),
            'published_at'    => $request->boolean('is_published') ? now() : null,
            'send_email'      => $request->boolean('send_email'),
            'send_sms'        => $request->boolean('send_sms'),
            'expiry_date'     => $request->expiry_date,
            'attachments'     => $paths ?: null,
        ]);

        if ($request->boolean('is_published')) {
            $this->notifyAllUsers($announcement);
        }

        return redirect()->route('announcements.index')->with('success', 'Announcement created.');
    }

    public function show(Announcement $announcement)
    {
        $sessionKey = 'viewed_announcement_' . $announcement->id;
        if (!session()->has($sessionKey)) {
            $announcement->increment('views_count');
            session()->put($sessionKey, true);
        }

        return view('announcements.show', compact('announcement'));
    }

    public function views(Announcement $announcement)
    {
        return response()->json(['views' => $announcement->views_count ?? 0]);
    }

    public function edit(Announcement $announcement)
    {
        return view('announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $validCategories = ['general', 'academic', 'event', 'finance', 'emergency', 'urgent'];

        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'content'        => 'required|string',
            'category'       => 'required|in:' . implode(',', $validCategories),
            'priority'       => 'nullable|in:low,normal,high,urgent',
            'attachments.*'  => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,gif|max:10240',
        ]);

        $paths = $announcement->attachments ?? [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $paths[] = $file->store('announcements', 'public');
            }
        }

        // Remove individually deleted attachments
        if ($request->has('remove_attachments')) {
            foreach ((array) $request->remove_attachments as $path) {
                Storage::disk('public')->delete($path);
                $paths = array_values(array_filter($paths, fn($p) => $p !== $path));
            }
        }

        $announcement->update([
            'title'           => $validated['title'],
            'content'         => $validated['content'],
            'category'        => $validated['category'],
            'priority'        => $request->input('priority', 'normal'),
            'target_audience' => $request->target_audience ? (array)$request->target_audience : null,
            'is_published'    => $request->boolean('is_published'),
            'published_at'    => $request->boolean('is_published') ? ($announcement->published_at ?? now()) : null,
            'expiry_date'     => $request->expiry_date ?: null,
            'send_email'      => $request->boolean('send_email'),
            'send_sms'        => $request->boolean('send_sms'),
            'attachments'     => $paths ?: null,
        ]);

        return redirect()->route('announcements.index')->with('success', 'Announcement updated.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return redirect()->route('announcements.index')->with('success', 'Announcement deleted.');
    }

    public function publish(Announcement $announcement)
    {
        $wasPublished = $announcement->is_published;
        $announcement->update(['is_published' => !$wasPublished, 'published_at' => $wasPublished ? null : now()]);
        $status = $announcement->fresh()->is_published ? 'published' : 'unpublished';

        if (!$wasPublished && $announcement->fresh()->is_published) {
            $this->notifyAllUsers($announcement);
        }

        return back()->with('success', "Announcement {$status} successfully.");
    }

    private function notifyAllUsers(Announcement $announcement): void
    {
        $now     = now();
        $url     = route('announcements.show', $announcement);
        $title   = 'New Announcement: ' . \Illuminate\Support\Str::limit($announcement->title, 60);
        $message = \Illuminate\Support\Str::limit(strip_tags($announcement->content), 120);

        // Bulk in-app DB notifications (fast insert)
        $rows = User::pluck('id')->map(fn($uid) => [
            'user_id'    => $uid,
            'type'       => 'announcement',
            'title'      => $title,
            'message'    => $message,
            'data'       => json_encode([]),
            'action_url' => $url,
            'is_read'    => 0,
            'read_at'    => null,
            'created_at' => $now,
            'updated_at' => $now,
        ])->toArray();

        foreach (array_chunk($rows, 500) as $chunk) {
            \Illuminate\Support\Facades\DB::table('notifications')->insert($chunk);
        }

        // Optional email delivery
        if ($announcement->send_email) {
            NotificationService::sendBulkEmail(
                User::where('is_active', true)->get(),
                'announcement',
                $title,
                $message,
                $url,
                'View Announcement',
            );
        }
    }
}
