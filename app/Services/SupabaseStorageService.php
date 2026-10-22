<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

/**
 * Upload & hapus foto kucing di Supabase Storage (bucket "cat-photos").
 *
 * Memakai TOKEN USER dari session (bukan service role key), sehingga policy storage
 * "folder pertama = auth.uid()" tetap berlaku, sama seperti saat aplikasi Flutter mengunggah.
 * Format path & URL identik dengan Flutter: {user_id}/{timestamp_ms}.{ext}
 */
class SupabaseStorageService
{
    /** Sesuai allowed_mime_types bucket. */
    private const ALLOWED = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    private string $url;

    private string $key;

    private string $bucket;

    public function __construct()
    {
        $this->url = rtrim((string) config('services.supabase.url'), '/');
        $this->key = (string) config('services.supabase.anon_key');
        $this->bucket = (string) config('services.supabase.cats_bucket', 'cat-photos');
    }

    /**
     * Unggah foto, kembalikan public URL penuh (nilai untuk kolom cats.photo_url).
     *
     * @throws InvalidArgumentException|RuntimeException
     */
    public function uploadCatPhoto(UploadedFile $file, string $userId, string $accessToken): string
    {
        if (! Str::isUuid($userId) || $accessToken === '') {
            throw new InvalidArgumentException('Sesi tidak valid untuk mengunggah foto.');
        }

        $mime = (string) $file->getMimeType();
        $ext = self::ALLOWED[$mime] ?? null;
        if (! $ext) {
            throw new InvalidArgumentException('Tipe file tidak didukung.');
        }

        $stamp = (int) (microtime(true) * 1000);
        $path = "{$userId}/{$stamp}.{$ext}";

        $res = Http::timeout(30)
            ->withHeaders([
                'apikey' => $this->key,
                'Authorization' => 'Bearer '.$accessToken,
                'x-upsert' => 'true',
            ])
            ->withBody((string) file_get_contents($file->getRealPath()), $mime)
            ->post("{$this->url}/storage/v1/object/{$this->bucket}/{$path}");

        if ($res->failed()) {
            throw new RuntimeException('Upload foto gagal (HTTP '.$res->status().').');
        }

        return "{$this->url}/storage/v1/object/public/{$this->bucket}/{$path}";
    }

    /**
     * Hapus foto berdasarkan public URL. Aman dipanggil dengan URL apa pun:
     * hanya file di bucket ini dan di folder milik user yang dihapus.
     * Kegagalan tidak dilempar (hanya dilaporkan) agar proses utama tidak ikut gagal.
     */
    public function deleteByUrl(?string $photoUrl, string $userId, string $accessToken): void
    {
        $path = $this->pathFromUrl($photoUrl, $userId);
        if (! $path || $accessToken === '') {
            return;
        }

        try {
            $res = Http::timeout(15)
                ->withHeaders([
                    'apikey' => $this->key,
                    'Authorization' => 'Bearer '.$accessToken,
                ])
                ->delete("{$this->url}/storage/v1/object/{$this->bucket}/{$path}");

            if ($res->failed()) {
                report(new RuntimeException('Hapus foto gagal (HTTP '.$res->status().") untuk {$path}."));
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private function pathFromUrl(?string $photoUrl, string $userId): ?string
    {
        if (! $photoUrl || ! Str::isUuid($userId)) {
            return null;
        }

        $prefix = "{$this->url}/storage/v1/object/public/{$this->bucket}/";
        if (! str_starts_with($photoUrl, $prefix)) {
            return null;
        }

        $path = rawurldecode(strtok(substr($photoUrl, strlen($prefix)), '?'));

        // Hanya file di folder milik user ini, tanpa path traversal.
        if (! str_starts_with($path, $userId.'/') || str_contains($path, '..')) {
            return null;
        }

        return $path;
    }
}