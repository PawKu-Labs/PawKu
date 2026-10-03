<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Wrapper Supabase Auth Admin API (butuh SERVICE ROLE KEY, hanya dipakai di server!).
 */
class SupabaseAdminService
{
    private function client(): PendingRequest
    {
        $key = config('services.supabase.service_role_key');

        return Http::withHeaders([
            'apikey' => $key,
            'Authorization' => 'Bearer '.$key,
        ])->baseUrl(rtrim(config('services.supabase.url'), '/').'/auth/v1/admin')
            ->acceptJson()->timeout(15);
    }

    /** Buat akun auth baru (email langsung terkonfirmasi). Return UUID user. */
    public function createUser(string $email, string $password, array $meta = []): string
    {
        $res = $this->client()->post('/users', [
            'email' => $email,
            'password' => $password,
            'email_confirm' => true,
            'user_metadata' => $meta,
        ]);

        if ($res->failed()) {
            throw new RuntimeException($res->json('msg') ?? $res->json('message') ?? 'Gagal membuat akun di Supabase.');
        }

        return $res->json('id');
    }

    /** Blokir login (ban sangat lama). */
    public function ban(string $userId): void
    {
        $this->update($userId, ['ban_duration' => '876000h']); // ±100 tahun
    }

    /** Cabut blokir. */
    public function unban(string $userId): void
    {
        $this->update($userId, ['ban_duration' => 'none']);
    }

    public function deleteUser(string $userId): void
    {
        $res = $this->client()->delete("/users/{$userId}");

        // 404 dianggap sukses (akun auth sudah tidak ada)
        if ($res->failed() && $res->status() !== 404) {
            throw new RuntimeException('Gagal menghapus akun di Supabase.');
        }
    }

    private function update(string $userId, array $payload): void
    {
        $res = $this->client()->put("/users/{$userId}", $payload);

        if ($res->failed()) {
            throw new RuntimeException('Gagal memperbarui akun di Supabase.');
        }
    }
}
