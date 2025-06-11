<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check() || !$request->user()->hasRole($role)) {
            // Atau redirect ke halaman lain dengan pesan error
            abort(403, 'ANDA TIDAK MEMILIKI AKSES UNTUK HALAMAN INI.');
        }
        return $next($request);
    }
}