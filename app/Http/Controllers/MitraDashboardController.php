<?php

namespace App\Http\Controllers;

use App\Support\BookingSql;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MitraDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $mitra = $request->attributes->get('mitra'); // sudah diverifikasi EnsureMitraOwner
        $today = now('Asia/Jakarta')->toDateString();

        $stats = ['total_bookings' => 0, 'today_bookings' => 0, 'services' => 0];

        try {
            $stats['total_bookings'] = DB::table('bookings')
                ->where('mitra_id', $mitra->id)
                ->whereNotIn('status', BookingSql::EXCLUDED)
                ->count();
        } catch (\Throwable $e) {
            report($e);
        }

        try {
            $stats['today_bookings'] = DB::table('bookings')
                ->where('mitra_id', $mitra->id)
                ->whereNotIn('status', BookingSql::EXCLUDED)
                ->whereRaw(BookingSql::DATE.' = ?', [$today])
                ->count();
        } catch (\Throwable $e) {
            report($e);
        }

        try {
            $stats['services'] = DB::table('mitra_services')
                ->where('mitra_id', $mitra->id)
                ->where('is_active', true)
                ->count();
        } catch (\Throwable $e) {
            report($e);
        }

        return view('mitra.dashboard', compact('mitra', 'stats'));
    }

    /** Placeholder tab Booking/Layanan/Profil sampai Sprint 3. */
    public function soon(Request $request): View
    {
        return view('mitra.soon', [
            'mitra' => $request->attributes->get('mitra'),
            'tab' => $request->route('tab'),
        ]);
    }
}