<?php

namespace App\Http\Controllers;

use App\Services\SupabaseAdminService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SuperAdminController extends Controller
{
    public function __construct(private SupabaseAdminService $supabase) {}

    /* ---------------------------------------------------------------
     | 1. Dashboard Monitoring
     * ------------------------------------------------------------- */
    public function dashboard(): View
    {
        $safe = function (callable $fn) {
            try {
                return $fn();
            } catch (\Throwable $e) {
                report($e);

                return 0;
            }
        };

        return view('superadmin.dashboard', [
            'users' => [
                'owners' => $safe(fn () => DB::table('profiles')->where('role', 'pemilik_kucing')->count()),
                'mitra' => $safe(fn () => DB::table('mitras')->count()),
                'admins' => $safe(fn () => DB::table('profiles')->where('role', 'admin')->count()),
            ],
            'petshops' => [
                'approved' => $safe(fn () => DB::table('mitras')->where('status', 'approved')->count()),
                'pending' => $safe(fn () => DB::table('mitras')->where('status', 'pending')->count()),
                'rejected' => $safe(fn () => DB::table('mitras')->where('status', 'rejected')->count()),
            ],
            'activity' => [
                'bookings' => $safe(fn () => DB::table('bookings')->count()),
                'cats' => $safe(fn () => DB::table('cats')->count()),
            ],
        ]);
    }

    /* ---------------------------------------------------------------
     | 2. Persetujuan Petshop (tabel mitras)
     * ------------------------------------------------------------- */
    public function petshops(Request $request): View
    {
        $status = $request->query('status', 'pending');
        abort_unless(in_array($status, ['pending', 'approved', 'rejected'], true), 404);

        $counts = DB::table('mitras')
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')->pluck('total', 'status');

        $petshops = DB::table('mitras')
            ->leftJoin('profiles', 'profiles.id', '=', 'mitras.owner_id')
            ->leftJoin('auth.users as u', 'u.id', '=', 'mitras.owner_id')
            ->select('mitras.*', 'u.email as owner_email')
            ->selectRaw('coalesce(profiles.full_name, profiles.username) as owner_name')
            ->where('mitras.status', $status)
            ->orderByDesc('mitras.created_at')
            ->paginate(10)->withQueryString();

        return view('superadmin.petshops', compact('petshops', 'status', 'counts'));
    }

    public function updatePetshopStatus(Request $request, string $id): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['approved', 'rejected', 'pending'])],
        ]);

        $approved = $data['status'] === 'approved';

        $updated = DB::table('mitras')->where('id', $id)->update([
            'status' => $data['status'],
            'approved_by' => $approved ? session('supabase.user.id') : null,
            'approved_at' => $approved ? now() : null,
        ]);

        abort_if(! $updated, 404);

        $label = ['approved' => 'disetujui', 'rejected' => 'ditolak', 'pending' => 'dikembalikan ke menunggu'][$data['status']];

        return back()->with('success', "Petshop berhasil {$label}.");
    }

    /* ---------------------------------------------------------------
     | 3. Kelola Admin
     * ------------------------------------------------------------- */
    public function admins(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $admins = DB::table('profiles')
            ->leftJoin('auth.users as u', 'u.id', '=', 'profiles.id')
            ->select('profiles.*', 'u.email')
            ->where('profiles.role', 'admin')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('profiles.full_name', 'ilike', "%{$q}%")
                        ->orWhere('profiles.username', 'ilike', "%{$q}%")
                        ->orWhere('u.email', 'ilike', "%{$q}%");
                });
            })
            ->orderByDesc('profiles.created_at')
            ->paginate(10)->withQueryString();

        return view('superadmin.admins', compact('admins', 'q'));
    }

    public function storeAdmin(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'username' => ['required', 'alpha_dash', 'min:3', 'max:30', 'unique:profiles,username'],
            'email' => [
                'required',
                'email',
                'max:255',
                function ($attr, $value, $fail) {
                    try {
                        if (DB::table('auth.users')->whereRaw('lower(email) = ?', [strtolower($value)])->exists()) {
                            $fail('Email sudah terdaftar.');
                        }
                    } catch (\Throwable $e) {
                        // jika auth.users tidak terbaca, Supabase tetap menolak email duplikat
                    }
                },
            ],
            'password' => ['required', 'string', 'min:8', 'max:72'],
        ], [], ['password' => 'password sementara']);

        try {
            $uid = $this->supabase->createUser($data['email'], $data['password'], [
                'full_name' => $data['username'],
                'username' => $data['username'],
            ]);

            // updateOrInsert: aman jika ada trigger Supabase yang sudah membuat baris profiles
            DB::table('profiles')->updateOrInsert(['id' => $uid], [
                'username' => $data['username'],
                'full_name' => $data['username'],
                'role' => 'admin',
                'is_active' => true,
                'is_blocked' => false,
                'created_by' => session('supabase.user.id'),
                'onboarding_completed' => true,
            ]);
        } catch (\Throwable $e) {
            report($e);

            return back()->withInput($request->except('password'))
                ->with('error', $e->getMessage())->with('open_modal', true);
        }

        return back()->with('success', "Admin {$data['username']} berhasil ditambahkan. Berikan password sementara kepada yang bersangkutan.");
    }

    public function adminAction(Request $request, string $id): RedirectResponse
    {
        $data = $request->validate([
            'action' => ['required', Rule::in(['activate', 'deactivate', 'block', 'unblock', 'demote', 'delete'])],
        ]);

        $admin = DB::table('profiles')->where('id', $id)->where('role', 'admin')->first();
        abort_if(! $admin, 404, 'Akun admin tidak ditemukan.');
        abort_if($id === session('supabase.user.id'), 403);

        try {
            switch ($data['action']) {
                case 'deactivate':
                    $this->supabase->ban($id); // login ditolak di level auth Supabase
                    DB::table('profiles')->where('id', $id)->update(['is_active' => false]);
                    $msg = 'Admin dinonaktifkan.';
                    break;

                case 'block':
                    $this->supabase->ban($id);
                    DB::table('profiles')->where('id', $id)->update(['is_blocked' => true]);
                    $msg = 'Admin diblokir.';
                    break;

                case 'activate':
                    $this->supabase->unban($id);
                    DB::table('profiles')->where('id', $id)->update(['is_active' => true]);
                    $msg = 'Admin diaktifkan kembali.';
                    break;

                case 'unblock':
                    $this->supabase->unban($id);
                    DB::table('profiles')->where('id', $id)->update(['is_blocked' => false, 'is_active' => true]);
                    $msg = 'Blokir admin dibuka.';
                    break;

                case 'demote':
                    $this->supabase->unban($id);
                    DB::table('profiles')->where('id', $id)->update([
                        'role' => 'pemilik_kucing',
                        'is_active' => true,
                        'is_blocked' => false,
                    ]);
                    $msg = 'Admin diturunkan menjadi pemilik kucing.';
                    break;

                case 'delete':
                    DB::transaction(function () use ($id) {
                        // lepas referensi agar FK tidak menghalangi penghapusan
                        DB::table('mitras')->where('approved_by', $id)->update(['approved_by' => null]);
                        DB::table('profiles')->where('created_by', $id)->update(['created_by' => null]);
                        DB::table('profiles')->where('id', $id)->delete();
                        // profiles.id -> auth.users.id: profil harus terhapus dulu.
                        // Jika Supabase gagal, exception akan me-rollback transaksi ini.
                        $this->supabase->deleteUser($id);
                    });
                    $msg = 'Akun admin dihapus permanen.';
                    break;
            }
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', $msg);
    }
}
