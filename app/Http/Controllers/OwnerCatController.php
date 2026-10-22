<?php

namespace App\Http\Controllers;

use App\Http\Requests\CatRequest;
use App\Services\SupabaseStorageService;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Profil Kucing milik user (padanan fitur cat_profile di aplikasi Flutter).
 *
 * PENTING (keamanan): koneksi database Laravel memakai role "postgres" yang MELEWATI RLS,
 * jadi isolasi data ditegakkan di sini. Setiap query tabel cats WAJIB memakai owner_id user
 * yang login, termasuk update dan delete.
 */
class OwnerCatController extends Controller
{
    public function __construct(private SupabaseStorageService $storage) {}

    public function index(Request $request): View
    {
        $userId = $this->userId($request);

        try {
            $cats = DB::table('cats')
                ->where('owner_id', $userId)
                ->orderBy('created_at')
                ->get();
        } catch (\Throwable $e) {
            report($e);
            abort(503, 'Data kucing tidak dapat dimuat. Coba lagi.');
        }

        $cats->each(fn ($cat) => $cat->age_label = $this->ageLabel($cat->birth_date));

        return view('owner.cats.index', ['cats' => $cats]);
    }

    public function create(): View
    {
        return view('owner.cats.form', [
            'cat' => null,
            'breeds' => $this->breeds(),
        ]);
    }

    public function store(CatRequest $request): RedirectResponse
    {
        $userId = $this->userId($request);
        $token = $this->token($request);
        $data = $request->catData();
        $photoUrl = null;

        // Upload dulu: bila gagal, tidak ada baris yang tersimpan.
        if ($request->hasFile('photo')) {
            try {
                $photoUrl = $this->storage->uploadCatPhoto($request->file('photo'), $userId, $token);
            } catch (\Throwable $e) {
                report($e);

                return back()->withInput()->with('error', 'Gagal mengunggah foto. Coba lagi.');
            }
        }

        try {
            $this->asUser($userId, fn () => DB::table('cats')->insert([
                ...$data,
                'photo_url' => $photoUrl,
                'owner_id' => $userId,
            ]));
        } catch (QueryException $e) {
            $this->storage->deleteByUrl($photoUrl, $userId, $token); // bersihkan file yatim

            return back()->withInput()->with('error', $this->friendlyError($e, 'Gagal menyimpan data kucing. Coba lagi.'));
        }

        return redirect()->route('owner.cats.index')
            ->with('success', 'Kucing "'.$data['name'].'" berhasil ditambahkan.');
    }

    public function edit(Request $request, string $cat): View
    {
        $row = $this->findOwnedCat($cat, $this->userId($request));

        return view('owner.cats.form', [
            'cat' => $row,
            'breeds' => $this->breeds(),
        ]);
    }

    public function update(CatRequest $request, string $cat): RedirectResponse
    {
        $userId = $this->userId($request);
        $token = $this->token($request);
        $row = $this->findOwnedCat($cat, $userId);

        $data = $request->catData();
        $newPhotoUrl = null;

        if ($request->hasFile('photo')) {
            try {
                $newPhotoUrl = $this->storage->uploadCatPhoto($request->file('photo'), $userId, $token);
            } catch (\Throwable $e) {
                report($e);

                return back()->withInput()->with('error', 'Gagal mengunggah foto. Coba lagi.');
            }
            $data['photo_url'] = $newPhotoUrl;
        }

        try {
            $affected = $this->asUser($userId, fn () => DB::table('cats')
                ->where('id', $row->id)
                ->where('owner_id', $userId)
                ->update($data));
        } catch (QueryException $e) {
            $this->storage->deleteByUrl($newPhotoUrl, $userId, $token);

            return back()->withInput()->with('error', $this->friendlyError($e, 'Gagal menyimpan perubahan. Coba lagi.'));
        }

        if (! $affected) {
            $this->storage->deleteByUrl($newPhotoUrl, $userId, $token);
            abort(404);
        }

        // Foto lama dihapus setelah data baru tersimpan.
        if ($newPhotoUrl) {
            $this->storage->deleteByUrl($row->photo_url, $userId, $token);
        }

        return redirect()->route('owner.cats.index')
            ->with('success', 'Profil "'.$data['name'].'" berhasil diperbarui.');
    }

    public function destroy(Request $request, string $cat): RedirectResponse
    {
        $userId = $this->userId($request);
        $token = $this->token($request);
        $row = $this->findOwnedCat($cat, $userId);

        try {
            $this->asUser($userId, fn () => DB::table('cats')
                ->where('id', $row->id)
                ->where('owner_id', $userId)
                ->delete());
        } catch (QueryException $e) {
            return redirect()->route('owner.cats.edit', $row->id)->with('error', $this->friendlyError(
                $e,
                'Gagal menghapus kucing. Coba lagi.',
                'Kucing ini punya riwayat booking sehingga tidak bisa dihapus.'
            ));
        }

        $this->storage->deleteByUrl($row->photo_url, $userId, $token);

        return redirect()->route('owner.cats.index')
            ->with('success', 'Kucing "'.$row->name.'" berhasil dihapus.');
    }

    // ---------------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------------

    private function userId(Request $request): string
    {
        $id = (string) $request->session()->get('supabase.user.id', '');
        abort_if(! Str::isUuid($id), 403, 'Anda tidak memiliki akses ke halaman ini.');

        return $id;
    }

    private function token(Request $request): string
    {
        return (string) $request->session()->get('supabase.access_token', '');
    }

    /** Ambil kucing HANYA jika milik user ini; selain itu 404 (tidak membocorkan keberadaan data). */
    private function findOwnedCat(string $id, string $userId): object
    {
        abort_unless(Str::isUuid($id), 404);

        try {
            $row = DB::table('cats')->where('id', $id)->where('owner_id', $userId)->first();
        } catch (\Throwable $e) {
            report($e);
            abort(503, 'Data kucing tidak dapat dimuat. Coba lagi.');
        }

        abort_if(! $row, 404);

        return $row;
    }

    /** Daftar ras dari breed_weight_standards (sama dengan form Flutter). Gagal => daftar kosong. */
    private function breeds(): array
    {
        try {
            return DB::table('breed_weight_standards')
                ->orderBy('breed')
                ->pluck('breed')
                ->map(fn ($b) => trim((string) $b))
                ->filter()
                ->unique()
                ->values()
                ->all();
        } catch (\Throwable $e) {
            report($e);

            return [];
        }
    }

    /**
     * Jalankan operasi tulis dalam transaksi dengan auth.uid() terisi.
     * Trigger audit (log_row_change) membaca auth.uid() untuk mencatat pelaku; tanpa ini
     * log aktivitas akan kosong karena Laravel tidak membawa JWT ke Postgres.
     */
    private function asUser(string $userId, callable $fn): mixed
    {
        return DB::transaction(function () use ($userId, $fn) {
            if (DB::getDriverName() === 'pgsql') {
                DB::statement("select set_config('request.jwt.claim.sub', ?, true)", [$userId]);
            }

            return $fn();
        });
    }

    /** Terjemahkan error database menjadi pesan yang bisa dibaca user. */
    private function friendlyError(QueryException $e, string $fallback, ?string $foreignKeyMessage = null): string
    {
        $msg = $e->getMessage();

        // Dari trigger guard_cat_limit
        if (str_contains($msg, 'CAT_LIMIT_REACHED')) {
            if (preg_match('/CAT_LIMIT_REACHED:\s*(.+?)(?:\s*\(Connection:|\r?\n|CONTEXT:|$)/s', $msg, $m)) {
                return trim($m[1]);
            }

            return 'Batas jumlah kucing untuk akun ini sudah tercapai.';
        }

        // FK violation (mis. bookings.cat_id tanpa CASCADE)
        if ($foreignKeyMessage && (string) $e->getCode() === '23503') {
            return $foreignKeyMessage;
        }

        report($e);

        return $fallback;
    }

    /** Padanan Cat.ageLabel di Flutter (dihitung dari birth_date, tidak disimpan). */
    private function ageLabel(?string $birthDate): string
    {
        if (! $birthDate) {
            return 'Umur tidak diketahui';
        }

        $birth = Carbon::parse($birthDate)->startOfDay();
        $today = Carbon::now('Asia/Jakarta')->startOfDay();

        if ($birth->gt($today)) {
            return 'Baru lahir';
        }

        $diff = $birth->diff($today);
        $years = (int) $diff->y;
        $months = (int) $diff->m;

        if ($years > 0) {
            return $months > 0 ? "{$years} thn {$months} bln" : "{$years} tahun";
        }

        return $months > 0 ? "{$months} bulan" : 'Baru lahir';
    }
}