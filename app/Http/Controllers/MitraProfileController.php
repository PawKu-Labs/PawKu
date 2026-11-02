<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Aksi tab Profil di dashboard mitra (web): ganti/hapus foto profil (mitras.logo_url)
 * dan ubah nama + deskripsi. Memakai bucket Supabase yang sama dengan mobile.
 *
 * Alamat & titik lokasi sengaja tidak diubah di sini (butuh peta); tetap lewat mobile.
 */
class MitraProfileController extends Controller
{
    private const LOGO_MAX_KB = 3072;

    public function updateInfo(Request $request, string $mitra): RedirectResponse
    {
        $row = $this->ownedApproved($request, $mitra);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:1000'],
        ], [
            'name.required' => 'Nama usaha wajib diisi.',
            'name.max' => 'Nama usaha maksimal 120 karakter.',
            'description.max' => 'Deskripsi maksimal 1000 karakter.',
        ]);

        try {
            DB::table('mitras')
                ->where('id', $row->id)
                ->where('owner_id', $row->owner_id)
                ->update($this->withTimestamp([
                    'name' => trim($data['name']),
                    'description' => trim((string) ($data['description'] ?? '')) ?: null,
                ]));
        } catch (\Throwable $e) {
            report($e);

            return $this->back($mitra)->with('error', 'Gagal menyimpan profil. Coba lagi.');
        }

        return $this->back($mitra)->with('success', 'Profil usaha berhasil disimpan.');
    }

    public function updateLogo(Request $request, string $mitra): RedirectResponse
    {
        $row = $this->ownedApproved($request, $mitra);
        $userId = (string) $row->owner_id;

        $request->validate([
            'logo' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:'.self::LOGO_MAX_KB],
        ], [
            'logo.required' => 'Pilih foto dulu.',
            'logo.file' => 'Foto gagal diunggah. Coba pilih ulang.',
            'logo.mimes' => 'Foto harus berformat JPG, PNG, atau WebP.',
            'logo.max' => 'Ukuran foto maksimal 3 MB.',
            'logo.uploaded' => 'Foto gagal diunggah. Ukurannya mungkin terlalu besar.',
        ]);

        // 1) Unggah file baru
        try {
            [$newUrl, $newPath] = $this->uploadPhoto($request->file('logo'), $userId);
        } catch (\Throwable $e) {
            report($e);

            return $this->back($mitra)->with('error', 'Gagal mengunggah foto. Coba lagi.');
        }

        // 2) Simpan URL baru. Gagal -> buang file yang baru diunggah.
        $oldUrl = $row->logo_url;
        try {
            $affected = DB::table('mitras')
                ->where('id', $row->id)
                ->where('owner_id', $row->owner_id)
                ->update($this->withTimestamp(['logo_url' => $newUrl]));
            if (! $affected) {
                throw new \RuntimeException('Tidak ada baris yang berubah.');
            }
        } catch (\Throwable $e) {
            report($e);
            $this->deleteObject($newPath);

            return $this->back($mitra)->with('error', 'Foto terunggah tetapi gagal disimpan. Coba lagi.');
        }

        // 3) Bersihkan file lama (kegagalan tidak mengganggu)
        $this->removeStored($oldUrl, $userId, (string) $row->id);

        return $this->back($mitra)->with('success', 'Foto profil berhasil diperbarui.');
    }

    public function removeLogo(Request $request, string $mitra): RedirectResponse
    {
        $row = $this->ownedApproved($request, $mitra);

        if (empty($row->logo_url)) {
            return $this->back($mitra)->with('error', 'Belum ada foto profil.');
        }

        try {
            DB::table('mitras')
                ->where('id', $row->id)
                ->where('owner_id', $row->owner_id)
                ->update($this->withTimestamp(['logo_url' => null]));
        } catch (\Throwable $e) {
            report($e);

            return $this->back($mitra)->with('error', 'Gagal menghapus foto. Coba lagi.');
        }

        $this->removeStored($row->logo_url, (string) $row->owner_id, (string) $row->id);

        return $this->back($mitra)->with('success', 'Foto profil dihapus.');
    }

    // ---------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------

    private function back(string $mitra): RedirectResponse
    {
        return redirect()->route('owner.mitra.dashboard.unit', ['mitra' => strtolower($mitra), 'tab' => 'profil']);
    }

    /** Mitra milik user yang login (404 bila bukan miliknya). Status tidak diperiksa. */
    private function ownedMitra(Request $request, string $mitra): object
    {
        $userId = $request->session()->get('supabase.user.id');
        abort_if(! $userId, 403);

        try {
            $row = DB::table('mitras')
                ->where('id', strtolower($mitra))
                ->where('owner_id', $userId)
                ->first();
        } catch (\Throwable $e) {
            report($e);
            abort(503, 'Data mitra tidak dapat dimuat. Coba lagi.');
        }

        abort_if(! $row, 404);

        return $row;
    }

    private function ownedApproved(Request $request, string $mitra): object
    {
        $row = $this->ownedMitra($request, $mitra);
        abort_if($row->status !== 'approved', 403);

        return $row;
    }

    private function withTimestamp(array $payload): array
    {
        if (Schema::hasColumn('mitras', 'updated_at')) {
            $payload['updated_at'] = now();
        }

        return $payload;
    }

    /** @return array{0:string,1:string} [urlPublik, pathDiBucket] */
    private function uploadPhoto(UploadedFile $file, string $userId): array
    {
        [$base, $key, $bucket] = $this->storageConfig();

        if ($base === '' || $key === '') {
            throw new \RuntimeException('Konfigurasi Supabase Storage belum diisi.');
        }

        $mime = $file->getMimeType() ?: 'image/jpeg';
        $ext = match ($mime) {
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => 'jpg',
        };
        $path = "mitra/{$userId}/logo_".now()->timestamp.'_'.Str::random(8).'.'.$ext;

        $res = Http::timeout(30)
            ->withToken($key)
            ->withHeaders(['apikey' => $key, 'x-upsert' => 'false'])
            ->withBody((string) file_get_contents($file->getRealPath()), $mime)
            ->post("{$base}/storage/v1/object/{$bucket}/{$path}");

        if (! $res->successful()) {
            throw new \RuntimeException('Upload gagal: '.$res->status().' '.$res->body());
        }

        return ["{$base}/storage/v1/object/public/{$bucket}/{$path}", $path];
    }

    /** Hapus 1 objek di bucket berdasarkan path. Kegagalan hanya dilaporkan. */
    private function deleteObject(string $path): void
    {
        [$base, $key, $bucket] = $this->storageConfig();
        if ($base === '' || $key === '') {
            return;
        }

        $encoded = implode('/', array_map('rawurlencode', explode('/', $path)));

        try {
            Http::timeout(15)
                ->withToken($key)
                ->withHeaders(['apikey' => $key])
                ->delete("{$base}/storage/v1/object/{$bucket}/{$encoded}");
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /**
     * Hapus file lama dari bucket bila: URL-nya memang dari bucket ini, berada di folder
     * milik user ini (web: mitra/{uid}/..., mobile: {uid}/...), dan tidak dipakai di tempat lain.
     */
    private function removeStored(?string $url, string $userId, string $mitraId): void
    {
        if (! $url) {
            return;
        }

        [$base, , $bucket] = $this->storageConfig();
        $prefix = "{$base}/storage/v1/object/public/{$bucket}/";
        if ($base === '' || ! str_starts_with($url, $prefix)) {
            return;
        }

        $path = urldecode((string) strtok(substr($url, strlen($prefix)), '?'));
        if ($path === '' || str_contains($path, '..')) {
            return;
        }
        if (! str_starts_with($path, "mitra/{$userId}/") && ! str_starts_with($path, "{$userId}/")) {
            return;
        }
        if ($this->urlStillUsed($url, $mitraId)) {
            return;
        }

        $this->deleteObject($path);
    }

    /** Dipakai mitra lain, galeri, atau foto layanan? Bila ragu (query gagal) anggap masih dipakai. */
    private function urlStillUsed(string $url, string $mitraId): bool
    {
        try {
            if (DB::table('mitras')->where('id', '!=', $mitraId)->where('logo_url', $url)->exists()) {
                return true;
            }
            if (DB::table('mitras')->whereRaw('? = ANY(gallery_photo_urls)', [$url])->exists()) {
                return true;
            }
            if (DB::table('mitra_services')->whereRaw('? = ANY(photo_urls)', [$url])->exists()) {
                return true;
            }

            return false;
        } catch (\Throwable $e) {
            report($e);

            return true;
        }
    }

    /** @return array{0:string,1:string,2:string} [baseUrl, serviceRoleKey, bucket] */
    private function storageConfig(): array
    {
        return [
            rtrim((string) config('services.supabase.url'), '/'),
            (string) config('services.supabase.service_role_key'),
            (string) (config('services.supabase.bucket') ?: 'petshop-photos'),
        ];
    }
}