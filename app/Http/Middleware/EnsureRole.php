<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Pemakaian: ->middleware('role:superadmin') atau 'role:superadmin,admin'
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $role = $request->session()->get('supabase.profile.role');

        if (! in_array($role, $roles, true)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
