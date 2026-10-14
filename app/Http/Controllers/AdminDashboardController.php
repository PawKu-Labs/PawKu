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