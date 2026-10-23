<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
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

    private const T_SERVICES = 'mitra_services';

    private const T_CATS = 'cats';

    private const T_PROFILES = 'profiles';

    private const T_SLOTS = 'service_slots';

    private const T_HOURS = 'mitra_operating_hours';

    private const T_REVIEWS = 'reviews';

    public function index(Request $request, ?string $mitra = null): View|RedirectResponse
    {
        $userId = $request->session()->get('supabase.user.id');
        $requestedMitraId = strtolower((string) ($mitra ?: $request->query('mitra', $request->query('mitra_id'))));

        try {
            $userMitras = DB::table('mitras')
                ->where('owner_id', $userId)
                ->orderBy('name')
                ->get();
        } catch (\Throwable $e) {
            report($e);
            abort(503, 'Data mitra tidak dapat dimuat. Coba lagi.');
        }

        if ($userMitras->isEmpty()) {
            return view('owner.mitra.dashboard', [
                'userMitras' => collect(),
                'activeMitra' => null,
                'mitraId' => null,
                'currentTab' => 'beranda',
                'bookings' => collect(),
                'services' => collect(),
                'operatingHours' => collect(),
                'stats' => [
                    'total_bookings' => 0,
                    'today_bookings' => 0,
                    'pending_bookings' => 0,
                    'serving_bookings' => 0,
                    'services_count' => 0,
                    'total_reviews' => 0,
                    'avg_rating' => null,
                ],
            ]);
        }

        if ($requestedMitraId !== '') {
            $activeMitra = $userMitras->first(fn ($m) => strtolower((string) $m->id) === $requestedMitraId);
            abort_if(! $activeMitra, 404);
        } else {
            $activeMitra = $userMitras->firstWhere('status', 'approved') ?? $userMitras->first();
        }

        $mitraId = strtolower((string) $activeMitra->id);

        if ($activeMitra->status !== 'approved') {
            return redirect()->route('owner.mitra.status', ['mitra' => $mitraId]);
        }

        $tab = (string) $request->query('tab', 'beranda');
        $currentTab = in_array($tab, self::TABS, true) ? $tab : 'beranda';

        $bookings = $this->safe(fn () => $this->loadBookings($activeMitra->id), collect());
        $services = $this->safe(
            fn () => DB::table(self::T_SERVICES)->where('mitra_id', $activeMitra->id)->orderBy('name')->get(),
            collect()
        );
        $operatingHours = $this->safe(function () use ($activeMitra) {
            if (! Schema::hasTable(self::T_HOURS)) {
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
            fn () => DB::table('mitras')->where('id', strtolower($mitra))->where('owner_id', $userId)->first(),
            null
        );
        abort_if(! $activeMitra, 404);
        abort_if($activeMitra->status !== 'approved', 403);

        $back = Route::has('owner.mitra.dashboard.unit')
            ? redirect()->route('owner.mitra.dashboard.unit', ['mitra' => strtolower($mitra), 'tab' => 'booking'])
            : redirect()->route('owner.mitra.dashboard', ['mitra' => strtolower($mitra), 'tab' => 'booking']);

        $row = $this->safe(fn () => $this->findBooking($booking, $activeMitra->id), null);
        abort_if(! $row, 404);

        $allowed = self::TRANSITIONS[$row->status] ?? [];
        if (! in_array($data['status'], $allowed, true)) {
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

        if (! $affected) {
            return $back->with('error', 'Status booking sudah berubah. Muat ulang halaman.');
        }

        return $back->with('success', 'Status booking diperbarui menjadi "'.$data['status'].'".');
    }

    // ---------------------------------------------------------------------
    // Service CRUD
    // ---------------------------------------------------------------------

    /** Preset nama layanan per jenis mitra (mirror dari Flutter mitraTypeInfo). */
    private const SERVICE_PRESETS = [
        'PAW_HEALTH' => ['Konsultasi Umum', 'Vaksinasi', 'Sterilisasi', 'Pemeriksaan Gigi', 'Pemeriksaan Mata & Telinga', 'Rawat Inap', 'Operasi Minor', 'Cek Darah & Lab'],
        'PAW_GROOMING' => ['Grooming Basic', 'Grooming Full', 'Mandi + Blow', 'Potong Kuku', 'Bersihkan Telinga', 'Potong Bulu Wajah', 'Spa Kucing'],
        'PAW_HOTEL' => ['Kamar Standard', 'Kamar Deluxe', 'Kamar VIP', 'Suite'],
        'PAW_SHOP' => ['Konsultasi Nutrisi', 'Jasa Pengiriman', 'Paket Makanan Bulanan', 'Aksesoris Custom'],
    ];

    public function storeService(Request $request, string $mitra): RedirectResponse
    {
        $activeMitra = $this->verifyMitraOwner($request, $mitra);
        $back = $this->backToLayanan($mitra);

        $isHotel = strtoupper($activeMitra->mitra_type ?? '') === 'PAW_HOTEL';
        $data = $this->validateServiceRequest($request, $isHotel);

        try {
            DB::table(self::T_SERVICES)->insert([
                'id' => Str::uuid()->toString(),
                'mitra_id' => $activeMitra->id,
                'name' => $data['name'],
                'price' => $data['price'],
                'duration_minutes' => $data['duration_minutes'] ?? null,
                'capacity' => $data['capacity'] ?? null,
                'cats_per_room' => $data['cats_per_room'] ?? null,
                'room_class' => $data['room_class'] ?? null,
                'facilities' => $this->pgArray($data['facilities'] ?? []),
                'checkin_time' => $data['checkin_time'] ?? null,
                'checkout_time' => $data['checkout_time'] ?? null,
                'is_active' => true,
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            report($e);

            return $back->with('error', 'Gagal menyimpan layanan. Coba lagi.');
        }

        return $back->with('success', 'Layanan "'.$data['name'].'" berhasil ditambahkan.');
    }

    public function updateService(Request $request, string $mitra, string $service): RedirectResponse
    {
        $activeMitra = $this->verifyMitraOwner($request, $mitra);
        $back = $this->backToLayanan($mitra);

        $row = DB::table(self::T_SERVICES)->where('id', $service)->where('mitra_id', $activeMitra->id)->first();
        abort_if(! $row, 404);

        $isHotel = strtoupper($activeMitra->mitra_type ?? '') === 'PAW_HOTEL';
        $data = $this->validateServiceRequest($request, $isHotel);

        try {
            DB::table(self::T_SERVICES)->where('id', $service)->update([
                'name' => $data['name'],
                'price' => $data['price'],
                'duration_minutes' => $data['duration_minutes'] ?? null,
                'capacity' => $data['capacity'] ?? null,
                'cats_per_room' => $data['cats_per_room'] ?? null,
                'room_class' => $data['room_class'] ?? null,
                'facilities' => $this->pgArray($data['facilities'] ?? []),
                'checkin_time' => $data['checkin_time'] ?? null,
                'checkout_time' => $data['checkout_time'] ?? null,
            ]);
        } catch (\Throwable $e) {
            report($e);

            return $back->with('error', 'Gagal memperbarui layanan. Coba lagi.');
        }

        return $back->with('success', 'Layanan "'.$data['name'].'" berhasil diperbarui.');
    }

    public function destroyService(Request $request, string $mitra, string $service): RedirectResponse
    {
        $activeMitra = $this->verifyMitraOwner($request, $mitra);
        $back = $this->backToLayanan($mitra);

        $row = DB::table(self::T_SERVICES)->where('id', $service)->where('mitra_id', $activeMitra->id)->first();
        abort_if(! $row, 404);

        try {
            DB::table(self::T_SERVICES)->where('id', $service)->delete();
        } catch (\Throwable $e) {
            report($e);

            return $back->with('error', 'Gagal menghapus layanan. Coba lagi.');
        }

        return $back->with('success', 'Layanan "'.$row->name.'" berhasil dihapus.');
    }

    public function toggleService(Request $request, string $mitra, string $service): RedirectResponse
    {
        $activeMitra = $this->verifyMitraOwner($request, $mitra);
        $back = $this->backToLayanan($mitra);

        $row = DB::table(self::T_SERVICES)->where('id', $service)->where('mitra_id', $activeMitra->id)->first();
        abort_if(! $row, 404);

        $newState = ! ((bool) ($row->is_active ?? true));

        try {
            DB::table(self::T_SERVICES)->where('id', $service)->update(['is_active' => $newState]);
        } catch (\Throwable $e) {
            report($e);

            return $back->with('error', 'Gagal mengubah status layanan. Coba lagi.');
        }

        $label = $newState ? 'diaktifkan' : 'dinonaktifkan';

        return $back->with('success', 'Layanan "'.$row->name.'" berhasil '.$label.'.');
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
        $userCol = Schema::hasColumn($b, 'owner_id') ? 'owner_id' : 'user_id';
        $hasSlots = Schema::hasTable($sl) && Schema::hasColumn($b, 'slot_id');

        $q = DB::table($b)
            ->leftJoin($s, "$s.id", '=', "$b.service_id")
            ->leftJoin($c, "$c.id", '=', "$b.cat_id")
            ->leftJoin($p, "$p.id", '=', "$b.$userCol");

        if ($hasSlots) {
            $q->leftJoin($sl, "$sl.id", '=', "$b.slot_id");
        }

        $q->where($hasMitraCol ? "$b.mitra_id" : "$s.mitra_id", $mitraId);

        $hasSlotDateOnBookings = Schema::hasColumn($b, 'slot_date');

        $select = [
            "$b.*",
            DB::raw("COALESCE($s.name, ".(Schema::hasColumn($b, 'service_name') ? "$b.service_name" : 'null').') as service_name'),
            "$c.name as cat_name",
            "$p.full_name as customer_name",
            "$p.username as customer_username",
            $this->pick($s, 'price', 'service_price'),
            $this->pick($c, 'breed', 'cat_breed'),
            $this->pick($p, 'phone_number', 'customer_phone'),
            $hasSlotDateOnBookings
                ? DB::raw("$b.slot_date as slot_date")
                : ($hasSlots ? $this->pick($sl, 'date', 'slot_date') : DB::raw('null as slot_date')),
            Schema::hasColumn($b, 'slot_start_time')
                ? DB::raw("$b.slot_start_time as slot_start_time")
                : ($hasSlots ? $this->pick($sl, 'start_time', 'slot_start_time') : DB::raw('null as slot_start_time')),
            Schema::hasColumn($b, 'slot_end_time')
                ? DB::raw("$b.slot_end_time as slot_end_time")
                : ($hasSlots ? $this->pick($sl, 'end_time', 'slot_end_time') : DB::raw('null as slot_end_time')),
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
            if (! Schema::hasTable(self::T_REVIEWS)) {
                return null;
            }

            if (Schema::hasColumn(self::T_REVIEWS, 'mitra_id')) {
                return DB::table(self::T_REVIEWS)
                    ->where('mitra_id', $mitraId)
                    ->selectRaw('count(*) as total, avg(rating) as avg_rating')
                    ->first();
            }

            if (Schema::hasTable(self::T_BOOKINGS) && Schema::hasColumn(self::T_REVIEWS, 'booking_id')) {
                return DB::table(self::T_REVIEWS)
                    ->join(self::T_BOOKINGS, self::T_BOOKINGS.'.id', '=', self::T_REVIEWS.'.booking_id')
                    ->where(self::T_BOOKINGS.'.mitra_id', $mitraId)
                    ->selectRaw('count(*) as total, avg('.self::T_REVIEWS.'.rating) as avg_rating')
                    ->first();
            }

            return null;
        }, null);

        $totalReviews = (int) ($review->total ?? 0);

        return [
            'total_bookings' => $bookings->count(),
            'today_bookings' => $todayCount,
            'pending_bookings' => $bookings->where('status', 'menunggu')->count(),
            'serving_bookings' => $bookings->where('status', 'dilayani')->count(),
            'services_count' => $services->filter(fn ($svc) => $svc->is_active ?? true)->count(),
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

    /** Verify mitra belongs to the authenticated owner and return it. Aborts 403/404 otherwise. */
    private function verifyMitraOwner(Request $request, string $mitra): object
    {
        $userId = $request->session()->get('supabase.user.id');
        $row = $this->safe(
            fn () => DB::table('mitras')->where('id', strtolower($mitra))->where('owner_id', $userId)->first(),
            null
        );
        abort_if(! $row, 404);
        abort_if($row->status !== 'approved', 403);

        return $row;
    }

    private function backToLayanan(string $mitra): RedirectResponse
    {
        $route = Route::has('owner.mitra.dashboard.unit')
            ? route('owner.mitra.dashboard.unit', ['mitra' => strtolower($mitra), 'tab' => 'layanan'])
            : route('owner.mitra.dashboard', ['mitra' => strtolower($mitra), 'tab' => 'layanan']);

        return redirect($route);
    }

    /** Validate and normalise a service form request. */
    private function validateServiceRequest(Request $request, bool $isHotel): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:120'],
            'price' => ['required', 'numeric', 'min:1'],
            'duration_minutes' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:50'],
            'cats_per_room' => ['nullable', 'integer', 'min:1', 'max:10'],
            'room_class' => ['nullable', 'string', 'max:60'],
            'facilities' => ['nullable', 'array'],
            'facilities.*' => ['string', 'max:40'],
            'checkin_time' => ['nullable', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'checkout_time' => ['nullable', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
        ];

        $data = $request->validate($rules);

        // Normalise time fields to HH:MM:SS
        foreach (['checkin_time', 'checkout_time'] as $field) {
            if (! empty($data[$field]) && strlen($data[$field]) === 5) {
                $data[$field] .= ':00';
            }
        }

        if ($isHotel) {
            $data['duration_minutes'] = null;
        } else {
            $data['capacity'] = null;
            $data['cats_per_room'] = null;
            $data['room_class'] = null;
            $data['facilities'] = [];
            $data['checkin_time'] = null;
            $data['checkout_time'] = null;
        }

        return $data;
    }

    /**
     * Convert a PHP array of strings to a PostgreSQL array literal string,
     * e.g. ["AC","WiFi"] → '{"AC","WiFi"}'.
     */
    private function pgArray(array $items): string
    {
        if (empty($items)) {
            return '{}';
        }

        $escaped = array_map(function (string $item): string {
            return '"'.addcslashes($item, '"\\').'"';
        }, $items);

        return '{'.implode(',', $escaped).'}';
    }
}
