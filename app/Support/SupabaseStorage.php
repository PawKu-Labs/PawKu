<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SupabaseStorage
{
    /**
     * Unggah file ke bucket. Mengembalikan ['path' => ..., 'url' => ...].
     */
    public static function upload(UploadedFile $file, string $folder = 'articles'): array
    {
        $path = trim($folder, '/') . '/' . Str::uuid() . '.' . $file->extension();

        $bucket = self::bucket();
        $res = Http::withHeaders(self::headers())
            ->withBody(file_get_contents($file->getRealPath()), $file->getMimeType())
            ->post(self::endpoint("object/{$bucket}/{$path}"));

        if (!$res->successful()) {
            report(new \RuntimeException('Supabase upload gagal: ' . $res->body()));
            throw new \RuntimeException('Gagal mengunggah gambar.');
        }

        return ['path' => $path, 'url' => self::publicUrl($path)];
    }

    public static function delete(?string $path): void
    {
        if (!$path) {
            return;
        }

        $res = Http::withHeaders(self::headers())
            ->delete(self::endpoint('object/' . self::bucket() . '/' . $path));

        if (!$res->successful()) {
            report(new \RuntimeException('Supabase delete gagal: ' . $res->body()));
        }
    }

    public static function publicUrl(string $path): string
    {
        return rtrim(config('services.supabase.url'), '/') . '/storage/v1/object/public/' . self::bucket() . '/' . $path;
    }

    private static function bucket(): string
    {
        return config('services.supabase.bucket');
    }

    private static function endpoint(string $suffix): string
    {
        return rtrim(config('services.supabase.url'), '/') . '/storage/v1/' . $suffix;
    }

    private static function headers(): array
    {
        $key = config('services.supabase.service_role_key') ?? config('services.supabase.service_key') ?? config('services.supabase.anon_key');

        return ['Authorization' => 'Bearer ' . $key, 'apikey' => $key, 'x-upsert' => 'true'];
    }
}