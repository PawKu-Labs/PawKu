<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    private string $url;

    private string $key;

    public function __construct()
    {
        $this->url = rtrim(config('services.supabase.url'), '/');
        $this->key = config('services.supabase.anon_key');
    }

    private function client(?string $accessToken = null): PendingRequest
    {
        return Http::acceptJson()
            ->timeout(15)
            ->withHeaders([
                'apikey' => $this->key,
                'Authorization' => 'Bearer '.($accessToken ?? $this->key),
            ]);
    }

    /** POST /login — email & password */
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8'],
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
        ]);

        $res = $this->client()->post("{$this->url}/auth/v1/token?grant_type=password", [
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        if ($res->failed()) {
            $code = $res->json('error_code') ?? $res->json('code');

            $message = match ($code) {
                'invalid_credentials' => 'Email atau password yang Anda masukkan salah.',
                'email_not_confirmed' => 'Email belum dikonfirmasi. Periksa kotak masuk email Anda.',
                'over_request_rate_limit',
                'over_email_send_rate_limit' => 'Terlalu banyak percobaan. Coba lagi beberapa saat.',
                default => 'Gagal masuk. Periksa kembali email dan password Anda.',
            };

            return response()->json([
                'message' => $message,
                'credential' => $code === 'invalid_credentials',
            ], $res->status() >= 500 ? 502 : 401);
        }

        return $this->finalizeLogin($request, $res->json());
    }

    /** POST /auth/session — dipakai callback Google OAuth */
    public function session(Request $request): JsonResponse
    {
        $data = $request->validate([
            'access_token' => ['required', 'string'],
            'refresh_token' => ['nullable', 'string'],
        ]);

        // Verifikasi token ke Supabase, jangan percaya token dari client begitu saja
        $userRes = $this->client($data['access_token'])->get("{$this->url}/auth/v1/user");

        if ($userRes->failed()) {
            return response()->json(['message' => 'Sesi Google tidak valid.'], 401);
        }

        return $this->finalizeLogin($request, [
            'access_token' => $data['access_token'],
            'refresh_token' => $data['refresh_token'] ?? null,
            'expires_in' => 3600,
            'user' => $userRes->json(),
        ]);
    }

    /** POST /logout */
    public function logout(Request $request)
    {
        $token = $request->session()->get('supabase.access_token');

        if ($token) {
            try {
                $this->client($token)->post("{$this->url}/auth/v1/logout");
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda berhasil keluar dari sesi.');
    }

    private function finalizeLogin(Request $request, array $auth): JsonResponse
    {
        $accessToken = $auth['access_token'];
        $user = $auth['user'] ?? [];
        $userId = $user['id'] ?? null;
        $profile = null;

        // Cek akun dinonaktifkan Super Admin & ambil profil pengguna
        if ($userId) {
            try {
                $profileRes = $this->client($accessToken)
                    ->get("{$this->url}/rest/v1/profiles", [
                        'select' => '*',
                        'id' => 'eq.'.$userId,
                        'limit' => 1,
                    ])->json();

                if (! empty($profileRes[0])) {
                    $profile = $profileRes[0];

                    if (($profile['is_active'] ?? true) === false) {
                        $this->client($accessToken)->post("{$this->url}/auth/v1/logout");

                        return response()->json([
                            'message' => 'Akun kamu dinonaktifkan oleh Super Admin. Hubungi admin untuk info lebih lanjut.',
                        ], 403);
                    }
                }
            } catch (\Throwable $e) {
                report($e); // pengecekan dilewati bila gagal
            }
        }

        $request->session()->regenerate();
        $request->session()->put('supabase', [
            'access_token' => $accessToken,
            'refresh_token' => $auth['refresh_token'] ?? null,
            'expires_at' => now()->addSeconds($auth['expires_in'] ?? 3600)->timestamp,
            'user' => $user,
            'profile' => $profile,
        ]);

        $role = $profile['role'] ?? 'admin';
        $redirectRoute = $role === 'super_admin'
            ? route('superadmin.dashboard')
            : route('admin.dashboard');

        return response()->json([
            'message' => 'Login berhasil! Mengalihkan...',
            'redirect' => $redirectRoute,
        ]);
    }
}
