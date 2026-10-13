<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MitraDashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard mitra petshop.
     */
    public function index(Request $request): View
    {
        $sessionData = $request->session()->get('supabase', []);
        $user = $sessionData['user'] ?? [];
        $profile = $sessionData['profile'] ?? [];
        $userId = $user['id'] ?? null;

        $stats = [
            'petshop_status' => 'unknown',
            'total_bookings' => 0,
            'today_bookings' => 0,
            'services' => 0,
        ];

        try {
            if ($userId) {
                $mitra = DB::table('mitras')->where('user_id', $userId)->first();

                if ($mitra) {
                    $stats['petshop_status'] = $mitra->status ?? 'pending';
                    $stats['total_bookings'] = DB::table('bookings')
                        ->where('petshop_id', $mitra->id)
                        ->count();
                    $stats['today_bookings'] = DB::table('bookings')
                        ->where('petshop_id', $mitra->id)
                        ->where('booking_date', now()->toDateString())
                        ->count();
                    $stats['services'] = DB::table('services')
                        ->where('petshop_id', $mitra->id)
                        ->count();
                }
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return view('mitra.dashboard', [
            'user' => $user,
            'profile' => $profile,
            'stats' => $stats,
        ]);
    }
}
