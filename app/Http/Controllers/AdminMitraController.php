<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Dashboard ADMIN KLINIK (member_role = 'admin_klinik' di mitra_members, khusus mitra PAW_HEALTH).
 *
 * Aturan akses:
 *  - Booking pelanggan masuk ke admin klinik (bukan admin PawKu): terima / tolak, lihat nomor antri,
 *    lalu teruskan ke dokter yang menangani.
 *  - Admin klinik TIDAK melihat pendapatan, harga, maupun rating. Karena itu query di sini
 *    sengaja tidak pernah memilih kolom service_price / tabel reviews.
 *
 * Penulisan ke database:
 *  - Laravel terhubung langsung ke Postgres sehingga auth.uid() kosong di dalam trigger.
 *    Setiap penulisan dibungkus transaksi + set_config('request.jwt.claim.sub') supaya trigger
 *    guard_booking_update / validate_booking_doctor mengenali admin klinik yang sedang login.
 *    Pengecekan keanggotaan di PHP adalah lapisan pertama, trigger database adalah lapisan kedua.
 */
class AdminMitraController extends Controller
{
    private const TZ = 'Asia/Jakarta';

    private const PER_PAGE = 20;

    /** Tanggal jadwal booking: tanggal slot, atau tanggal dibuat (WIB) bila tidak ada slot. */
    private const DATE_EXPR = "coalesce(b.slot_date, (b.created_at at time zone 'Asia/Jakarta')::date)";

    /** Tanggal booking MASUK (dibuat pemilik kucing), WIB. Dipakai untuk angka ringkasan dashboard. */
    private const CREATED_EXPR = "(b.created_at at time zone 'Asia/Jakarta')::date";

    private const STATUS_LABELS = [
        'menunggu' => 'Menunggu',
        'dikonfirmasi' => 'Dikonfirmasi',
        'dilayani' => 'Dilayani',
        'selesai' => 'Selesai',
        'ditolak' => 'Ditolak',
        'dibatalkan' => 'Dibatalkan',
        'kedaluwarsa' => 'Kedaluwarsa',
    ];

    /** Alur status yang boleh dilakukan admin klinik (sama dengan guard di database). */
    private const TRANSITIONS = [
        'menunggu' => ['dikonfirmasi', 'ditolak'],
        'dikonfirmasi' => ['dilayani', 'ditolak'],
        'dilayani' => ['selesai'],
    ];

    /** Dokter hanya boleh ditugaskan selama booking masih berjalan. */
    private const ASSIGNABLE = ['menunggu', 'dikonfirmasi', 'dilayani'];

    private const FILTERS = [
        'menunggu' => 'Menunggu',
        'aktif' => 'Aktif',
        'selesai' => 'Selesai',
        'batal' => 'Ditolak / Dibatalkan',
        'semua' => 'Semua',
    ];

    /** Pesan error dari trigger database yang aman ditampilkan apa adanya. */
    private const DB_MESSAGES = [
        'Pilih dokter terlebih dahulu sebelum booking dilayani',
        'Dokter tidak terdaftar atau belum terverifikasi di klinik ini',
        'Dokter hanya bisa ditugaskan pada booking yang masih berjalan',
        'Hanya admin klinik yang boleh menugaskan dokter',
        'Perubahan status tidak diizinkan',
        'Data booking ini tidak boleh diubah',
        'Tidak diizinkan mengubah booking ini',
    ];

    // ---------------------------------------------------------------------
    // Halaman
    // ---------------------------------------------------------------------

    /** /admin-mitra — arahkan ke dashboard klinik pertama (atau yang dipilih lewat ?mitra=). */
    public function index(Request $request): RedirectResponse
    {
        $clinics = $this->clinicsOf($request);
        abort_if($clinics->isEmpty(), 403, 'Akun Anda belum terdaftar sebagai admin klinik.');

        $requested = strtolower((string) $request->query('mitra', ''));
        $target = $clinics->first(fn ($c) => strtolower((string) $c->id) === $requested) ?? $clinics->first();

        return redirect()->route('admin_mitra.dashboard', ['mitra' => $target->id]);
    }

    public function dashboard(Request $request, string $mitra): View
    {
        [$clinic, $clinics] = $this->resolveClinic($request, $mitra);

        $now = Carbon::now(self::TZ);
        $today = $now->toDateString();
        $d7 = $now->copy()->subDays(6)->toDateString();
        $d30 = $now->copy()->subDays(29)->toDateString();
        $d = self::DATE_EXPR;
        $c = self::CREATED_EXPR;

        // Ringkasan angka berdasarkan tanggal booking MASUK (dibuat), tidak menghitung kedaluwarsa.
        // "Terjadwal" = booking berjalan yang jadwalnya setelah hari ini.
        $stats = $this->safe(fn () => DB::table('bookings as b')
            ->where('b.mitra_id', $clinic->id)
            ->selectRaw("
                count(*) filter (where $c = ?::date and b.status <> 'kedaluwarsa') as today,
                count(*) filter (where $c between ?::date and ?::date and b.status <> 'kedaluwarsa') as week,
                count(*) filter (where $c between ?::date and ?::date and b.status <> 'kedaluwarsa') as month,
                count(*) filter (where $c >= ?::date and b.status in ('dikonfirmasi', 'dilayani')) as aktif,
                count(*) filter (where $c >= ?::date and b.status = 'menunggu') as menunggu,
                count(*) filter (where $c >= ?::date and b.status = 'selesai') as selesai,
                count(*) filter (where $c >= ?::date and b.status = 'ditolak') as ditolak,
                count(*) filter (where $c >= ?::date and b.status = 'dibatalkan') as dibatalkan,
                count(*) filter (where $d > ?::date and b.status in ('menunggu', 'dikonfirmasi', 'dilayani')) as terjadwal
            ", [$today, $d7, $today, $d30, $today, $d30, $d30, $d30, $d30, $d30, $today])
            ->first(), null);

        $stats = [
            'today' => (int) ($stats->today ?? 0),
            'week' => (int) ($stats->week ?? 0),
            'month' => (int) ($stats->month ?? 0),
            'aktif' => (int) ($stats->aktif ?? 0),
            'menunggu' => (int) ($stats->menunggu ?? 0),
            'selesai' => (int) ($stats->selesai ?? 0),
            'ditolak' => (int) ($stats->ditolak ?? 0),
            'dibatalkan' => (int) ($stats->dibatalkan ?? 0),
            'terjadwal' => (int) ($stats->terjadwal ?? 0),
        ];

        // Jadwal hari ini (tanpa booking ditolak / dibatalkan / kedaluwarsa)
        $schedule = $this->safe(fn () => $this->bookingQuery($clinic->id)
            ->whereRaw("$d = ?::date", [$today])
            ->whereIn('b.status', ['menunggu', 'dikonfirmasi', 'dilayani', 'selesai'])
            ->orderByRaw('b.slot_start_time asc nulls last')
            ->orderBy('b.queue_number')
            ->get(), collect());

        // Layanan terlaris 30 hari terakhir (jumlah booking saja, tanpa harga)
        $topServices = $this->safe(fn () => DB::table('bookings as b')
            ->leftJoin('mitra_services as s', 's.id', '=', 'b.service_id')
            ->where('b.mitra_id', $clinic->id)
            ->whereRaw("$c between ?::date and ?::date", [$d30, $today])
            ->whereNotIn('b.status', ['ditolak', 'dibatalkan', 'kedaluwarsa'])
            ->selectRaw("coalesce(b.service_name, s.name, 'Layanan dihapus') as name, count(*) as total")
            ->groupByRaw("coalesce(b.service_name, s.name, 'Layanan dihapus')")
            ->orderByDesc('total')
            ->limit(5)
            ->get(), collect());

        return view('mitra.admin_mitra.dashboard', [
            'clinic' => $clinic,
            'clinics' => $clinics,
            'stats' => $stats,
            'schedule' => $schedule,
            'topServices' => $topServices,
            'statusLabels' => self::STATUS_LABELS,
        ]);
    }

    public function booking(Request $request, string $mitra): View
    {
        [$clinic, $clinics] = $this->resolveClinic($request, $mitra);

        $filter = (string) $request->query('filter', 'menunggu');
        if (! array_key_exists($filter, self::FILTERS)) {
            $filter = 'menunggu';
        }

        $q = $this->bookingQuery($clinic->id);

        match ($filter) {
            'menunggu' => $q->where('b.status', 'menunggu'),
            'aktif' => $q->whereIn('b.status', ['dikonfirmasi', 'dilayani']),
            'selesai' => $q->where('b.status', 'selesai'),
            'batal' => $q->whereIn('b.status', ['ditolak', 'dibatalkan']),
            default => null,
        };

        // Yang perlu ditindak: urut jadwal terdekat. Riwayat: urut terbaru.
        if (in_array($filter, ['menunggu', 'aktif'], true)) {
            $q->orderByRaw(self::DATE_EXPR.' asc')
                ->orderByRaw('b.slot_start_time asc nulls last')
                ->orderBy('b.queue_number');
        } else {
            $q->orderByDesc('b.created_at');
        }

        $bookings = $this->safe(
            fn () => $q->simplePaginate(self::PER_PAGE)->withQueryString(),
            null
        );
        abort_if($bookings === null, 503, 'Data booking tidak dapat dimuat. Coba lagi.');

        // Angka di tab filter (seluruh waktu)
        $byStatus = $this->safe(fn () => DB::table('bookings')
            ->where('mitra_id', $clinic->id)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status'), collect());

        $count = fn (array $statuses) => (int) collect($statuses)->sum(fn ($s) => (int) ($byStatus[$s] ?? 0));
        $tabs = [];
        foreach (self::FILTERS as $key => $label) {
            $tabs[] = [
                'key' => $key,
                'label' => $label,
                'count' => match ($key) {
                    'menunggu' => $count(['menunggu']),
                    'aktif' => $count(['dikonfirmasi', 'dilayani']),
                    'selesai' => $count(['selesai']),
                    'batal' => $count(['ditolak', 'dibatalkan']),
                    default => (int) $byStatus->sum(),
                },
            ];
        }

        // Dokter yang boleh ditugaskan: member role dokter, terverifikasi, aktif
        $doctors = $this->safe(fn () => DB::table('mitra_members as mm')
            ->join('profiles as p', 'p.id', '=', 'mm.user_id')
            ->where('mm.mitra_id', $clinic->id)
            ->where('mm.member_role', 'dokter')
            ->where('mm.verification_status', 'verified')
            ->where('mm.is_active', true)
            ->select('mm.user_id', 'p.full_name', 'p.username', 'mm.specialization')
            ->orderBy('p.full_name')
            ->get(), collect());

        $pendingDoctors = (int) $this->safe(fn () => DB::table('mitra_members')
            ->where('mitra_id', $clinic->id)
            ->where('member_role', 'dokter')
            ->where('verification_status', 'pending')
            ->count(), 0);

        return view('mitra.admin_mitra.booking', [
            'clinic' => $clinic,
            'clinics' => $clinics,
            'bookings' => $bookings,
            'filter' => $filter,
            'tabs' => $tabs,
            'doctors' => $doctors,
            'pendingDoctors' => $pendingDoctors,
            'assignable' => self::ASSIGNABLE,
            'statusLabels' => self::STATUS_LABELS,
        ]);
    }

    // ---------------------------------------------------------------------
    // Aksi
    // ---------------------------------------------------------------------

    /** Terima / tolak / mulai layani / selesai. */
    public function updateStatus(Request $request, string $mitra, string $booking): RedirectResponse
    {
        [$clinic] = $this->resolveClinic($request, $mitra);

        $data = $request->validate([
            'status' => ['required', 'in:dikonfirmasi,ditolak,dilayani,selesai'],
        ]);

        $row = $this->findBooking($booking, (string) $clinic->id);
        abort_if(! $row, 404);

        $allowed = self::TRANSITIONS[$row->status] ?? [];
        if (! in_array($data['status'], $allowed, true)) {
            return back()->with('error', 'Perubahan status tidak diizinkan untuk booking ini.');
        }

        // Mulai layani & selesai wajib sudah ada dokter yang menangani.
        if (in_array($data['status'], ['dilayani', 'selesai'], true) && empty($row->assigned_doctor_id)) {
            return back()->with('error', 'Pilih dokter terlebih dahulu sebelum booking dilayani.');
        }

        try {
            $affected = $this->writeAsUser($request, function () use ($row, $data) {
                // Syarat status lama mencegah dua klik / dua admin saling menimpa.
                return DB::table('bookings')
                    ->where('id', $row->id)
                    ->where('status', $row->status)
                    ->update(['status' => $data['status']]);
            });
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', $this->friendlyDbError($e, 'Gagal memperbarui status booking. Coba lagi.'));
        }

        if (! $affected) {
            return back()->with('error', 'Status booking sudah berubah. Muat ulang halaman.');
        }

        $label = strtolower(self::STATUS_LABELS[$data['status']] ?? $data['status']);

        return back()->with('success', 'Booking berhasil diubah menjadi "'.$label.'".');
    }

    /** Teruskan booking ke dokter (atau kosongkan pilihan dokter). */
    public function assignDoctor(Request $request, string $mitra, string $booking): RedirectResponse
    {
        [$clinic] = $this->resolveClinic($request, $mitra);

        $data = $request->validate([
            'doctor_id' => ['nullable', 'uuid'],
        ]);
        $doctorId = ! empty($data['doctor_id']) ? strtolower($data['doctor_id']) : null;

        $row = $this->findBooking($booking, (string) $clinic->id);
        abort_if(! $row, 404);

        if (! in_array($row->status, self::ASSIGNABLE, true)) {
            return back()->with('error', 'Dokter hanya bisa dipilih untuk booking yang masih berjalan.');
        }

        $doctorName = null;
        if ($doctorId !== null) {
            $doctorName = $this->safe(fn () => DB::table('profiles')->where('id', $doctorId)->value('full_name'), null);
        }

        try {
            $this->writeAsUser($request, function () use ($row, $doctorId) {
                return DB::table('bookings')
                    ->where('id', $row->id)
                    ->update(['assigned_doctor_id' => $doctorId]);
            });
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', $this->friendlyDbError($e, 'Gagal menugaskan dokter. Coba lagi.'));
        }

        return back()->with('success', $doctorId === null
            ? 'Pilihan dokter dikosongkan.'
            : 'Booking diteruskan ke '.($doctorName ?: 'dokter terpilih').'.');
    }

    // ---------------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------------

    /** Query dasar daftar booking (tanpa harga). */
    private function bookingQuery(string $mitraId)
    {
        return DB::table('bookings as b')
            ->leftJoin('cats as c', 'c.id', '=', 'b.cat_id')
            ->leftJoin('profiles as p', 'p.id', '=', 'b.owner_id')
            ->leftJoin('profiles as dr', 'dr.id', '=', 'b.assigned_doctor_id')
            ->where('b.mitra_id', $mitraId)
            ->select(
                'b.id',
                'b.queue_number',
                'b.status',
                'b.notes',
                'b.service_name',
                'b.slot_date',
                'b.slot_start_time',
                'b.slot_end_time',
                'b.created_at',
                'b.assigned_doctor_id',
                'c.name as cat_name',
                'c.breed as cat_breed',
                'p.full_name as owner_name',
                'p.username as owner_username',
                'p.phone_number as owner_phone',
                'dr.full_name as doctor_name'
            );
    }

    private function findBooking(string $bookingId, string $mitraId): ?object
    {
        return $this->safe(fn () => DB::table('bookings')
            ->where('id', strtolower($bookingId))
            ->where('mitra_id', $mitraId)
            ->select('id', 'status', 'assigned_doctor_id')
            ->first(), null);
    }

    /** Klinik (PAW_HEALTH, approved) tempat user login terdaftar sebagai admin klinik aktif. */
    private function clinicsOf(Request $request): Collection
    {
        $userId = $request->session()->get('supabase.user.id');
        abort_if(! $userId, 403);

        try {
            return DB::table('mitra_members as mm')
                ->join('mitras as m', 'm.id', '=', 'mm.mitra_id')
                ->where('mm.user_id', $userId)
                ->where('mm.member_role', 'admin_klinik')
                ->where('mm.is_active', true)
                ->where('m.status', 'approved')
                ->where('m.mitra_type', 'PAW_HEALTH')
                ->select('m.id', 'm.name', 'm.mitra_type', 'm.status', 'm.logo_url', 'm.owner_id')
                ->orderBy('m.name')
                ->get();
        } catch (\Throwable $e) {
            report($e);
            abort(503, 'Data klinik tidak dapat dimuat. Coba lagi.');
        }
    }

    /**
     * Pastikan user login adalah admin klinik untuk klinik ini.
     * Klinik juga disimpan ke request attribute 'mitra' karena layout mitra membacanya dari sana.
     *
     * @return array{0: object, 1: Collection}
     */
    private function resolveClinic(Request $request, string $mitra): array
    {
        $clinics = $this->clinicsOf($request);
        $wanted = strtolower($mitra);

        $clinic = $clinics->first(fn ($c) => strtolower((string) $c->id) === $wanted);
        abort_if(! $clinic, 404);

        $request->attributes->set('mitra', $clinic);

        return [$clinic, $clinics];
    }

    /**
     * Jalankan penulisan di dalam transaksi dengan identitas admin klinik yang sedang login,
     * sehingga auth.uid() terbaca di trigger database.
     */
    private function writeAsUser(Request $request, callable $write): mixed
    {
        $userId = (string) $request->session()->get('supabase.user.id');

        return DB::transaction(function () use ($userId, $write) {
            DB::select(
                "select set_config('request.jwt.claim.sub', ?, true), set_config('request.jwt.claims', ?, true)",
                [$userId, json_encode(['sub' => $userId, 'role' => 'authenticated'])]
            );

            return $write();
        });
    }

    /** Ambil pesan trigger database yang ramah pengguna; selain itu pakai pesan cadangan. */
    private function friendlyDbError(\Throwable $e, string $fallback): string
    {
        foreach (self::DB_MESSAGES as $message) {
            if (str_contains($e->getMessage(), $message)) {
                return $message.'.';
            }
        }

        return $fallback;
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