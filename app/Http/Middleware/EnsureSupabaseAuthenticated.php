<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class EnsureSupabaseAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $accessToken = $request->session()->get('supabase.access_token');

        if (! $accessToken) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            return redirect()->route('login')->with('error', 'Silakan masuk terlebih dahulu untuk mengakses halaman ini.');
        }

        $expiresAt = $request->session()->get('supabase.expires_at');

        // Jika token sudah kadaluarsa, coba segarkan menggunakan refresh_token
        if ($expiresAt && now()->timestamp >= (int) $expiresAt) {
            $refreshToken = $request->session()->get('supabase.refresh_token');

            if ($refreshToken) {
                $refreshed = $this->refreshToken($request, $refreshToken);
                if (! $refreshed) {
                    return $this->handleSessionExpired($request);
                }
            } else {
                return $this->handleSessionExpired($request);
            }
        }

        return $next($request);
    }

    /**
     * Segarkan token autentikasi Supabase.
     */
    private function refreshToken(Request $request, string $refreshToken): bool
    {
        $url = rtrim((string) config('services.supabase.url'), '/');
        $key = (string) config('services.supabase.anon_key');

        if (! $url || ! $key) {
            return false;
        }

        try {
            $response = Http::acceptJson()
                ->timeout(10)
                ->withHeaders([
                    'apikey' => $key,
                ])
                ->post("{$url}/auth/v1/token?grant_type=refresh_token", [
                    'refresh_token' => $refreshToken,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $request->session()->put('supabase.access_token', $data['access_token']);
                if (! empty($data['refresh_token'])) {
                    $request->session()->put('supabase.refresh_token', $data['refresh_token']);
                }
                $request->session()->put(
                    'supabase.expires_at',
                    now()->addSeconds($data['expires_in'] ?? 3600)->timestamp
                );

                if (! empty($data['user'])) {
                    $request->session()->put('supabase.user', $data['user']);
                }

                return true;
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return false;
    }

    /**
     * Respon saat sesi kadaluarsa.
     */
    private function handleSessionExpired(Request $request): Response
    {
        $request->session()->forget('supabase');

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Sesi Anda telah berakhir. Silakan login kembali.'], 401);
        }

        return redirect()->route('login')->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
    }
}
