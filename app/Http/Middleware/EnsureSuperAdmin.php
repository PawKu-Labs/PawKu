<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $role = $request->session()->get('supabase.profile.role');

        if ($role !== 'super_admin') {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Akses ditolak. Fitur ini hanya dapat diakses oleh Super Admin.',
                ], 403);
            }

            return redirect()->route('admin.dashboard')->with('error', 'Akses ditolak. Halaman tersebut hanya dapat diakses oleh Super Admin.');
        }

        return $next($request);
    }
}
