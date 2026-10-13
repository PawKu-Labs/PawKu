<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OwnerMitraController extends Controller
{
    /**
     * Tampilkan dashboard kelola mitra milik pemilik kucing.
     */
    public function index(Request $request): View
    {
        $sessionData = $request->session()->get('supabase', []);
        $user = $sessionData['user'] ?? [];
        $profile = $sessionData['profile'] ?? [];
        $userId = $user['id'] ?? null;

        $userMitras = collect();
        $activeMitra = null;
        $stats = [
            'petshop_status' => 'unknown',
            'total_bookings' => 0,
            'today_bookings' => 0,
            'pending_bookings' => 0,
            'confirmed_bookings' => 0,
            'services_count' => 0,
            'avg_rating' => 0,
            'total_reviews' => 0,
        ];
        $bookings = collect();
        $services = collect();
        $operatingHours = collect();

        if ($userId) {
            try {
                $userMitras = DB::table('mitras')
                    ->where('owner_id', $userId)
                    ->orderBy('created_at', 'asc')
                    ->get();

                if ($userMitras->isNotEmpty()) {
                    $selectedId = $request->query('mitra_id');

                    // Prioritaskan ID terpilih jika cocok, lalu yang approved, atau fallback yang pertama
                    $activeMitra = $userMitras->firstWhere('id', $selectedId)
                        ?? $userMitras->firstWhere('status', 'approved')
                        ?? $userMitras->first();

                    if ($activeMitra) {
                        $stats['petshop_status'] = $activeMitra->status ?? 'pending';

                        $stats['total_bookings'] = DB::table('bookings')
                            ->where('mitra_id', $activeMitra->id)
                            ->count();

                        $todayDate = now()->toDateString();
                        $stats['today_bookings'] = DB::table('bookings')
                            ->where('mitra_id', $activeMitra->id)
                            ->where(function ($q) use ($todayDate): void {
                                $q->whereDate('slot_date', $todayDate)
                                    ->orWhereDate('created_at', $todayDate);
                            })
                            ->count();

                        $stats['pending_bookings'] = DB::table('bookings')
                            ->where('mitra_id', $activeMitra->id)
                            ->where('status', 'menunggu')
                            ->count();

                        $stats['confirmed_bookings'] = DB::table('bookings')
                            ->where('mitra_id', $activeMitra->id)
                            ->whereIn('status', ['dikonfirmasi', 'dilayani'])
                            ->count();

                        $stats['services_count'] = DB::table('mitra_services')
                            ->where('mitra_id', $activeMitra->id)
                            ->count();

                        // Ambil ulasan jika ada
                        $reviews = DB::table('reviews')
                            ->join('bookings', 'bookings.id', '=', 'reviews.booking_id')
                            ->where('bookings.mitra_id', $activeMitra->id)
                            ->select('reviews.rating')
                            ->get();

                        if ($reviews->isNotEmpty()) {
                            $stats['total_reviews'] = $reviews->count();
                            $stats['avg_rating'] = round($reviews->avg('rating'), 1);
                        }

                        // Daftar Booking
                        $bookings = DB::table('bookings')
                            ->leftJoin('cats', 'cats.id', '=', 'bookings.cat_id')
                            ->leftJoin('profiles', 'profiles.id', '=', 'bookings.owner_id')
                            ->leftJoin('auth.users as u', 'u.id', '=', 'bookings.owner_id')
                            ->where('bookings.mitra_id', $activeMitra->id)
                            ->select([
                                'bookings.*',
                                'cats.name as cat_name',
                                'cats.breed as cat_breed',
                                'cats.gender as cat_gender',
                                'cats.photo_url as cat_photo_url',
                                'profiles.full_name as customer_name',
                                'profiles.username as customer_username',
                                'profiles.phone_number as customer_phone',
                                'profiles.avatar_url as customer_avatar',
                                'u.email as customer_email',
                            ])
                            ->orderByDesc('bookings.created_at')
                            ->get();

                        // Daftar Layanan
                        $services = DB::table('mitra_services')
                            ->where('mitra_id', $activeMitra->id)
                            ->orderBy('name', 'asc')
                            ->get();

                        // Jam Operasional (0=Minggu, 1=Senin, ..., 6=Sabtu)
                        $operatingHours = DB::table('mitra_operating_hours')
                            ->where('mitra_id', $activeMitra->id)
                            ->orderBy('day_of_week', 'asc')
                            ->get();
                    }
                }
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return view('owner.mitra.dashboard', [
            'user' => $user,
            'profile' => $profile,
            'userMitras' => $userMitras,
            'activeMitra' => $activeMitra,
            'stats' => $stats,
            'bookings' => $bookings,
            'services' => $services,
            'operatingHours' => $operatingHours,
            'currentTab' => $request->query('tab', 'beranda'),
        ]);
    }

    /**
     * Perbarui status booking dari tab Booking mitra.
     */
    public function updateBookingStatus(Request $request, string $id): RedirectResponse
    {
        $sessionData = $request->session()->get('supabase', []);
        $userId = $sessionData['user']['id'] ?? null;

        abort_unless($userId, 401);

        $data = $request->validate([
            'status' => [
                'required',
                Rule::in(['menunggu', 'dikonfirmasi', 'dilayani', 'selesai', 'ditolak', 'dibatalkan']),
            ],
            'mitra_id' => ['nullable', 'string'],
            'tab' => ['nullable', 'string'],
        ]);

        $booking = DB::table('bookings')->where('id', $id)->first();
        abort_unless($booking, 404, 'Data booking tidak ditemukan.');

        // Pastikan booking ini milik unit mitra yang dipunyai user
        $mitra = DB::table('mitras')
            ->where('id', $booking->mitra_id)
            ->where('owner_id', $userId)
            ->first();

        abort_unless($mitra, 403, 'Anda tidak memiliki hak untuk mengelola booking ini.');

        $updates = [
            'status' => $data['status'],
        ];

        if ($data['status'] === 'dilayani' && empty($booking->check_in_at)) {
            $updates['check_in_at'] = now();
        }

        if ($data['status'] === 'selesai' && empty($booking->check_out_at)) {
            $updates['check_out_at'] = now();
        }

        DB::table('bookings')->where('id', $id)->update($updates);

        $statusLabels = [
            'menunggu' => 'Menunggu',
            'dikonfirmasi' => 'Dikonfirmasi',
            'dilayani' => 'Sedang Dilayani',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
            'dibatalkan' => 'Dibatalkan',
        ];

        $label = $statusLabels[$data['status']] ?? $data['status'];

        $tab = $data['tab'] ?? 'booking';
        $redirectUrl = route('owner.mitra.dashboard', [
            'mitra_id' => $mitra->id,
            'tab' => $tab,
        ]);

        return redirect($redirectUrl)->with('success', "Status booking antrean #{$booking->queue_number} berhasil diubah ke {$label}.");
    }
}
