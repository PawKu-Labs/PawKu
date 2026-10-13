<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OwnerDashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard pemilik kucing.
     */
    public function index(Request $request): View
    {
        $sessionData = $request->session()->get('supabase', []);
        $user = $sessionData['user'] ?? [];
        $profile = $sessionData['profile'] ?? [];
        $userId = $user['id'] ?? null;

        $stats = [
            'cats' => 0,
            'bookings' => 0,
            'upcoming_bookings' => 0,
        ];

        try {
            if ($userId) {
                $stats['cats'] = DB::table('cats')->where('owner_id', $userId)->count();
                $stats['bookings'] = DB::table('bookings')->where('user_id', $userId)->count();
                $stats['upcoming_bookings'] = DB::table('bookings')
                    ->where('user_id', $userId)
                    ->where('booking_date', '>=', now()->toDateString())
                    ->count();
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return view('owner.dashboard', [
            'user' => $user,
            'profile' => $profile,
            'stats' => $stats,
        ]);
    }
}
