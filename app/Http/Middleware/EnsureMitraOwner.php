<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class EnsureMitraOwner
{
    /**
     * Akses dashboard mitra ditentukan kepemilikan (mitras.owner_id), bukan role.
     * Mitra yang valid disimpan di $request->attributes['mitra'].
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userId = $request->session()->get('supabase.user.id');
        abort_if(! $userId, 403, 'Anda tidak memiliki akses ke halaman ini.');

        $mitra = null;
        $failed = false;

        try {
            $mitra = DB::table('mitras')
                ->where('id', $request->route('mitra'))
                ->where('owner_id', $userId)
                ->first();
        } catch (\Throwable $e) {
            report($e);
            $failed = true;
        }

        abort_if($failed, 503, 'Data mitra tidak dapat dimuat. Coba lagi.');
        abort_if(! $mitra, 404);

        // Mitra yang belum disetujui diarahkan ke halaman status
        if ($mitra->status !== 'approved') {
            return redirect()->route('owner.mitra.status', $mitra->id);
        }

        $request->attributes->set('mitra', $mitra);

        return $next($request);
    }
}