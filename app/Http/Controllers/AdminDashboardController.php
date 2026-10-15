<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Tampilkan halaman admin dashboard ala Mantis UI.
     */
    public function index(Request $request): View
    {
        $sessionData = $request->session()->get('supabase', []);
        $user = $sessionData['user'] ?? [];
        $profile = $sessionData['profile'] ?? [];
        $expiresAt = $sessionData['expires_at'] ?? null;

        // Ambil data statistik dari database dengan fallback aman
        $stats = [
            'cats' => 0,
            'bookings' => 0,
            'petshops' => 0,
            'users' => 0,
        ];

        // Tiap hitungan berdiri sendiri agar satu query gagal tidak menggagalkan yang lain
        foreach ([
            'cats' => fn () => DB::table('cats')->count(),
            'bookings' => fn () => DB::table('bookings')->count(),
            'petshops' => fn () => DB::table('mitras')->count(),
            'users' => fn () => DB::table('profiles')->count(),
        ] as $key => $fn) {
            try {
                $stats[$key] = $fn();
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return view('admin.dashboard', [
            'user' => $user,
            'profile' => $profile,
            'sessionData' => $sessionData,
            'expiresAt' => $expiresAt,
            'stats' => $stats,
        ]);
    }
}