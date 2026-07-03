<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SystemNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $appName;

    public function __construct(
        public string  $notifTitle,
        public string  $notifMessage,
        public string  $recipientName  = '',
        public ?string $actionUrl      = null,
        public string  $actionLabel    = 'View Details',
        public string  $notifType      = 'general',
        public array   $attachmentPaths = [],
    ) {
        $path = storage_path('app/settings.json');
        $settings = file_exists($path) ? (json_decode(file_get_contents($path), true) ?? []) : [];
        $this->appName = $settings['university_name'] ?? config('app.name', 'University Management System');
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->notifTitle);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.notification');
    }

    public function attachments(): array
    {
        $items = [];
        foreach ($this->attachmentPaths as $path) {
            $fullPath = storage_path('app/public/' . $path);
            if (file_exists($fullPath)) {
                $items[] = \Illuminate\Mail\Mailables\Attachment::fromPath($fullPath)
                    ->as(basename($path));
            }
        }
        return $items;
    }
}
