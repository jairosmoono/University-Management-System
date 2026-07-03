<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $path = storage_path('app/settings.json');
        $settings = file_exists($path) ? (json_decode(file_get_contents($path), true) ?? []) : [];

        if (!($settings['maintenance_mode'] ?? false)) {
            return $next($request);
        }

        if (auth()->check() && auth()->user()->hasRole('super-admin')) {
            return $next($request);
        }

        if ($request->is('login') || $request->routeIs('logout')) {
            return $next($request);
        }

        $uni = $settings;
        return response()->view('maintenance', compact('uni'), 503);
    }
}
