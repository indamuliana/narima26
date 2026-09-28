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
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if account is active
        if (! $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'login' => 'Akun Anda sedang dinonaktifkan. Silakan hubungi panitia SPMB SMK Wikrama 1 Garut.',
            ]);
        }

        // If no specific roles required, allow through
        if (empty($roles)) {
            return $next($request);
        }

        // Check if user has one of the allowed roles
        if (! in_array($user->role, $roles, true)) {
            // If request expects JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Anda tidak memiliki hak akses untuk halaman ini.',
                ], 403);
            }

            // Redirect user to their own dashboard with warning
            $targetRoute = $user->getDashboardRoute();
            if ($request->routeIs($targetRoute)) {
                abort(403, 'Akses ditolak.');
            }

            return redirect()->route($targetRoute)->with('error', 'Akses ditolak. Anda tidak memiliki izin mengakses halaman tersebut.');
        }

        return $next($request);
    }
}
