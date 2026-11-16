<?php

namespace App\Http\Controllers;

use App\Support\ActivityLogger;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    private const CATEGORIES = ['all', 'auth', 'user', 'data', 'transaction', 'admin'];

    private const DOCTOR_STATUSES = ['pending', 'verified', 'rejected'];

    /**
     * Dashboard monitoring untuk admin.
     */
    public function index(): View
    {
        $safe = function (callable $fn, $fallback = 0) {
            try {
                return $fn();
            } catch (\Throwable $e) {
                report($e);

                return $fallback;
            }
        };

        return view('admin.dashboard', [
            'users' => [
                'owners' => $safe(fn() => DB::table('profiles')->where('role', 'pemilik_kucing')->count()),
                'mitra' => $safe(fn() => DB::table('mitras')->count()),
                'admins' => $safe(fn() => DB::table('profiles')->where('role', 'admin')->count()),
            ],
            'petshops' => [
                'approved' => $safe(fn() => DB::table('mitras')->where('status', 'approved')->count()),
                'pending' => $safe(fn() => DB::table('mitras')->where('status', 'pending')->count()),
                'rejected' => $safe(fn() => DB::table('mitras')->where('status', 'rejected')->count()),
            ],
            'activity' => [
                'bookings' => $safe(fn() => DB::table('bookings')->count()),
                'cats' => $safe(fn() => DB::table('cats')->count()),
            ],
            'recent' => $safe(
                fn() => DB::table('activity_logs')->orderByDesc('created_at')->orderByDesc('id')->limit(6)->get(),
                collect()
            ),
        ]);
    }

    /**
     * Pemantauan aktivitas sistem: aktivitas pengguna, perubahan data, dan transaksi.
     */
    public function activity(Request $request): View
    {
        $category = $request->query('category', 'all');
        abort_unless(in_array($category, self::CATEGORIES, true), 404);

        $ready = Schema::hasTable('activity_logs');
        $search = trim((string) $request->query('q', ''));
        $from = $this->parseDate($request->query('from'));
        $to = $this->parseDate($request->query('to'));

        $empty = [
            'ready' => $ready,
            'category' => $category,
            'search' => $search,
            'from' => $from?->toDateString(),
            'to' => $to?->toDateString(),
            'logs' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15),
            'totals' => collect(),
            'today' => collect(),
        ];

        if (!$ready) {
            return view('admin.activity', $empty);
        }

        $tz = 'Asia/Jakarta';

        $query = DB::table('activity_logs');

        if ($category !== 'all') {
            $query->where('category', $category);
        }

        if ($search !== '') {
            $like = '%' . str_replace(['%', '_'], ['\%', '\_'], $search) . '%';
            $query->where(function ($q) use ($like) {
                $q->where('description', 'like', $like)
                    ->orWhere('actor_name', 'like', $like)
                    ->orWhere('subject_type', 'like', $like)
                    ->orWhere('subject_id', 'like', $like);
            });
        }

        if ($from) {
            $query->where('created_at', '>=', Carbon::parse($from->toDateString(), $tz)->startOfDay()->utc());
        }
        if ($to) {
            $query->where('created_at', '<=', Carbon::parse($to->toDateString(), $tz)->endOfDay()->utc());
        }

        $logs = $query
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $logs->getCollection()->transform(function ($row) {
            $row->changes = $this->diff($row->old_values, $row->new_values);

            return $row;
        });

        $startOfToday = Carbon::now($tz)->startOfDay()->utc();

        return view('admin.activity', array_merge($empty, [
            'logs' => $logs,
            'totals' => DB::table('activity_logs')
                ->selectRaw('category, count(*) as total')->groupBy('category')->pluck('total', 'category'),
            'today' => DB::table('activity_logs')->where('created_at', '>=', $startOfToday)
                ->selectRaw('category, count(*) as total')->groupBy('category')->pluck('total', 'category'),
        ]));
    }

    /**
     * Tampilkan daftar persetujuan petshop mitra (status: pending, approved, rejected).
     */
    public function petshops(Request $request): View
    {
        $status = $request->query('status', 'pending');
        abort_unless(in_array($status, ['pending', 'approved', 'rejected'], true), 404);

        $counts = DB::table('mitras')
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $query = DB::table('mitras')
            ->leftJoin('profiles', 'profiles.id', '=', 'mitras.owner_id');

        if (DB::getDriverName() === 'pgsql') {
            $query->leftJoin('auth.users as u', 'u.id', '=', 'mitras.owner_id')
                ->select('mitras.*', 'u.email as owner_email');
        } else {
            $query->select('mitras.*')->selectRaw('null as owner_email');
        }

        $petshops = $query
            ->selectRaw('coalesce(profiles.full_name, profiles.username) as owner_name')
            ->where('mitras.status', $status)
            ->orderByDesc('mitras.created_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.petshops', compact('petshops', 'status', 'counts'));
    }

    /**
     * Update status verifikasi petshop mitra (Setujui, Tolak, atau Kembalikan ke Menunggu).
     */
    public function updatePetshopStatus(Request $request, string $id): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['approved', 'rejected', 'pending'])],
            'rejection_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $mitra = DB::table('mitras')->where('id', $id)->first();
        abort_if(!$mitra, 404);

        $approved = $data['status'] === 'approved';
        $rejected = $data['status'] === 'rejected';

        $updatePayload = [
            'status' => $data['status'],
            'approved_by' => $approved ? session('supabase.user.id') : null,
            'approved_at' => $approved ? now() : null,
        ];

        if ($rejected && !empty($data['rejection_reason'])) {
            $updatePayload['rejection_reason'] = $data['rejection_reason'];
        } elseif ($approved) {
            $updatePayload['rejection_reason'] = null;
        }

        // Aksi ini dicatat manual (lengkap dengan nama admin), jadi trigger database dilewati
        // agar tidak muncul dua entri untuk satu kejadian.
        DB::transaction(function () use ($id, $updatePayload) {
            if (DB::getDriverName() === 'pgsql') {
                DB::statement("select set_config('app.skip_audit', '1', true)");
            }
            DB::table('mitras')->where('id', $id)->update($updatePayload);
        });

        $label = match ($data['status']) {
            'approved' => 'disetujui',
            'rejected' => 'ditolak',
            'pending' => 'dikembalikan ke status menunggu',
            default => 'diperbarui',
        };

        ActivityLogger::log(
            'admin',
            $data['status'],
            'Petshop "' . ($mitra->name ?? $id) . '" ' . $label,
            'mitras',
            $id,
            ['status' => $mitra->status, 'rejection_reason' => $mitra->rejection_reason ?? null],
            ['status' => $data['status'], 'rejection_reason' => $updatePayload['rejection_reason'] ?? ($mitra->rejection_reason ?? null)],
        );

        return back()->with('success', "Petshop berhasil {$label}.");
    }

    /**
     * Daftar dokter hewan klinik untuk diverifikasi admin PawKu (status: pending, verified, rejected).
     * Dokter baru berstatus pending sampai STR/SIP-nya diperiksa di sini.
     */
    public function doctors(Request $request): View
    {
        $status = $request->query('status', 'pending');
        abort_unless(in_array($status, self::DOCTOR_STATUSES, true), 404);

        $counts = DB::table('mitra_members')
            ->where('member_role', 'dokter')
            ->selectRaw('verification_status, count(*) as total')
            ->groupBy('verification_status')
            ->pluck('total', 'verification_status');

        $query = DB::table('mitra_members as mm')
            ->join('profiles as p', 'p.id', '=', 'mm.user_id')
            ->join('mitras as m', 'm.id', '=', 'mm.mitra_id')
            ->leftJoin('profiles as vb', 'vb.id', '=', 'mm.verified_by');

        if (DB::getDriverName() === 'pgsql') {
            $query->leftJoin('auth.users as u', 'u.id', '=', 'mm.user_id')
                ->select('u.email as email');
        } else {
            $query->selectRaw('null as email');
        }

        $doctors = $query
            ->addSelect(
                'mm.id',
                'mm.str_number',
                'mm.sip_number',
                'mm.specialization',
                'mm.verification_status',
                'mm.rejection_reason',
                'mm.verified_at',
                'mm.is_active',
                'mm.created_at',
                'p.full_name',
                'p.username',
                'p.phone_number',
                'p.avatar_url',
                'm.name as mitra_name',
                'm.status as mitra_status'
            )
            ->selectRaw('coalesce(vb.full_name, vb.username) as verified_by_name')
            ->where('mm.member_role', 'dokter')
            ->where('mm.verification_status', $status)
            ->orderByDesc('mm.created_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.dokter', compact('doctors', 'status', 'counts'));
    }

    /**
     * Verifikasi, tolak, atau kembalikan ke menunggu untuk satu dokter hewan.
     * Database mewajibkan STR dan SIP terisi sebelum dokter boleh berstatus verified.
     */
    public function updateDoctorStatus(Request $request, string $member): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(self::DOCTOR_STATUSES)],
            'rejection_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $row = DB::table('mitra_members as mm')
            ->join('profiles as p', 'p.id', '=', 'mm.user_id')
            ->join('mitras as m', 'm.id', '=', 'mm.mitra_id')
            ->where('mm.id', strtolower($member))
            ->where('mm.member_role', 'dokter')
            ->select('mm.id', 'mm.str_number', 'mm.sip_number', 'mm.verification_status', 'mm.rejection_reason',
                'p.full_name', 'p.username', 'm.name as mitra_name')
            ->first();
        abort_if(!$row, 404);

        $verified = $data['status'] === 'verified';
        $rejected = $data['status'] === 'rejected';

        if ($verified && (trim((string) $row->str_number) === '' || trim((string) $row->sip_number) === '')) {
            return back()->with('error', 'Dokter belum melengkapi nomor STR dan SIP, jadi belum bisa diverifikasi.');
        }

        $reason = $rejected ? (trim((string) ($data['rejection_reason'] ?? '')) ?: null) : null;

        $payload = [
            'verification_status' => $data['status'],
            'verified_by' => $verified ? session('supabase.user.id') : null,
            'verified_at' => $verified ? now() : null,
            'rejection_reason' => $reason,
        ];

        try {
            // Aksi ini dicatat manual (lengkap dengan nama admin), jadi trigger audit database dilewati
            // agar tidak muncul dua entri untuk satu kejadian.
            DB::transaction(function () use ($row, $payload) {
                if (DB::getDriverName() === 'pgsql') {
                    DB::statement("select set_config('app.skip_audit', '1', true)");
                }
                DB::table('mitra_members')->where('id', $row->id)->update($payload);
            });
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Gagal memperbarui verifikasi dokter. Coba lagi.');
        }

        $label = match ($data['status']) {
            'verified' => 'diverifikasi',
            'rejected' => 'ditolak verifikasinya',
            default => 'dikembalikan ke status menunggu verifikasi',
        };
        $doctorName = $row->full_name ?: ($row->username ?: 'Dokter');

        ActivityLogger::log(
            'admin',
            $data['status'],
            'Dokter "' . $doctorName . '" (' . ($row->mitra_name ?? 'klinik') . ') ' . $label,
            'mitra_members',
            $row->id,
            ['verification_status' => $row->verification_status, 'rejection_reason' => $row->rejection_reason],
            ['verification_status' => $data['status'], 'rejection_reason' => $reason],
        );

        return back()->with('success', 'Dokter ' . $doctorName . ' berhasil ' . $label . '.');
    }

    private function parseDate(mixed $value): ?Carbon
    {
        if (!is_string($value) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return null;
        }

        try {
            return Carbon::createFromFormat('Y-m-d', $value);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Bandingkan nilai lama dan baru untuk ditampilkan di modal rincian.
     *
     * @return array<int, array{field:string, old:?string, new:?string}>
     */
    private function diff(?string $old, ?string $new): array
    {
        $o = json_decode($old ?? '', true) ?: [];
        $n = json_decode($new ?? '', true) ?: [];
        $ignore = ['updated_at', 'created_at'];
        $out = [];

        foreach (array_unique(array_merge(array_keys($o), array_keys($n))) as $key) {
            if (in_array($key, $ignore, true)) {
                continue;
            }
            $a = $o[$key] ?? null;
            $b = $n[$key] ?? null;
            if ($o && $n && $a === $b) {
                continue;
            }
            $out[] = ['field' => $key, 'old' => $this->stringify($a), 'new' => $this->stringify($b)];
        }

        return array_slice($out, 0, 40);
    }

    private function stringify(mixed $v): ?string
    {
        if ($v === null) {
            return null;
        }
        $s = is_scalar($v) ? (string) $v : json_encode($v, JSON_UNESCAPED_UNICODE);

        return mb_strlen($s) > 200 ? mb_substr($s, 0, 200) . '…' : $s;
    }
}