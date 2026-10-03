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

        try {
            $stats['cats'] = DB::table('cats')->count();
            $stats['bookings'] = DB::table('bookings')->count();
            $stats['petshops'] = DB::table('petshops')->count();
            $stats['users'] = DB::table('profiles')->count();
        } catch (\Throwable $e) {
            report($e);
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
