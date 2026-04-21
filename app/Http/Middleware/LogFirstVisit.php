<?php

namespace App\Http\Middleware;

use App\Models\LoginLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogFirstVisit
{
    /**
     * Record the first browser session visit in login_logs (once per session).
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->has('logged_visit')) {
            $request->session()->put('logged_visit', true);

            LoginLog::create([
                'user_id' => auth()->id(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        return $next($request);
    }
}
