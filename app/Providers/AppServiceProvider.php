<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Auth\Events\Login;
use App\Models\Admission;
use App\Models\Announcement;
use App\Models\Message;
use App\Models\SupportTicket;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // After a hard-delete on any model, reset the table's AUTO_INCREMENT
        // so the next inserted row gets id = max(existing ids) + 1 (or 1 if empty).
        // forceDeleted fires when a SoftDeletes model is permanently removed.
        // deleted fires for models without SoftDeletes (plain hard-delete).
        Event::listen('eloquent.forceDeleted: *', function (string $event, array $data) {
            $model = $data[0];
            DB::statement("ALTER TABLE `{$model->getTable()}` AUTO_INCREMENT = 1");
        });

        Event::listen('eloquent.deleted: *', function (string $event, array $data) {
            $model = $data[0];
            // Skip if the model uses SoftDeletes — the row still exists; wait for forceDeleted.
            if (!in_array(SoftDeletes::class, class_uses_recursive($model))) {
                DB::statement("ALTER TABLE `{$model->getTable()}` AUTO_INCREMENT = 1");
            }
        });

        Event::listen(Login::class, function (Login $event) {
            $event->user->update([
                'last_login_at' => now(),
                'last_login_ip' => request()->ip(),
            ]);
        });

        // Share global data with all views
        View::composer('*', function ($view) {
            if (auth()->check()) {
                $unreadMessages = Message::where('receiver_id', auth()->id())
                    ->where('is_read', false)->count();
                $recentAnnouncements = Announcement::where('is_published', true)
                    ->latest()->take(5)->get();

                $user = auth()->user();
                $openTicketsQuery = SupportTicket::whereIn('status', ['open', 'in_progress']);
                if (!$user->hasRole(['super-admin', 'registrar'])) {
                    $openTicketsQuery->where('user_id', $user->id);
                }
                $openSupportTickets = $openTicketsQuery->count();

                $pendingAdmissions = $user->hasAnyRole(['super-admin', 'registrar', 'admin'])
                    ? Admission::where('status', 'pending')->count()
                    : 0;

                $view->with('unreadMessages', $unreadMessages);
                $view->with('recentAnnouncements', $recentAnnouncements);
                $view->with('openSupportTickets', $openSupportTickets);
                $view->with('pendingAdmissions', $pendingAdmissions);
            }
        });
    }
}
