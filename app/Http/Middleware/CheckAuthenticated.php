<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAuthenticated
{
    /**
     * Menangani request yang masuk.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Mengecek apakah pengguna sudah login
        if (Auth::check()) {
            // Jika sudah login, lanjutkan ke request berikutnya
            return $next($request);
        }

        // Jika belum login, arahkan ke halaman login
        return redirect()->route('auth.login')->with('error', 'You must be logged in to access this page.');
    }
}
