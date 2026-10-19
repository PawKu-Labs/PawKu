<?php

namespace App\Http\Controllers;

use App\Support\MitraType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OwnerMitraController extends Controller
{
    /**
     * ASUMSI: kode jenis usaha sesuai dengan MitraType di aplikasi Dart (`_type.code`).
     * Ubah daftar ini bila kode di database Anda berbeda.
     */


    private const MAX_PHOTOS = 10;

    /** true bila kolom mitras.gallery_photo_urls bertipe jsonb; false bila text[] (Postgres array). */
    private const GALLERY_AS_JSON = false;

    private const DAYS = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

    public function index(Request $request): View
    {
        $userId = $request->session()->get('supabase.user.id');
        $mitras = collect();
        $loadError = false;

        try {
            $mitras = DB::table('mitras')
                ->where('owner_id', $userId)
                ->orderByDesc('created_at')
                ->get()
                // Buang baris tanpa id supaya tidak merusak pembuatan URL.
                ->filter(fn($m) => !empty($m->id))
                ->values();
        } catch (\Throwable $e) {
            report($e);
            $loadError = true;
        }

        $counts = [
            'approved' => $mitras->where('status', 'approved')->count(),
            'pending' => $mitras->where('status', 'pending')->count(),
            'rejected' => $mitras->where('status', 'rejected')->count(),
        ];

        return view('owner.mitra.index', compact('mitras', 'counts', 'loadError'));
    }

    public function status(Request $request, string $mitra)
    {
        $userId = $request->session()->get('supabase.user.id');
        $row = null;
        $failed = false;

        try {
            $row = DB::table('mitras')
                ->where('id', $mitra)
                ->where('owner_id', $userId)
                ->first();
        } catch (\Throwable $e) {
            report($e);
            $failed = true;
        }

        abort_if($failed, 503, 'Data mitra tidak dapat dimuat. Coba lagi.');
        abort_if(!$row, 404);

        if ($row->status === 'approved') {
            // Pakai $mitra dari URL (sudah tervalidasi UUID & kepemilikan), bukan $row->id.
            return redirect()->route('owner.mitra.dashboard', ['mitra' => $mitra]);
        }

        abort_if(!$row->status, 404);

        return view('owner.mitra.status', ['mitra' => $row]);
    }

    // ---------------------------------------------------------------
    // Daftar usaha baru
    // ---------------------------------------------------------------

    public function create(Request $request): View
    {
        $userId = $request->session()->get('supabase.user.id');

        // Mitra terakhir yang dimiliki: dipakai untuk tombol "salin jam operasional".
        $prefill = null;
        $prefillHours = null;
        try {
            $prefill = DB::table('mitras')->where('owner_id', $userId)->orderByDesc('created_at')->first();
            if ($prefill) {
                $prefillHours = $this->loadHours($prefill->id);
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return view('owner.mitra.form', $this->formData(null, $this->defaultHours(), $prefill, $prefillHours, $userId));
    }

    public function store(Request $request): RedirectResponse
    {
        return $this->persist($request, null);
    }

    // ---------------------------------------------------------------
    // Perbaiki pengajuan yang ditolak
    // ---------------------------------------------------------------

    public function edit(Request $request, string $mitra): View
    {
        $row = $this->ownedRejected($request, $mitra);

        return view('owner.mitra.form', $this->formData($row, $this->loadHours($row->id) ?: $this->defaultHours(), null, null, null));
    }

    public function update(Request $request, string $mitra): RedirectResponse
    {
        return $this->persist($request, $this->ownedRejected($request, $mitra));
    }

    // ---------------------------------------------------------------
    // Inti penyimpanan (dipakai store & update)
    // ---------------------------------------------------------------

    private function persist(Request $request, ?object $existing): RedirectResponse
    {
        $userId = $request->session()->get('supabase.user.id');
        abort_if(!$userId, 403);

        $request->merge([
            'mitra_type' => strtoupper(trim((string) $request->input('mitra_type'))),
        ]);

        $data = $request->validate([
            'mitra_type' => ['required', Rule::in(MitraType::codes())],
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:1000'],
            'rules_text' => ['required', 'string', 'max:5000'],
            'address' => ['required', 'string', 'max:500'],
            'province' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'logo' => ['nullable', 'image', 'max:3072'],
            'photos' => ['nullable', 'array', 'max:' . self::MAX_PHOTOS],
            'photos.*' => ['image', 'max:5120'],
            'keep_gallery' => ['nullable', 'array'],
            'keep_gallery.*' => ['string', 'max:1000'],
            'owner_note' => ['nullable', 'string', 'max:300'],
            'hours' => ['required', 'array', 'size:7'],
            'hours.*.open' => ['nullable', 'date_format:H:i'],
            'hours.*.close' => ['nullable', 'date_format:H:i'],
        ], [
            'mitra_type.required' => 'Pilih jenis usaha dulu.',
            'name.required' => 'Nama usaha wajib diisi.',
            'rules_text.required' => 'Aturan & informasi layanan wajib diisi.',
            'address.required' => 'Alamat lengkap wajib diisi.',
            'latitude.required' => 'Pilih titik lokasi di peta dulu.',
            'longitude.required' => 'Pilih titik lokasi di peta dulu.',
            'photos.max' => 'Maksimal ' . self::MAX_PHOTOS . ' foto.',
            'photos.*.image' => 'File foto harus berupa gambar.',
            'photos.*.max' => 'Ukuran tiap foto maksimal 5 MB.',
            'logo.image' => 'Logo harus berupa gambar.',
            'logo.max' => 'Ukuran logo maksimal 3 MB.',
        ]);

        // ----- Foto: foto lama yang dipertahankan harus benar-benar milik mitra ini -----
        $existingGallery = $existing ? $this->parseGallery($existing->gallery_photo_urls ?? null) : [];
        $keep = array_values(array_intersect($request->input('keep_gallery', []), $existingGallery));
        $newFiles = array_values(array_filter((array) $request->file('photos', [])));
        $total = count($keep) + count($newFiles);

        if ($total < 1) {
            throw ValidationException::withMessages(['photos' => 'Minimal 1 foto wajib diupload.']);
        }
        if ($total > self::MAX_PHOTOS) {
            throw ValidationException::withMessages(['photos' => 'Maksimal ' . self::MAX_PHOTOS . ' foto.']);
        }

        // ----- Jam operasional -----
        $hourRows = [];
        foreach (range(0, 6) as $i) {
            $h = $data['hours'][$i] ?? [];
            $closed = !empty($h['closed']);
            $is24h = !$closed && !empty($h['is24h']);
            $open = $h['open'] ?? null;
            $close = $h['close'] ?? null;

            if (!$closed && !$is24h) {
                if (!$open || !$close) {
                    throw ValidationException::withMessages(['hours' => self::DAYS[$i] . ': jam buka dan jam tutup wajib diisi.']);
                }
                if ($open === $close) {
                    throw ValidationException::withMessages(['hours' => self::DAYS[$i] . ': jam buka dan tutup tidak boleh sama.']);
                }
            }

            $hourRows[] = [
                'day_of_week' => $i,
                'is_closed' => $closed,
                'is_24h' => $is24h,
                'open_time' => $open ? $open . ':00' : '09:00:00',
                'close_time' => $close ? $close . ':00' : '17:00:00',
            ];
        }

        // ----- Upload foto ke Supabase Storage -----
        try {
            $logoUrl = $existing->logo_url ?? null;
            if ($request->hasFile('logo')) {
                $logoUrl = $this->uploadPhoto($request->file('logo'), $userId, 'logo');
            }

            $uploaded = [];
            foreach ($newFiles as $f) {
                $uploaded[] = $this->uploadPhoto($f, $userId, 'gallery');
            }
        } catch (\Throwable $e) {
            report($e);

            return back()->withInput()->with('error', 'Gagal mengunggah foto. Coba lagi.');
        }

        $gallery = [...$keep, ...$uploaded];

        $fullAddress = collect([$data['address'], $data['district'] ?? '', $data['city'] ?? '', $data['province'] ?? ''])
            ->map(fn($s) => trim((string) $s))
            ->filter()
            ->implode(', ');

        $payload = [
            'mitra_type' => $data['mitra_type'],
            'name' => trim($data['name']),
            'description' => trim((string) ($data['description'] ?? '')) ?: null,
            'rules_text' => trim($data['rules_text']),
            'logo_url' => $logoUrl,
            'gallery_photo_urls' => self::GALLERY_AS_JSON ? json_encode($gallery) : $this->toPgArray($gallery),
            'address' => $fullAddress,
            'latitude' => (float) $data['latitude'],
            'longitude' => (float) $data['longitude'],
            'status' => 'pending',
        ];

        try {
            DB::transaction(function () use ($existing, $payload, $userId, $hourRows, $data) {
                if ($existing) {
                    $mitraId = $existing->id;

                    if (Schema::hasColumn('mitras', 'owner_note')) {
                        $payload['owner_note'] = trim((string) ($data['owner_note'] ?? '')) ?: null;
                    }

                    $affected = DB::table('mitras')
                        ->where('id', $mitraId)
                        ->where('owner_id', $userId)
                        ->where('status', 'rejected') // cegah dobel kirim
                        ->update($payload);

                    if (!$affected) {
                        throw new \RuntimeException('Status mitra sudah berubah.');
                    }
                } else {
                    $mitraId = (string) Str::uuid();
                    DB::table('mitras')->insert($payload + [
                        'id' => $mitraId,
                        'owner_id' => $userId,
                        'created_at' => now(),
                    ]);
                }

                DB::table('mitra_operating_hours')->where('mitra_id', $mitraId)->delete();
                DB::table('mitra_operating_hours')->insert(
                    array_map(fn($r) => $r + ['mitra_id' => $mitraId], $hourRows)
                );
            });
        } catch (\Throwable $e) {
            report($e);

            return back()->withInput()->with('error', 'Gagal menyimpan data usaha. Muat ulang halaman lalu coba lagi.');
        }

        return redirect()->route('owner.mitra')->with(
            'success',
            $existing ? 'Pengajuan dikirim ulang dan menunggu peninjauan Admin.' : 'Usaha berhasil didaftarkan dan menunggu peninjauan Admin.'
        );
    }

    // ---------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------

    private function ownedRejected(Request $request, string $mitra): object
    {
        $userId = $request->session()->get('supabase.user.id');

        try {
            $row = DB::table('mitras')->where('id', strtolower($mitra))->where('owner_id', $userId)->first();
        } catch (\Throwable $e) {
            report($e);
            abort(503, 'Data mitra tidak dapat dimuat. Coba lagi.');
        }

        abort_if(!$row, 404);
        // Hanya pengajuan yang ditolak yang boleh diperbaiki.
        abort_if($row->status !== 'rejected', 403);

        return $row;
    }

    private function formData(?object $mitra, array $hours, ?object $prefill, ?array $prefillHours, ?string $userId): array
    {
        $types = collect(MitraType::codes())
            ->map(fn($code) => ['code' => $code] + MitraType::info($code))
            ->all();

        $owner = null;
        $uid = $userId ?? ($mitra->owner_id ?? null);
        if ($uid) {
            try {
                $owner = DB::table('profiles')->where('id', $uid)->first();
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return [
            'mitra' => $mitra,
            'types' => $types,
            'days' => self::DAYS,
            'hours' => $hours,
            'gallery' => $mitra ? $this->parseGallery($mitra->gallery_photo_urls ?? null) : [],
            'maxPhotos' => self::MAX_PHOTOS,
            'prefill' => $prefill,
            'prefillHours' => $prefillHours,
            'owner' => $owner,
        ];
    }

    private function defaultHours(): array
    {
        return array_map(fn() => ['closed' => false, 'is24h' => false, 'open' => '09:00', 'close' => '17:00'], range(0, 6));
    }

    /** Jam operasional tersimpan -> array 7 hari berformat HH:MM. */
    private function loadHours(string $mitraId): array
    {
        $hours = $this->defaultHours();

        $rows = DB::table('mitra_operating_hours')->where('mitra_id', $mitraId)->get();
        foreach ($rows as $r) {
            $d = (int) $r->day_of_week;
            if ($d < 0 || $d > 6) {
                continue;
            }
            $hours[$d] = [
                'closed' => (bool) $r->is_closed,
                'is24h' => (bool) $r->is_24h,
                'open' => $r->open_time ? substr((string) $r->open_time, 0, 5) : '09:00',
                'close' => $r->close_time ? substr((string) $r->close_time, 0, 5) : '17:00',
            ];
        }

        return $hours;
    }

    private function parseGallery(mixed $value): array
    {
        if (is_array($value)) {
            return array_values($value);
        }
        if (!is_string($value) || $value === '') {
            return [];
        }
        $json = json_decode($value, true);
        if (is_array($json)) {
            return array_values($json);
        }

        // Format array Postgres: {"url1","url2"}
        return array_values(array_filter(str_getcsv(trim($value, '{}'), ',', '"')));
    }

    private function toPgArray(array $items): string
    {
        return '{' . implode(',', array_map(fn($u) => '"' . addcslashes($u, '"\\') . '"', $items)) . '}';
    }

    /** Unggah ke Supabase Storage (bucket publik) dan kembalikan URL publiknya. */
    private function uploadPhoto(UploadedFile $file, string $userId, string $prefix): string
    {
        $base = rtrim((string) config('services.supabase.url'), '/');
        $key = (string) config('services.supabase.service_role_key');
        $bucket = (string) config('services.supabase.bucket', 'petshop-photos');

        if ($base === '' || $key === '') {
            throw new \RuntimeException('Konfigurasi Supabase Storage belum diisi.');
        }

        $ext = strtolower($file->extension() ?: 'jpg');
        $path = "mitra/{$userId}/{$prefix}_" . now()->timestamp . '_' . Str::random(8) . '.' . $ext;

        $res = Http::withToken($key)
            ->withHeaders(['apikey' => $key, 'x-upsert' => 'true'])
            ->withBody(file_get_contents($file->getRealPath()), $file->getMimeType() ?: 'image/jpeg')
            ->post("{$base}/storage/v1/object/{$bucket}/{$path}");

        if (!$res->successful()) {
            throw new \RuntimeException('Upload gagal: ' . $res->status() . ' ' . $res->body());
        }

        return "{$base}/storage/v1/object/public/{$bucket}/{$path}";
    }
}