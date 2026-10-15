<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OwnerMitraController extends Controller
{
    public function index(Request $request): View
    {
        $userId = $request->session()->get('supabase.user.id');
        $mitras = collect();
        $loadError = false;

        try {
            $mitras = DB::table('mitras')
                ->where('owner_id', $userId)
                ->orderByDesc('created_at')
                ->get();
        } catch (\Throwable $e) {
            report($e);
            $loadError = true;
        }

        $counts = [
            'approved' => $mitras->where('status', 'approved')->count(),
            'pending' => $mitras->where('status', 'pending')->count(),
            'rejected' => $mitras->where('status', 'rejected')->count(),
        ];

        return view('owner.mitra.index', compact('mitras', 'counts', 'loadError'));
    }

    public function status(Request $request, string $mitra)
    {
        $userId = $request->session()->get('supabase.user.id');
        $row = null;
        $failed = false;

        try {
            $row = DB::table('mitras')
                ->where('id', $mitra)
                ->where('owner_id', $userId)
                ->first();
        } catch (\Throwable $e) {
            report($e);
            $failed = true;
        }

        abort_if($failed, 503, 'Data mitra tidak dapat dimuat. Coba lagi.');
        abort_if(! $row, 404);

        if ($row->status === 'approved') {
            return redirect()->route('mitra.dashboard', $row->id);
        }

        return view('owner.mitra.status', ['mitra' => $row]);
    }
}