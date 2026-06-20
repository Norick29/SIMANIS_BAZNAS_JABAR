<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect('/login');
        }

        // 2. Cek apakah role user saat ini ada di dalam daftar parameter role yang diizinkan
        if (in_array(Auth::user()->role, $roles)) {
            return $next($request);
        }

        // 3. Jika tidak punya hak akses, lempar error 403 (Forbidden)
        abort(403, 'Unauthorized action.');
    }
}