<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        foreach ($roles as $role) {
            if ($role === 'admin' && $user->isAdmin()) {
                return $next($request);
            }
            if ($role === 'superadmin' && $user->isSuperAdmin()) {
                return $next($request);
            }
            if ($role === 'rw' && ($user->isRw() || $user->isSuperAdmin())) {
                return $next($request);
            }
            if ($role === 'rt' && ($user->isRt() || $user->isRw() || $user->isSuperAdmin())) {
                return $next($request);
            }
            if ($role === 'warga' && $user->isWarga()) {
                return $next($request);
            }
            if ($user->role === $role) {
                return $next($request);
            }
        }

        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }
}
