<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireTwofishKey
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (\Illuminate\Support\Facades\Auth::check()) {
            $lastActivity = \Illuminate\Support\Facades\Session::get('twofish_last_activity');
            $currentTime = time();
            $timeout = 300; // 5 minutes in seconds

            if ($lastActivity && ($currentTime - $lastActivity > $timeout)) {
                // Session expired due to inactivity!
                \Illuminate\Support\Facades\Session::forget('twofish_key');
                \Illuminate\Support\Facades\Session::forget('twofish_last_activity');
                \Illuminate\Support\Facades\Session::put('url.intended', $request->fullUrl());
                
                return redirect()->route('unlock')->with('warning', 'Vault Anda otomatis dikunci karena tidak ada aktivitas selama 5 menit.');
            }

            // Update last activity timestamp
            \Illuminate\Support\Facades\Session::put('twofish_last_activity', $currentTime);

            if (!\Illuminate\Support\Facades\Session::has('twofish_key')) {
                \Illuminate\Support\Facades\Session::put('url.intended', $request->fullUrl());
                return redirect()->route('unlock')->with('warning', 'Sesi Twofish Anda berakhir. Silakan masukkan Master Password Anda untuk membuka kembali vault.');
            }
        }

        return $next($request);
    }
}
