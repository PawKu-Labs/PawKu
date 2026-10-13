<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSupabaseGuest
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $accessToken = $request->session()->get('supabase.access_token');

        if ($accessToken) {
            $role = $request->session()->get('supabase.profile.role');
            $redirect = match ($role) {
                'super_admin' => route('superadmin.dashboard'),
                'admin' => route('admin.dashboard'),
                'mitra' => route('mitra.dashboard'),
                default => route('owner.dashboard'),
            };

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Anda sudah login.',
                    'redirect' => $redirect,
                ], 200);
            }

            return redirect($redirect);
        }

        return $next($request);
    }
}
