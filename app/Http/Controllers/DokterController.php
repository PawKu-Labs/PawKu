<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Portal Dokter Hewan.
 *
 * Akses: user yang terdaftar sebagai dokter (member_role = 'dokter') di minimal
 * satu klinik PAW_HEALTH yang sudah disetujui.
 *
 * Session keys yang digunakan:
 *  - supabase.user.id           → UUID akun Supabase
 *  - supabase.profile           → baris profil
 *  - dokter.active_clinic       → UUID klinik aktif (dipilih via switch)
 *
 * Penulisan ke database dibungkus writeAsUser() agar auth.uid() terbaca trigger.
 */
class DokterController extends Controller
{
    private const TZ = 'Asia/Jakarta';

    private const PER_PAGE = 20;

    // ─── Dashboard ───────────────────────────────────────────────────

    public function dashboard(Request $request): View
    {
        [$membership, $clinic, $clinics] = $this->resolveDoctor($request);

        $now = Carbon::now(self::TZ);
        $today = $now->toDateString();
        $weekEnd = $now->copy()->addDays(6)->toDateString();

        $userId = $this->userId($request);

        // Statistik hari ini
        $stats = $this->safe(function () use ($clinic, $userId, $today) {
            return DB::table('bookings as b')
                ->where('b.mitra_id', $clinic->id)
                ->where('b.assigned_doctor_id', $userId)
                ->selectRaw("
                    count(*) filter (where b.slot_date = ?::date and b.status not in ('ditolak','dibatalkan','kedaluwarsa')) as antrian,
                    count(*) filter (where b.slot_date = ?::date and b.status = 'selesai') as selesai,
                    count(*) filter (where b.assigned_doctor_id = ? and b.status not in ('ditolak','dibatalkan','kedaluwarsa','selesai')) as draft,
                    0 as kontrol
                ", [$today, $today, $userId])
                ->first();
        }, null);

        // Hitung kontrol minggu ini dari medical_records
        $kontrolCount = $this->safe(fn() => DB::table('medical_records')
            ->where('author_id', $userId)
            ->where('mitra_id', $clinic->id)
            ->whereNotNull('next_visit_date')
            ->whereBetween('next_visit_date', [$today, $weekEnd])
            ->count(), 0);

        $statsData = [
            'antrian' => (int) ($stats->antrian ?? 0),
            'selesai' => (int) ($stats->selesai ?? 0),
            'draft' => $this->safe(fn() => DB::table('medical_records')
                ->where('author_id', $userId)
                ->where('mitra_id', $clinic->id)
                ->where('status', 'draft')
                ->count(), 0),
            'kontrol' => $kontrolCount,
        ];

        // Antrian hari ini (booking yang di-assign ke dokter ini)
        $antrian = $this->safe(fn() => DB::table('bookings as b')
            ->leftJoin('cats as c', 'c.id', '=', 'b.cat_id')
            ->leftJoin('profiles as p', 'p.id', '=', 'b.owner_id')
            ->leftJoin('cat_health_conditions as chc', function ($j) {
                $j->on('chc.cat_id', '=', 'c.id')
                    ->where('chc.condition_type', 'alergi')
                    ->where('chc.is_active', true);
            })
            ->where('b.mitra_id', $clinic->id)
            ->where('b.assigned_doctor_id', $userId)
            ->where('b.slot_date', $today)
            ->whereNotIn('b.status', ['ditolak', 'dibatalkan', 'kedaluwarsa'])
            ->select(
                'b.id',
                'b.slot_start_time',
                'b.service_name',
                'b.status',
                'c.id as cat_id',
                'c.name as cat_name',
                'c.breed as cat_breed',
                'p.full_name as owner_name',
                'p.username as owner_username',
            )
            ->selectRaw("string_agg(distinct chc.condition_name, ', ') as alergi_list")
            ->groupBy(
                'b.id',
                'b.slot_start_time',
                'b.service_name',
                'b.status',
                'c.id',
                'c.name',
                'c.breed',
                'p.full_name',
                'p.username',
            )
            ->orderByRaw('b.slot_start_time asc nulls last')
            ->orderBy('b.id')
            ->limit(10)
            ->get(), collect());

        // Draf rekam medis belum final
        $draftList = $this->safe(fn() => DB::table('medical_records as mr')
            ->leftJoin('cats as c', 'c.id', '=', 'mr.cat_id')
            ->where('mr.author_id', $userId)
            ->where('mr.mitra_id', $clinic->id)
            ->where('mr.status', 'draft')
            ->select(
                'mr.id',
                'c.name as cat_name',
                'mr.created_at',
                'mr.chief_complaint',
            )
            ->orderByDesc('mr.created_at')
            ->limit(5)
            ->get(), collect());

        // Jadwal kontrol & vaksin minggu ini
        $kontrol = $this->safe(fn() => DB::table('medical_records as mr')
            ->leftJoin('cats as c', 'c.id', '=', 'mr.cat_id')
            ->where('mr.author_id', $userId)
            ->where('mr.mitra_id', $clinic->id)
            ->whereNotNull('mr.next_visit_date')
            ->whereBetween('mr.next_visit_date', [$today, $weekEnd])
            ->select(
                'c.name as cat_name',
                'mr.next_visit_date',
                'mr.follow_up_advice',
                'mr.diagnosis_primary',
            )
            ->orderBy('mr.next_visit_date')
            ->limit(5)
            ->get(), collect());

        // Session data untuk layout
        $this->syncLayoutSession($request, $clinics, $clinic, $membership);

        return view('dokter.dashboard', [
            'stats' => $statsData,
            'antrian' => $antrian->map(fn($a) => $this->formatAntrianRow($a)),
            'draftList' => $draftList->map(fn($d) => [
                'cat' => $d->cat_name ?? 'Kucing',
                'tanggal' => $this->formatTanggal($d->created_at),
                'keluhan' => $d->chief_complaint ?? '-',
            ]),
            'kontrol' => $kontrol->map(fn($k) => [
                'cat' => $k->cat_name ?? 'Kucing',
                'tanggal' => $this->formatDate($k->next_visit_date),
                'alasan' => $k->follow_up_advice ?: ($k->diagnosis_primary ? 'Kontrol ' . $k->diagnosis_primary : 'Kontrol'),
            ]),
        ]);
    }

    // ─── Pasien & Antrian ────────────────────────────────────────────

    public function pasien(Request $request): View
    {
        [$membership, $clinic, $clinics] = $this->resolveDoctor($request);

        $userId = $this->userId($request);
        $today = Carbon::now(self::TZ)->toDateString();
        $q = trim((string) $request->query('q', ''));

        // Antrian hari ini
        $antrian = $this->safe(fn() => DB::table('bookings as b')
            ->leftJoin('cats as c', 'c.id', '=', 'b.cat_id')
            ->leftJoin('profiles as p', 'p.id', '=', 'b.owner_id')
            ->leftJoin('cat_health_conditions as chc', function ($j) {
                $j->on('chc.cat_id', '=', 'c.id')
                    ->where('chc.condition_type', 'alergi')
                    ->where('chc.is_active', true);
            })
            ->where('b.mitra_id', $clinic->id)
            ->where('b.assigned_doctor_id', $userId)
            ->where('b.slot_date', $today)
            ->whereNotIn('b.status', ['ditolak', 'dibatalkan', 'kedaluwarsa'])
            ->select(
                'b.id',
                'b.slot_start_time',
                'b.service_name',
                'b.status',
                'c.id as cat_id',
                'c.name as cat_name',
                'c.breed as cat_breed',
                'c.birth_date',
                'c.gender',
                'c.is_neutered',
                'p.full_name as owner_name',
                'p.username as owner_username',
            )
            ->selectRaw("string_agg(distinct chc.condition_name, ', ') as alergi_list")
            ->groupBy(
                'b.id',
                'b.slot_start_time',
                'b.service_name',
                'b.status',
                'c.id',
                'c.name',
                'c.breed',
                'c.birth_date',
                'c.gender',
                'c.is_neutered',
                'p.full_name',
                'p.username',
            )
            ->orderByRaw('b.slot_start_time asc nulls last')
            ->orderBy('b.id')
            ->get(), collect());

        // Semua pasien yang pernah diperiksa dokter ini di klinik ini
        $pasienQuery = DB::table('medical_records as mr')
            ->join('cats as c', 'c.id', '=', 'mr.cat_id')
            ->join('profiles as p', 'p.id', '=', 'c.owner_id')
            ->leftJoin('cat_health_conditions as chc', function ($j) {
                $j->on('chc.cat_id', '=', 'c.id')
                    ->where('chc.condition_type', 'kronis')
                    ->where('chc.is_active', true);
            })
            ->where('mr.author_id', $userId)
            ->where('mr.mitra_id', $clinic->id);

        if ($q !== '') {
            $like = '%' . $q . '%';
            $pasienQuery->where(function ($w) use ($like) {
                $w->whereRaw('lower(c.name) like lower(?)', [$like])
                    ->orWhereRaw('lower(p.full_name) like lower(?)', [$like])
                    ->orWhereRaw('lower(p.username) like lower(?)', [$like]);
            });
        }

        $pasien = $this->safe(fn() => $pasienQuery
            ->select(
                'c.id',
                'c.name as nama',
                'c.breed as ras',
                'c.gender',
                'c.birth_date',
                'c.is_neutered',
                'c.weight as berat',
                'p.full_name as owner',
                'p.username as owner_username',
            )
            ->selectRaw("string_agg(distinct chc.condition_name, ', ') as kondisi")
            ->groupBy('c.id', 'c.name', 'c.breed', 'c.gender', 'c.birth_date', 'c.is_neutered', 'c.weight', 'p.full_name', 'p.username')
            ->orderBy('c.name')
            ->get(), collect());

        $this->syncLayoutSession($request, $clinics, $clinic, $membership);

        return view('dokter.index', [
            'antrian' => $antrian->map(fn($a) => $this->formatAntrianRowFull($a)),
            'pasien' => $pasien->map(fn($p) => [
                'id' => $p->id,
                'nama' => $p->nama,
                'ras' => $p->ras ?? 'Domestik',
                'jk' => $p->gender === 'betina' ? 'Betina' : 'Jantan',
                'steril' => $p->is_neutered ? 'Sudah' : 'Belum',
                'berat' => $p->berat ? number_format((float) $p->berat, 1, ',', '') : '-',
                'owner' => $p->owner ?? $p->owner_username ?? '-',
                'kondisi' => $p->kondisi ? explode(', ', $p->kondisi) : [],
            ])->values()->toArray(),
            'q' => $q,
        ]);
    }

    public function pasienShow(Request $request, string $id): View
    {
        [$membership, $clinic, $clinics] = $this->resolveDoctor($request);

        $userId = $this->userId($request);

        // Ambil data kucing
        $cat = $this->safe(fn() => DB::table('cats as c')
            ->join('profiles as p', 'p.id', '=', 'c.owner_id')
            ->where('c.id', strtolower($id))
            ->select(
                'c.id',
                'c.name',
                'c.breed',
                'c.birth_date',
                'c.gender',
                'c.is_neutered',
                'c.weight',
                'c.color',
                'c.microchip_id',
                'c.blood_type',
                'c.photo_url',
                'p.full_name as owner_name',
                'p.username as owner_username',
                'c.owner_id',
            )
            ->first(), null);

        abort_if(!$cat, 404, 'Pasien tidak ditemukan.');

        // Verifikasi akses: dokter pernah menangani kucing ini di klinik ini,
        // atau pemilik memberikan akses via medical_record_access
        $hasAccess = $this->safe(function () use ($userId, $clinic, $cat) {
            $handledBefore = DB::table('medical_records')
                ->where('cat_id', $cat->id)
                ->where('mitra_id', $clinic->id)
                ->where('author_id', $userId)
                ->exists();

            if ($handledBefore) {
                return true;
            }

            // Atau booking yang di-assign ke dokter ini
            $assignedBooking = DB::table('bookings')
                ->where('cat_id', $cat->id)
                ->where('mitra_id', $clinic->id)
                ->where('assigned_doctor_id', $userId)
                ->exists();

            if ($assignedBooking) {
                return true;
            }

            // Atau akses diberikan owner
            return DB::table('medical_record_access')
                ->where('cat_id', $cat->id)
                ->where('mitra_id', $clinic->id)
                ->whereNull('revoked_at')
                ->where(function ($w) {
                    $w->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                })
                ->exists();
        }, false);

        abort_if(!$hasAccess, 403, 'Anda tidak memiliki akses ke riwayat pasien ini.');

        $umur = $this->hitungUmur($cat->birth_date);

        $catData = [
            'id' => $cat->id,
            'nama' => $cat->name,
            'ras' => $cat->breed ?? 'Domestik',
            'umur' => $umur,
            'jk' => $cat->gender === 'betina' ? 'Betina' : 'Jantan',
            'steril' => $cat->is_neutered ? 'Sudah' : 'Belum',
            'berat' => $cat->weight ? number_format((float) $cat->weight, 1, ',', '') : '-',
            'warna' => $cat->color ?? '-',
            'microchip' => $cat->microchip_id ?: '-',
            'golongan' => $cat->blood_type ?: '-',
            'owner' => $cat->owner_name ?? $cat->owner_username ?? '-',
            'alergi' => [],
            'kondisi' => [],
        ];

        // Ambil alergi
        $catData['alergi'] = $this->safe(fn() => DB::table('cat_health_conditions')
            ->where('cat_id', $cat->id)
            ->where('condition_type', 'alergi')
            ->where('is_active', true)
            ->pluck('condition_name')
            ->toArray(), []);

        // Ambil kondisi kronis
        $catData['kondisi'] = $this->safe(fn() => DB::table('cat_health_conditions')
            ->where('cat_id', $cat->id)
            ->where('condition_type', 'kronis')
            ->where('is_active', true)
            ->pluck('condition_name')
            ->toArray(), []);

        // Timeline: rekam medis + catatan owner + vaksin
        $timeline = $this->buildTimeline($cat->id, $clinic->id);

        // Dokumen dari owner (medical_attachments dengan uploader_role = 'owner')
        $attachments = $this->safe(fn() => DB::table('medical_attachments')
            ->where('cat_id', $cat->id)
            ->where('uploader_role', 'owner')
            ->select('id', 'file_name', 'file_type', 'verification_status', 'created_at')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get(), collect());

        // Log akses
        $this->safe(function () use ($userId, $cat) {
            DB::table('medical_record_access_logs')->insert([
                'id' => Str::uuid()->toString(),
                'cat_id' => $cat->id,
                'accessor_id' => $userId,
                'action' => 'view',
                'created_at' => now(),
            ]);
        }, null);

        $this->syncLayoutSession($request, $clinics, $clinic, $membership);

        return view('dokter.show', [
            'cat' => $catData,
            'timeline' => $timeline,
            'attachments' => $attachments,
        ]);
    }

    // ─── Pemeriksaan Baru ────────────────────────────────────────────

    /**
     * PERBAIKAN:
     *  - Sumber pasien diperluas: booking aktif (menunggu/dikonfirmasi/dilayani)
     *    mulai kemarin ke depan, bukan hanya booking hari ini.
     *  - Tidak lagi 404 saat daftar kosong; redirect ke halaman pasien dengan pesan.
     *  - Exception query dilaporkan ke log (lihat storage/logs/laravel.log).
     */
    public function pemeriksaanCreate(Request $request): View|RedirectResponse
    {
        [$membership, $clinic, $clinics] = $this->resolveDoctor($request);

        $userId = $this->userId($request);
        $since = Carbon::now(self::TZ)->subDay()->toDateString();
        $selectedCat = strtolower((string) $request->query('cat', ''));
        $bookingId = strtolower((string) $request->query('booking', ''));

        // Kucing yang bisa diperiksa: dari booking aktif + yang pernah ditangani
        $daftarKucing = $this->safe(function () use ($userId, $clinic, $since) {
            // Kucing dari booking aktif yang di-assign ke dokter ini
            $fromBooking = DB::table('bookings as b')
                ->join('cats as c', 'c.id', '=', 'b.cat_id')
                ->join('profiles as p', 'p.id', '=', 'b.owner_id')
                ->where('b.mitra_id', $clinic->id)
                ->where('b.assigned_doctor_id', $userId)
                ->whereIn('b.status', ['menunggu', 'dikonfirmasi', 'dilayani'])
                ->where('b.slot_date', '>=', $since)
                ->select(
                    'c.id',
                    'c.name',
                    'c.breed',
                    'c.birth_date',
                    'c.gender',
                    'c.is_neutered',
                    'c.weight',
                    'c.color',
                    'c.microchip_id',
                    'c.blood_type',
                    'p.full_name as owner',
                    'p.username as owner_username',
                    'b.id as booking_id',
                )
                ->orderBy('b.slot_date')
                ->orderByRaw('b.slot_start_time asc nulls last')
                ->get()
                ->unique('id')
                ->values();

            // Kucing yang pernah ditangani dokter ini
            $fromRecords = DB::table('medical_records as mr')
                ->join('cats as c', 'c.id', '=', 'mr.cat_id')
                ->join('profiles as p', 'p.id', '=', 'c.owner_id')
                ->where('mr.author_id', $userId)
                ->where('mr.mitra_id', $clinic->id)
                ->when($fromBooking->isNotEmpty(), function ($q) use ($fromBooking) {
                    $q->whereNotIn('c.id', $fromBooking->pluck('id')->all());
                })
                ->select(
                    'c.id',
                    'c.name',
                    'c.breed',
                    'c.birth_date',
                    'c.gender',
                    'c.is_neutered',
                    'c.weight',
                    'c.color',
                    'c.microchip_id',
                    'c.blood_type',
                    'p.full_name as owner',
                    'p.username as owner_username',
                )
                ->selectRaw('null::uuid as booking_id')
                ->groupBy(
                    'c.id',
                    'c.name',
                    'c.breed',
                    'c.birth_date',
                    'c.gender',
                    'c.is_neutered',
                    'c.weight',
                    'c.color',
                    'c.microchip_id',
                    'c.blood_type',
                    'p.full_name',
                    'p.username',
                )
                ->get();

            return $fromBooking->concat($fromRecords)->values();
        }, collect());

        // Tidak ada pasien: jangan 404, arahkan dengan pesan yang jelas
        if ($daftarKucing->isEmpty()) {
            return redirect()
                ->route('dokter.pasien')
                ->with('error', 'Belum ada pasien yang bisa diperiksa. Tunggu booking diteruskan admin klinik.');
        }

        // Pilih kucing aktif
        $cat = null;
        if ($selectedCat !== '') {
            $cat = $daftarKucing->first(fn($k) => strtolower((string) $k->id) === $selectedCat);
        }
        if (!$cat && $bookingId !== '') {
            $cat = $daftarKucing->first(fn($k) => strtolower((string) ($k->booking_id ?? '')) === $bookingId);
        }
        if (!$cat) {
            $cat = $daftarKucing->first();
        }

        // Map ke format view
        $kucingMap = [];
        foreach ($daftarKucing as $k) {
            $kucingMap[$k->id] = $this->mapCatToViewFormat($k);
        }

        $catView = $kucingMap[$cat->id] ?? $this->mapCatToViewFormat($cat);

        // Ambil alergi & kondisi kronis
        $catView['alergi'] = $this->safe(fn() => DB::table('cat_health_conditions')
            ->where('cat_id', $cat->id)
            ->where('condition_type', 'alergi')
            ->where('is_active', true)
            ->pluck('condition_name')
            ->toArray(), []);

        $catView['kondisi'] = $this->safe(fn() => DB::table('cat_health_conditions')
            ->where('cat_id', $cat->id)
            ->whereIn('condition_type', ['kronis', 'kondisi_khusus'])
            ->where('is_active', true)
            ->pluck('condition_name')
            ->toArray(), []);

        // Ambil daftar diagnosis dari catalog
        $diagnosisList = $this->safe(fn() => DB::table('diagnosis_catalog')
            ->where('is_active', true)
            ->orderBy('name')
            ->pluck('name')
            ->toArray(), []);

        // Ambil daftar obat dari catalog
        $drugList = $this->safe(fn() => DB::table('drug_catalog')
            ->where('is_active', true)
            ->orderBy('name')
            ->pluck('name')
            ->toArray(), []);

        $this->syncLayoutSession($request, $clinics, $clinic, $membership);

        return view('dokter.create', [
            'cat' => $catView,
            'daftarKucing' => $kucingMap,
            'diagnosisList' => $diagnosisList,
            'drugList' => $drugList,
            'bookingId' => $cat->booking_id ?? null,
        ]);
    }

    public function pemeriksaanStore(Request $request): RedirectResponse
    {
        [$membership, $clinic] = $this->resolveDoctor($request);

        // Periksa verifikasi
        if ($membership->verification_status !== 'verified') {
            return back()->with('error', 'Anda belum terverifikasi. Lengkapi data di Profil Praktik.')->withInput();
        }

        $userId = $this->userId($request);
        $isFinal = $request->input('action') === 'final';

        $data = $request->validate([
            'cat_id' => ['required', 'uuid'],
            'booking_id' => ['nullable', 'uuid'],
            'keluhan' => ['required', 'string', 'max:500'],
            'anamnesis' => ['nullable', 'string', 'max:2000'],
            'berat' => ['nullable', 'numeric', 'min:0.01', 'max:50'],
            'suhu' => ['nullable', 'numeric', 'min:30', 'max:45'],
            'denyut_jantung' => ['nullable', 'integer', 'min:20', 'max:400'],
            'laju_napas' => ['nullable', 'integer', 'min:5', 'max:200'],
            'bcs' => ['nullable', 'integer', 'min:1', 'max:9'],
            'mukosa' => ['nullable', 'string', 'max:100'],
            'hidrasi' => ['nullable', 'string', 'max:100'],
            'temuan' => ['nullable', 'string', 'max:3000'],
            'diagnosis_utama' => ['required', 'string', 'max:300'],
            'diagnosis_banding' => ['nullable', 'string', 'max:300'],
            'keparahan' => ['nullable', 'in:ringan,sedang,berat,kritis'],
            'asesmen' => ['nullable', 'string', 'max:3000'],
            'tindakan' => ['nullable', 'string', 'max:300'],
            'kategori_tindakan' => ['nullable', 'string', 'max:100'],
            'catatan_tindakan' => ['nullable', 'string', 'max:3000'],
            'tanggal_kontrol' => ['nullable', 'date', 'after:today'],
            'vaksin_berikutnya' => ['nullable', 'date', 'after:today'],
            'anjuran' => ['nullable', 'string', 'max:3000'],
            'internal_notes' => ['nullable', 'string', 'max:3000'],
            'resep' => ['nullable', 'array', 'max:20'],
            'resep.*.obat' => ['nullable', 'string', 'max:200'],
            'resep.*.dosis' => ['nullable', 'string', 'max:100'],
            'resep.*.rute' => ['nullable', 'string', 'max:50'],
            'resep.*.frekuensi' => ['nullable', 'string', 'max:100'],
            'resep.*.hari' => ['nullable', 'integer', 'min:1', 'max:365'],
        ], [
            'cat_id.required' => 'Pilih pasien terlebih dahulu.',
            'keluhan.required' => 'Keluhan utama wajib diisi.',
            'diagnosis_utama.required' => 'Diagnosis utama wajib diisi.',
            'berat.min' => 'Berat badan harus lebih dari 0.',
            'suhu.min' => 'Suhu minimal 30°C.',
            'suhu.max' => 'Suhu maksimal 45°C.',
        ]);

        // Verifikasi kucing ada
        $catExists = $this->safe(fn() => DB::table('cats')->where('id', strtolower($data['cat_id']))->exists(), false);
        abort_if(!$catExists, 404, 'Data kucing tidak ditemukan.');

        try {
            $this->writeAsUser($request, function () use ($data, $userId, $clinic, $isFinal) {
                $recordId = Str::uuid()->toString();

                // Simpan rekam medis
                DB::table('medical_records')->insert([
                    'id' => $recordId,
                    'cat_id' => strtolower($data['cat_id']),
                    'mitra_id' => $clinic->id,
                    'booking_id' => !empty($data['booking_id']) ? strtolower($data['booking_id']) : null,
                    'author_id' => $userId,
                    'source_type' => 'dokter',
                    'visit_at' => now(),
                    'chief_complaint' => $data['keluhan'],
                    'anamnesis' => $data['anamnesis'] ?? null,
                    'weight_kg' => $data['berat'] ?? null,
                    'temperature_c' => $data['suhu'] ?? null,
                    'heart_rate_bpm' => $data['denyut_jantung'] ?? null,
                    'respiratory_rate' => $data['laju_napas'] ?? null,
                    'body_condition_score' => $data['bcs'] ?? null,
                    'mucous_membrane' => $data['mukosa'] ?? null,
                    'hydration_status' => $data['hidrasi'] ?? null,
                    'physical_exam_notes' => $data['temuan'] ?? null,
                    'diagnosis_primary' => $data['diagnosis_utama'],
                    'diagnosis_differential' => $data['diagnosis_banding'] ?? null,
                    'severity' => $data['keparahan'] ?? null,
                    'plan' => $data['asesmen'] ?? null,
                    'follow_up_advice' => $data['anjuran'] ?? null,
                    'next_visit_date' => $data['tanggal_kontrol'] ?? null,
                    'internal_notes' => $data['internal_notes'] ?? null,
                    'status' => $isFinal ? 'final' : 'draft',
                    'finalized_at' => $isFinal ? now() : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Simpan resep
                if (!empty($data['resep'])) {
                    foreach ($data['resep'] as $rx) {
                        if (empty($rx['obat'])) {
                            continue;
                        }

                        // Cari drug_id dari catalog
                        $drugId = DB::table('drug_catalog')
                            ->whereRaw('lower(name) = lower(?)', [trim($rx['obat'])])
                            ->value('id');

                        DB::table('prescriptions')->insert([
                            'id' => Str::uuid()->toString(),
                            'medical_record_id' => $recordId,
                            'drug_id' => $drugId,
                            'drug_name' => trim($rx['obat']),
                            'dose' => $rx['dosis'] ?? null,
                            'frequency' => $rx['frekuensi'] ?? null,
                            'route' => $rx['rute'] ?? null,
                            'duration_days' => $rx['hari'] ?? null,
                            'created_at' => now(),
                        ]);
                    }
                }

                // Simpan tindakan
                if (!empty($data['tindakan'])) {
                    DB::table('procedures')->insert([
                        'id' => Str::uuid()->toString(),
                        'medical_record_id' => $recordId,
                        'name' => $data['tindakan'],
                        'description' => $data['kategori_tindakan'] ?? null,
                        'notes' => $data['catatan_tindakan'] ?? null,
                        'performed_at' => now(),
                        'created_at' => now(),
                    ]);
                }

                // Update berat kucing jika diisi
                if (!empty($data['berat'])) {
                    DB::table('cats')
                        ->where('id', strtolower($data['cat_id']))
                        ->update(['weight' => $data['berat']]);

                    DB::table('weight_logs')->insert([
                        'id' => Str::uuid()->toString(),
                        'cat_id' => strtolower($data['cat_id']),
                        'weight_kg' => $data['berat'],
                        'recorded_at' => now(),
                    ]);
                }

                // Jika booking_id ada dan status final, update status booking jadi 'selesai'
                if ($isFinal && !empty($data['booking_id'])) {
                    DB::table('bookings')
                        ->where('id', strtolower($data['booking_id']))
                        ->where('assigned_doctor_id', $userId)
                        ->where('status', 'dilayani')
                        ->update([
                            'status' => 'selesai',
                            'completed_at' => now(),
                        ]);
                }

                // Log akses
                DB::table('medical_record_access_logs')->insert([
                    'id' => Str::uuid()->toString(),
                    'medical_record_id' => $recordId,
                    'cat_id' => strtolower($data['cat_id']),
                    'accessor_id' => $userId,
                    'action' => 'create',
                    'created_at' => now(),
                ]);

                return $recordId;
            });
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Gagal menyimpan rekam medis: ' . $this->friendlyDbError($e))->withInput();
        }

        return redirect()
            ->route('dokter.rekam-medis')
            ->with('success', $isFinal ? 'Rekam medis difinalisasi.' : 'Draf rekam medis disimpan.');
    }

    // ─── Rekam Medis Saya ────────────────────────────────────────────

    public function rekamMedis(Request $request): View
    {
        [$membership, $clinic, $clinics] = $this->resolveDoctor($request);

        $userId = $this->userId($request);

        $records = $this->safe(fn() => DB::table('medical_records as mr')
            ->leftJoin('cats as c', 'c.id', '=', 'mr.cat_id')
            ->leftJoin('profiles as p', 'p.id', '=', 'c.owner_id')
            ->where('mr.author_id', $userId)
            ->where('mr.mitra_id', $clinic->id)
            ->select(
                'mr.id',
                'mr.visit_at',
                'c.name as cat_name',
                'p.full_name as owner_name',
                'p.username as owner_username',
                'mr.diagnosis_primary',
                'mr.status',
            )
            ->orderByDesc('mr.visit_at')
            ->simplePaginate(self::PER_PAGE)
            ->withQueryString(), null);

        abort_if($records === null, 503, 'Data rekam medis tidak dapat dimuat.');

        $this->syncLayoutSession($request, $clinics, $clinic, $membership);

        return view('dokter.rekam-medis', [
            'records' => $records->through(fn($r) => [
                'id' => substr($r->id, 0, 8),
                'full_id' => $r->id,
                'tanggal' => $this->formatTanggal($r->visit_at),
                'cat' => $r->cat_name ?? 'Kucing',
                'owner' => $r->owner_name ?? $r->owner_username ?? '-',
                'diagnosis' => $r->diagnosis_primary ?? '-',
                'status' => $r->status,
            ]),
        ]);
    }

    // ─── Profil Praktik ──────────────────────────────────────────────

    public function profil(Request $request): View
    {
        [$membership, $clinic, $clinics] = $this->resolveDoctor($request);

        $userId = $this->userId($request);

        // Semua klinik dokter terdaftar
        $allClinics = $this->safe(fn() => DB::table('mitra_members as mm')
            ->join('mitras as m', 'm.id', '=', 'mm.mitra_id')
            ->where('mm.user_id', $userId)
            ->where('mm.member_role', 'dokter')
            ->select('m.name', 'mm.member_role', 'mm.is_active', 'mm.verification_status')
            ->orderBy('m.name')
            ->get(), collect());

        $this->syncLayoutSession($request, $clinics, $clinic, $membership);

        return view('dokter.profil', [
            'dokter' => [
                'str' => $membership->str_number ?? '',
                'sip' => $membership->sip_number ?? '',
                'spesialisasi' => $membership->specialization ?? '',
                'status' => $membership->verification_status ?? 'pending',
                'rejection_reason' => $membership->rejection_reason ?? null,
            ],
            'klinik' => $allClinics->map(fn($c) => [
                'nama' => $c->name,
                'peran' => ucfirst(str_replace('_', ' ', $c->member_role)),
                'aktif' => (bool) $c->is_active,
            ])->toArray(),
        ]);
    }

    public function profilUpdate(Request $request): RedirectResponse
    {
        [$membership, $clinic] = $this->resolveDoctor($request);

        $data = $request->validate([
            'str' => ['required', 'string', 'max:50'],
            'sip' => ['required', 'string', 'max:50'],
            'spesialisasi' => ['required', 'string', 'max:200'],
        ], [
            'str.required' => 'Nomor STR wajib diisi.',
            'sip.required' => 'Nomor SIP wajib diisi.',
            'spesialisasi.required' => 'Spesialisasi wajib diisi.',
        ]);

        try {
            $this->writeAsUser($request, function () use ($data, $membership) {
                DB::table('mitra_members')
                    ->where('id', $membership->id)
                    ->update([
                        'str_number' => $data['str'],
                        'sip_number' => $data['sip'],
                        'specialization' => $data['spesialisasi'],
                        'verification_status' => 'pending',
                        'updated_at' => now(),
                    ]);
            });
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Gagal menyimpan data praktik. Coba lagi.');
        }

        return back()->with('success', 'Data praktik disimpan dan diajukan untuk verifikasi.');
    }

    // ─── Ganti Klinik Aktif ──────────────────────────────────────────

    public function switchKlinik(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'clinic_id' => ['required', 'uuid'],
        ]);

        $userId = $this->userId($request);

        // Verifikasi user memang terdaftar di klinik ini
        $valid = $this->safe(fn() => DB::table('mitra_members as mm')
            ->join('mitras as m', 'm.id', '=', 'mm.mitra_id')
            ->where('mm.user_id', $userId)
            ->where('mm.member_role', 'dokter')
            ->where('mm.is_active', true)
            ->where('m.id', strtolower($data['clinic_id']))
            ->where('m.status', 'approved')
            ->where('m.mitra_type', 'PAW_HEALTH')
            ->exists(), false);

        if (!$valid) {
            return back()->with('error', 'Klinik tidak ditemukan atau Anda tidak terdaftar.');
        }

        $request->session()->put('dokter.active_clinic', strtolower($data['clinic_id']));

        return back()->with('success', 'Klinik aktif diganti.');
    }

    // ─── Verifikasi Dokumen Owner ────────────────────────────────────

    public function verifyAttachment(Request $request, string $attachment): RedirectResponse
    {
        [$membership, $clinic] = $this->resolveDoctor($request);

        if ($membership->verification_status !== 'verified') {
            return back()->with('error', 'Hanya dokter terverifikasi yang dapat memverifikasi dokumen.');
        }

        $userId = $this->userId($request);

        try {
            $this->writeAsUser($request, function () use ($attachment, $userId) {
                DB::table('medical_attachments')
                    ->where('id', strtolower($attachment))
                    ->where('verification_status', 'belum')
                    ->update([
                        'verification_status' => 'diverifikasi',
                        'verified_by' => $userId,
                        'verified_at' => now(),
                    ]);
            });
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Gagal memverifikasi dokumen.');
        }

        return back()->with('success', 'Dokumen berhasil diverifikasi.');
    }

    // =====================================================================
    //  HELPERS
    // =====================================================================

    private function userId(Request $request): string
    {
        return (string) $request->session()->get('supabase.user.id');
    }

    /**
     * Klinik PAW_HEALTH (approved) tempat user terdaftar sebagai dokter aktif.
     */
    private function clinicsOf(Request $request): Collection
    {
        $userId = $this->userId($request);
        abort_if(!$userId, 403, 'Silakan login terlebih dahulu.');

        try {
            return DB::table('mitra_members as mm')
                ->join('mitras as m', 'm.id', '=', 'mm.mitra_id')
                ->where('mm.user_id', $userId)
                ->where('mm.member_role', 'dokter')
                ->where('mm.is_active', true)
                ->where('m.status', 'approved')
                ->where('m.mitra_type', 'PAW_HEALTH')
                ->select(
                    'm.id',
                    'm.name',
                    'mm.id as membership_id',
                    'mm.str_number',
                    'mm.sip_number',
                    'mm.specialization',
                    'mm.verification_status',
                    'mm.rejection_reason',
                )
                ->orderBy('m.name')
                ->get();
        } catch (\Throwable $e) {
            report($e);
            abort(503, 'Data klinik tidak dapat dimuat. Coba lagi.');
        }
    }

    /**
     * Pastikan user adalah dokter di klinik yang diminta.
     *
     * @return array{0: object, 1: object, 2: Collection}
     */
    private function resolveDoctor(Request $request): array
    {
        $clinics = $this->clinicsOf($request);
        abort_if($clinics->isEmpty(), 403, 'Akun Anda belum terdaftar sebagai dokter di klinik mana pun.');

        $activeClinicId = strtolower((string) $request->session()->get('dokter.active_clinic', ''));

        // Pilih klinik aktif
        $entry = null;
        if ($activeClinicId !== '') {
            $entry = $clinics->first(fn($c) => strtolower((string) $c->id) === $activeClinicId);
        }
        if (!$entry) {
            $entry = $clinics->first();
            $request->session()->put('dokter.active_clinic', $entry->id);
        }

        // Buat objek membership dan clinic terpisah
        $membership = (object) [
            'id' => $entry->membership_id,
            'str_number' => $entry->str_number,
            'sip_number' => $entry->sip_number,
            'specialization' => $entry->specialization,
            'verification_status' => $entry->verification_status,
            'rejection_reason' => $entry->rejection_reason,
        ];

        $clinic = (object) [
            'id' => $entry->id,
            'name' => $entry->name,
        ];

        return [$membership, $clinic, $clinics];
    }

    /**
     * Simpan data ke session agar layout.blade.php bisa membacanya.
     */
    private function syncLayoutSession(Request $request, Collection $clinics, object $clinic, object $membership): void
    {
        $request->session()->put('dokter.clinics', $clinics->map(fn($c) => [
            'id' => $c->id,
            'name' => $c->name,
        ])->toArray());
        $request->session()->put('dokter.active_clinic', $clinic->id);
        $request->session()->put('dokter.verification', $membership->verification_status);
    }

    /**
     * Jalankan penulisan dalam transaksi dengan auth.uid() user login.
     */
    private function writeAsUser(Request $request, callable $write): mixed
    {
        $userId = $this->userId($request);

        return DB::transaction(function () use ($userId, $write) {
            DB::select(
                "select set_config('request.jwt.claim.sub', ?, true), set_config('request.jwt.claims', ?, true)",
                [$userId, json_encode(['sub' => $userId, 'role' => 'authenticated'])]
            );

            return $write();
        });
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

    private function friendlyDbError(\Throwable $e): string
    {
        $knownMessages = [
            'check constraint' => 'Data tidak sesuai format yang diharapkan.',
            'foreign key' => 'Referensi data tidak valid.',
            'unique constraint' => 'Data duplikat ditemukan.',
            'not-null constraint' => 'Field wajib tidak terisi.',
        ];

        foreach ($knownMessages as $pattern => $message) {
            if (str_contains(strtolower($e->getMessage()), $pattern)) {
                return $message;
            }
        }

        return 'Terjadi kesalahan. Coba lagi.';
    }

    private function hitungUmur(?string $birthDate): string
    {
        if (!$birthDate) {
            return '-';
        }

        try {
            $birth = Carbon::parse($birthDate);
            $now = Carbon::now(self::TZ);
            $years = $birth->diffInYears($now);
            $months = $birth->diffInMonths($now) % 12;

            if ($years > 0) {
                return $years . ' tahun' . ($months > 0 ? ' ' . $months . ' bulan' : '');
            }

            return $months > 0 ? $months . ' bulan' : 'Baru lahir';
        } catch (\Throwable) {
            return '-';
        }
    }

    private function formatTanggal(?string $datetime): string
    {
        if (!$datetime) {
            return '-';
        }

        try {
            return Carbon::parse($datetime)->timezone(self::TZ)->translatedFormat('d M Y, H.i');
        } catch (\Throwable) {
            return $datetime;
        }
    }

    private function formatDate(?string $date): string
    {
        if (!$date) {
            return '-';
        }

        try {
            return Carbon::parse($date)->translatedFormat('d M Y');
        } catch (\Throwable) {
            return $date;
        }
    }

    private function formatTime(?string $time): string
    {
        if (!$time) {
            return '-';
        }

        try {
            return Carbon::parse($time)->format('H.i');
        } catch (\Throwable) {
            return $time;
        }
    }

    private function mapBookingStatus(string $status): string
    {
        return match ($status) {
            'dilayani' => 'Sedang diperiksa',
            'dikonfirmasi' => 'Menunggu',
            'menunggu' => 'Menunggu',
            'selesai' => 'Selesai',
            default => ucfirst($status),
        };
    }

    /**
     * Map antrian row ke format yang kompatibel dengan view dashboard.
     */
    private function formatAntrianRow(object $a): array
    {
        return [
            'jam' => $this->formatTime($a->slot_start_time),
            'cat' => [
                'id' => $a->cat_id,
                'nama' => $a->cat_name ?? 'Kucing',
                'ras' => $a->cat_breed ?? 'Domestik',
                'owner' => $a->owner_name ?? $a->owner_username ?? '-',
                'alergi' => $a->alergi_list ? explode(', ', $a->alergi_list) : [],
                'kondisi' => [],
            ],
            'layanan' => $a->service_name ?? '-',
            'status' => $this->mapBookingStatus($a->status),
            'booking_id' => $a->id,
        ];
    }

    /**
     * Map antrian row ke format lengkap untuk halaman pasien.
     */
    private function formatAntrianRowFull(object $a): array
    {
        $row = $this->formatAntrianRow($a);
        $row['cat']['umur'] = $this->hitungUmur($a->birth_date ?? null);
        $row['cat']['jk'] = ($a->gender ?? 'jantan') === 'betina' ? 'Betina' : 'Jantan';
        $row['cat']['steril'] = ($a->is_neutered ?? false) ? 'Sudah' : 'Belum';

        return $row;
    }

    /**
     * Map cat row ke format view.
     */
    private function mapCatToViewFormat(object $k): array
    {
        return [
            'id' => $k->id,
            'nama' => $k->name,
            'ras' => $k->breed ?? 'Domestik',
            'umur' => $this->hitungUmur($k->birth_date ?? null),
            'jk' => ($k->gender ?? 'jantan') === 'betina' ? 'Betina' : 'Jantan',
            'steril' => ($k->is_neutered ?? false) ? 'Sudah' : 'Belum',
            'berat' => isset($k->weight) && $k->weight ? number_format((float) $k->weight, 1, ',', '') : '-',
            'warna' => $k->color ?? '-',
            'microchip' => $k->microchip_id ?? '-',
            'golongan' => $k->blood_type ?? '-',
            'owner' => $k->owner ?? $k->owner_username ?? '-',
            'alergi' => [],
            'kondisi' => [],
        ];
    }

    /**
     * Bangun timeline kesehatan dari rekam medis, catatan owner, dan vaksin.
     * PERBAIKAN: diurutkan memakai timestamp asli ('sort'), bukan string tanggal terformat.
     */
    private function buildTimeline(string $catId, string $clinicId): array
    {
        $timeline = [];

        // Rekam medis dari semua dokter klinik ini
        $records = $this->safe(fn() => DB::table('medical_records as mr')
            ->leftJoin('profiles as dr', 'dr.id', '=', 'mr.author_id')
            ->leftJoin('mitras as m', 'm.id', '=', 'mr.mitra_id')
            ->where('mr.cat_id', $catId)
            ->select(
                'mr.id',
                'mr.visit_at',
                'mr.chief_complaint',
                'mr.diagnosis_primary',
                'mr.weight_kg',
                'mr.temperature_c',
                'mr.follow_up_advice',
                'mr.status',
                'dr.full_name as doctor_name',
                'm.name as clinic_name',
            )
            ->orderByDesc('mr.visit_at')
            ->limit(20)
            ->get(), collect());

        foreach ($records as $r) {
            $isi = $r->chief_complaint ?? '';
            if ($r->weight_kg) {
                $isi .= ' BB ' . number_format((float) $r->weight_kg, 1) . ' kg.';
            }
            if ($r->temperature_c) {
                $isi .= ' Suhu ' . number_format((float) $r->temperature_c, 1) . ' °C.';
            }
            if ($r->follow_up_advice) {
                $isi .= ' ' . $r->follow_up_advice;
            }

            $timeline[] = [
                'tipe' => 'verified',
                'tanggal' => $this->formatDate($r->visit_at),
                'sort' => $this->sortKey($r->visit_at),
                'judul' => 'Pemeriksaan: ' . ($r->diagnosis_primary ?? 'Pemeriksaan umum'),
                'isi' => trim($isi) ?: 'Pemeriksaan rutin.',
                'sumber' => 'drh. ' . ($r->doctor_name ?? 'Dokter') . ', ' . ($r->clinic_name ?? 'Klinik'),
                'status' => $r->status,
            ];
        }

        // Catatan owner (owner_health_notes)
        $ownerNotes = $this->safe(fn() => DB::table('owner_health_notes as n')
            ->leftJoin('profiles as p', 'p.id', '=', 'n.author_id')
            ->where('n.cat_id', $catId)
            ->select('n.noted_at', 'n.note_type', 'n.content', 'p.full_name as author_name')
            ->orderByDesc('n.noted_at')
            ->limit(10)
            ->get(), collect());

        $noteTypeLabels = [
            'gejala' => 'Gejala',
            'nafsu_makan' => 'Nafsu makan',
            'obat_rumah' => 'Obat rumah',
            'perilaku' => 'Perilaku',
            'lainnya' => 'Catatan',
        ];

        foreach ($ownerNotes as $n) {
            $timeline[] = [
                'tipe' => 'owner',
                'tanggal' => $this->formatDate($n->noted_at),
                'sort' => $this->sortKey($n->noted_at),
                'judul' => $noteTypeLabels[$n->note_type] ?? 'Catatan di rumah',
                'isi' => $n->content,
                'sumber' => 'Dilaporkan owner' . ($n->author_name ? ' (' . $n->author_name . ')' : ''),
                'status' => null,
            ];
        }

        // Vaksin records
        $vaccines = $this->safe(fn() => DB::table('vaccine_records as v')
            ->leftJoin('profiles as dr', 'dr.id', '=', 'v.administered_by')
            ->leftJoin('mitras as m', 'm.id', '=', 'v.mitra_id')
            ->where('v.cat_id', $catId)
            ->select('v.date_given', 'v.vaccine_name', 'v.batch_number', 'v.notes', 'v.source_type', 'v.record_type', 'dr.full_name as doctor_name', 'm.name as clinic_name')
            ->orderByDesc('v.date_given')
            ->limit(10)
            ->get(), collect());

        $recordTypeLabels = [
            'vaksin' => 'Vaksin',
            'obat_cacing' => 'Obat cacing',
            'anti_kutu' => 'Anti kutu',
        ];

        foreach ($vaccines as $v) {
            $isi = $v->vaccine_name;
            if ($v->batch_number) {
                $isi .= ' (Batch: ' . $v->batch_number . ')';
            }
            if ($v->notes) {
                $isi .= '. ' . $v->notes;
            }

            $timeline[] = [
                'tipe' => $v->source_type === 'dokter' ? 'verified' : 'owner',
                'tanggal' => $this->formatDate($v->date_given),
                'sort' => $this->sortKey($v->date_given),
                'judul' => ($recordTypeLabels[$v->record_type] ?? 'Vaksin') . ': ' . $v->vaccine_name,
                'isi' => $isi,
                'sumber' => $v->source_type === 'dokter'
                    ? 'drh. ' . ($v->doctor_name ?? 'Dokter') . ', ' . ($v->clinic_name ?? 'Klinik')
                    : 'Dicatat owner',
                'status' => $v->source_type === 'dokter' ? 'final' : null,
            ];
        }

        // Urutkan terbaru lebih dulu berdasarkan timestamp asli
        usort($timeline, fn($a, $b) => $b['sort'] <=> $a['sort']);

        return $timeline;
    }

    private function sortKey(?string $date): int
    {
        if (!$date) {
            return 0;
        }

        try {
            return Carbon::parse($date)->getTimestamp();
        } catch (\Throwable) {
            return 0;
        }
    }
}