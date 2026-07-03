<?php
namespace App\Services;

use App\Models\NotificationPreference;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send an SMS to a single user (respects their SMS preference, defaults to enabled).
     */
    public static function send(User $user, string $message): bool
    {
        if (!static::userWantsSms($user, 'general')) return false;
        return static::dispatch($user->phone, $message);
    }

    /**
     * Send an SMS to a user for a specific notification type.
     */
    public static function sendForType(User $user, string $type, string $message): bool
    {
        if (!static::userWantsSms($user, $type)) return false;
        return static::dispatch($user->phone, $message);
    }

    /**
     * Bulk send to a collection of users. Returns [sent, failed] counts.
     */
    public static function sendBulk(iterable $users, string $type, string $message): array
    {
        $sent = $failed = 0;

        foreach ($users as $user) {
            if (!($user instanceof User)) {
                $user = User::find($user);
                if (!$user) { $failed++; continue; }
            }
            if (!static::userWantsSms($user, $type)) continue;
            if (static::dispatch($user->phone, $message)) {
                $sent++;
            } else {
                $failed++;
            }
        }

        return ['sent' => $sent, 'failed' => $failed];
    }

    /**
     * Check whether a user has SMS enabled for the given type (default: enabled).
     */
    public static function userWantsSms(User $user, string $type): bool
    {
        $pref = NotificationPreference::where('user_id', $user->id)
            ->where('type', $type)
            ->first();

        return $pref ? $pref->sms_enabled : true;
    }

    /**
     * Low-level dispatch: send a raw SMS to a phone number.
     * Respects SMS_DRIVER env var. Falls back to 'log' driver when credentials absent.
     */
    public static function dispatch(?string $phone, string $message): bool
    {
        // Normalise: keep only +, digits
        $normalized = preg_replace('/[^\d+]/', '', (string) $phone);
        if (!$normalized || !preg_match('/^\+?\d{7,15}$/', $normalized)) {
            Log::info("SMS skipped: invalid or missing phone [{$phone}]");
            return false;
        }
        $phone = $normalized;

        $driver = config('sms.driver', env('SMS_DRIVER', 'log'));

        return match($driver) {
            'africastalking' => static::sendViaAfricasTalking($phone, $message),
            'twilio'         => static::sendViaTwilio($phone, $message),
            default          => static::sendViaLog($phone, $message),
        };
    }

    // ── Drivers ────────────────────────────────────────────────────────────────

    private static function sendViaAfricasTalking(string $phone, string $message): bool
    {
        $username = config('sms.africastalking.username', env('SMS_USERNAME'));
        $apiKey   = config('sms.africastalking.api_key',  env('SMS_API_KEY'));
        $senderId = config('sms.africastalking.sender_id', env('SMS_SENDER_ID', ''));

        if (!$username || !$apiKey) {
            return static::sendViaLog($phone, $message);
        }

        // Sandbox uses a different base URL
        $isSandbox = ($username === 'sandbox');
        $baseUrl   = $isSandbox
            ? 'https://api.sandbox.africastalking.com/version1/'
            : 'https://api.africastalking.com/version1/';

        $payload = [
            'username' => $username,
            'to'       => $phone,
            'message'  => $message,
        ];

        // AT sender IDs must be alphanumeric, max 11 chars — strip spaces/specials
        $cleanSenderId = preg_replace('/[^A-Za-z0-9]/', '', $senderId);
        if ($cleanSenderId && !$isSandbox) {
            $payload['from'] = substr($cleanSenderId, 0, 11);
        }

        try {
            $response = Http::withoutVerifying()   // handles Windows SSL cert issues
                ->withHeaders([
                    'apikey' => $apiKey,   // AT API requires lowercase 'apikey'
                    'Accept' => 'application/json',
                ])
                ->asForm()
                ->post($baseUrl . 'messaging', $payload);

            if (!$response->successful()) {
                Log::warning("AT SMS HTTP {$response->status()} for {$phone}: " . $response->body());
                return false;
            }

            $body      = $response->object();
            $recipients = $body?->SMSMessageData?->Recipients ?? [];
            $code       = $recipients[0]->statusCode ?? 0;

            // 100=Processed, 101=Sent, 102=Queued
            if (in_array($code, [100, 101, 102])) {
                return true;
            }

            $atStatus = $recipients[0]->status ?? 'unknown';
            Log::warning("AT SMS statusCode={$code} ({$atStatus}) for {$phone}");
            return false;

        } catch (\Throwable $e) {
            Log::error("AT SMS exception for {$phone}: " . $e->getMessage());
            return false;
        }
    }

    private static function sendViaTwilio(string $phone, string $message): bool
    {
        $sid   = env('TWILIO_SID');
        $token = env('TWILIO_TOKEN');
        $from  = env('TWILIO_FROM');

        if (!$sid || !$token || !$from) {
            return static::sendViaLog($phone, $message);
        }

        try {
            $client = new \Twilio\Rest\Client($sid, $token);
            $client->messages->create($phone, ['from' => $from, 'body' => $message]);
            return true;
        } catch (\Throwable $e) {
            Log::error("Twilio SMS exception for {$phone}: " . $e->getMessage());
            return false;
        }
    }

    private static function sendViaLog(string $phone, string $message): bool
    {
        Log::channel('stack')->info("[SMS] To: {$phone} | Message: {$message}");
        return true; // treat as sent for dev/log mode
    }
}
