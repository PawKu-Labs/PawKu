<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Dashboard pengelolaan satu unit mitra (Beranda, Booking, Layanan, Profil).
 *
 * ASUMSI SKEMA (ubah konstanta T_* / kolom di bawah bila nama di database Anda berbeda):
 *   bookings            : id, user_id, cat_id, service_id, [mitra_id], [slot_id], status,
 *                         notes, queue_number, check_in_at, created_at, updated_at
 *   services            : id, mitra_id, name, price, duration_minutes, capacity, room_class, is_active
 *   cats                : id, name, breed
 *   profiles            : id, full_name, username, phone_number
 *   service_slots       : id, slot_date, start_time, end_time              (opsional)
 *   mitra_operating_hours: mitra_id, day_of_week, is_closed, is_24h, open_time, close_time (opsional)
 *   reviews             : mitra_id, rating                                  (opsional)
 *
 * Kolom/tabel yang bersifat opsional diperiksa lebih dulu, sehingga halaman tidak error bila belum ada.
 */
class MitraDashboardController extends Controller
{
    private const TZ = 'Asia/Jakarta';

    private const TABS = ['beranda', 'booking', 'layanan', 'profil'];

    private const BOOKING_STATUSES = ['menunggu', 'dikonfirmasi', 'dilayani', 'selesai', 'ditolak', 'dibatalkan'];

    /** Alur status yang diizinkan: dari => [tujuan...] */
    private const TRANSITIONS = [
        'menunggu' => ['dikonfirmasi', 'ditolak'],
        'dikonfirmasi' => ['dilayani', 'selesai'],
        'dilayani' => ['selesai'],
    ];

    private const T_BOOKINGS = 'bookings';
    private const T_SERVICES = 'services';
    private const T_CATS = 'cats';
    private const T_PROFILES = 'profiles';
    private const T_SLOTS = 'service_slots';
    private const T_HOURS = 'mitra_operating_hours';
    private const T_REVIEWS = 'reviews';

    public function index(Request $request, string $mitra): View|RedirectResponse
    {
        $userId = $request->session()->get('supabase.user.id');
        $mitraId = strtolower($mitra);

        try {
            $userMitras = DB::table('mitras')
                ->where('owner_id', $userId)
                ->orderBy('name')
                ->get();
        } catch (\Throwable $e) {
            report($e);
            abort(503, 'Data mitra tidak dapat dimuat. Coba lagi.');
        }

        $activeMitra = $userMitras->first(fn($m) => strtolower((string) $m->id) === $mitraId);
        abort_if(!$activeMitra, 404);

        if ($activeMitra->status !== 'approved') {
            return redirect()->route('owner.mitra.status', ['mitra' => $mitraId]);
        }

        $tab = (string) $request->query('tab', 'beranda');
        $currentTab = in_array($tab, self::TABS, true) ? $tab : 'beranda';

        $bookings = $this->safe(fn() => $this->loadBookings($activeMitra->id), collect());
        $services = $this->safe(
            fn() => DB::table(self::T_SERVICES)->where('mitra_id', $activeMitra->id)->orderBy('name')->get(),
            collect()
        );
        $operatingHours = $this->safe(function () use ($activeMitra) {
            if (!Schema::hasTable(self::T_HOURS)) {
                return collect();
            }

            return DB::table(self::T_HOURS)->where('mitra_id', $activeMitra->id)->get();
        }, collect());

        $stats = $this->buildStats($bookings, $services, $activeMitra->id);

        return view('owner.mitra.dashboard', compact(
            'userMitras',
            'activeMitra',
            'mitraId',
            'currentTab',
            'bookings',
            'services',
            'operatingHours',
            'stats'
        ));
    }

    public function updateBookingStatus(Request $request, string $mitra, string $booking): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(self::BOOKING_STATUSES)],
        ]);

        $userId = $request->session()->get('supabase.user.id');

        $activeMitra = $this->safe(
            fn() => DB::table('mitras')->where('id', strtolower($mitra))->where('owner_id', $userId)->first(),
            null
        );
        abort_if(!$activeMitra, 404);
        abort_if($activeMitra->status !== 'approved', 403);

        $back = redirect()->route('owner.mitra.dashboard', ['mitra' => strtolower($mitra), 'tab' => 'booking']);

        $row = $this->safe(fn() => $this->findBooking($booking, $activeMitra->id), null);
        abort_if(!$row, 404);

        $allowed = self::TRANSITIONS[$row->status] ?? [];
        if (!in_array($data['status'], $allowed, true)) {
            return $back->with('error', 'Perubahan status tidak diizinkan untuk booking ini.');
        }

        $payload = ['status' => $data['status']];
        if (Schema::hasColumn(self::T_BOOKINGS, 'updated_at')) {
            $payload['updated_at'] = now();
        }

        try {
            // Syarat status lama mencegah dua klik/dua perangkat saling menimpa.
            $affected = DB::table(self::T_BOOKINGS)
                ->where('id', $row->id)
                ->where('status', $row->status)
                ->update($payload);
        } catch (\Throwable $e) {
            report($e);

            return $back->with('error', 'Gagal memperbarui status booking. Coba lagi.');
        }

        if (!$affected) {
            return $back->with('error', 'Status booking sudah berubah. Muat ulang halaman.');
        }

        return $back->with('success', 'Status booking diperbarui menjadi "' . $data['status'] . '".');
    }

    // ---------------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------------

    private function loadBookings(string $mitraId): Collection
    {
        $b = self::T_BOOKINGS;
        $s = self::T_SERVICES;
        $c = self::T_CATS;
        $p = self::T_PROFILES;
        $sl = self::T_SLOTS;

        $hasMitraCol = Schema::hasColumn($b, 'mitra_id');
        $hasSlots = Schema::hasTable($sl) && Schema::hasColumn($b, 'slot_id');

        $q = DB::table($b)
            ->leftJoin($s, "$s.id", '=', "$b.service_id")
            ->leftJoin($c, "$c.id", '=', "$b.cat_id")
            ->leftJoin($p, "$p.id", '=', "$b.user_id");

        if ($hasSlots) {
            $q->leftJoin($sl, "$sl.id", '=', "$b.slot_id");
        }

        $q->where($hasMitraCol ? "$b.mitra_id" : "$s.mitra_id", $mitraId);

        $select = [
            "$b.*",
            "$s.name as service_name",
            "$c.name as cat_name",
            "$p.full_name as customer_name",
            "$p.username as customer_username",
            $this->pick($s, 'price', 'service_price'),
            $this->pick($c, 'breed', 'cat_breed'),
            $this->pick($p, 'phone_number', 'customer_phone'),
            $hasSlots ? $this->pick($sl, 'slot_date', 'slot_date') : DB::raw('null as slot_date'),
            $hasSlots ? $this->pick($sl, 'start_time', 'slot_start_time') : DB::raw('null as slot_start_time'),
            $hasSlots ? $this->pick($sl, 'end_time', 'slot_end_time') : DB::raw('null as slot_end_time'),
        ];

        return $q->select($select)->orderByDesc("$b.created_at")->limit(200)->get();
    }

    private function findBooking(string $bookingId, string $mitraId): ?object
    {
        $b = self::T_BOOKINGS;
        $s = self::T_SERVICES;

        $q = DB::table($b)
            ->leftJoin($s, "$s.id", '=', "$b.service_id")
            ->where("$b.id", $bookingId);

        $q->where(Schema::hasColumn($b, 'mitra_id') ? "$b.mitra_id" : "$s.mitra_id", $mitraId);

        return $q->select("$b.id", "$b.status")->first();
    }

    private function buildStats(Collection $bookings, Collection $services, string $mitraId): array
    {
        $today = Carbon::now(self::TZ)->toDateString();

        $todayCount = $bookings->filter(function ($bk) use ($today) {
            $date = $bk->slot_date
                ?? Carbon::parse($bk->created_at)->timezone(self::TZ)->toDateString();

            return substr((string) $date, 0, 10) === $today;
        })->count();

        $review = $this->safe(function () use ($mitraId) {
            if (!Schema::hasTable(self::T_REVIEWS) || !Schema::hasColumn(self::T_REVIEWS, 'mitra_id')) {
                return null;
            }

            return DB::table(self::T_REVIEWS)
                ->where('mitra_id', $mitraId)
                ->selectRaw('count(*) as total, avg(rating) as avg_rating')
                ->first();
        }, null);

        $totalReviews = (int) ($review->total ?? 0);

        return [
            'total_bookings' => $bookings->count(),
            'today_bookings' => $todayCount,
            'pending_bookings' => $bookings->where('status', 'menunggu')->count(),
            'serving_bookings' => $bookings->where('status', 'dilayani')->count(),
            'services_count' => $services->filter(fn($svc) => $svc->is_active ?? true)->count(),
            'total_reviews' => $totalReviews,
            'avg_rating' => ($totalReviews > 0 && $review->avg_rating !== null)
                ? number_format((float) $review->avg_rating, 1)
                : null,
        ];
    }

    /** Pilih kolom bila ada; bila tidak, kembalikan NULL agar query tidak error. */
    private function pick(string $table, string $column, string $alias)
    {
        return Schema::hasColumn($table, $column)
            ? DB::raw("{$table}.{$column} as {$alias}")
            : DB::raw("null as {$alias}");
    }

    private function safe(callable $fn, mixed $fallback): mixed
    {
        try {
            return $fn();
        } catch (\Throwable $e) {
            report($e);

            return $fallback;
        }
    }
}