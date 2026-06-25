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
        if (\Illuminate\Support\Facades\Auth::check() && !\Illuminate\Support\Facades\Session::has('twofish_key')) {
            \Illuminate\Support\Facades\Session::put('url.intended', $request->fullUrl());
            return redirect()->route('unlock')->with('warning', 'Sesi Twofish Anda berakhir. Silakan masukkan Master Password Anda untuk membuka kembali vault.');
        }

        return $next($request);
    }
}
