<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Helper tampilan data Mitra untuk halaman publik.
 * Padanan logika model Petshop / OperatingHour / PetshopService di aplikasi Flutter.
 */
class MitraDisplay
{
    public const TZ = 'Asia/Jakarta';

    private const MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

    private const DAYS = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

    /**
     * Pengunjung dianggap sudah login bila session Supabase masih punya token aktif
     * atau refresh token (token akan disegarkan otomatis di halaman terproteksi).
     */
    public static function isLoggedIn(): bool
    {
        if (! session('supabase.access_token')) {
            return false;
        }

        return (int) session('supabase.expires_at', 0) > now()->timestamp
            || filled(session('supabase.refresh_token'));
    }

    /** Tambahkan properti tampilan ke satu baris mitras (gallery, logo, image, type, contact). */
    public static function prepare(object $row): object
    {
        $gallery = array_values(array_filter(array_map(
            [self::class, 'safeUrl'],
            self::parseArray($row->gallery_photo_urls ?? null)
        )));

        $logo = self::safeUrl($row->logo_url ?? null);
        $photo = self::safeUrl($row->photo_url ?? null);

        $row->logo = $logo;
        $row->gallery = $gallery;
        // Urutan sama dengan Petshop.displayImageUrl di Flutter: logo -> galeri -> photo_url lama.
        $row->image = $logo ?? ($gallery[0] ?? $photo);
        $row->type = MitraType::info($row->mitra_type ?? null);
        $row->contact = self::contact($row->phone_number ?? null);

        return $row;
    }

    /** Hanya izinkan URL http(s). */
    public static function safeUrl(mixed $value): ?string
    {
        return is_string($value) && preg_match('#^https?://#i', $value) ? $value : null;
    }

    /** Kolom text[] Postgres bisa kembali sebagai array, JSON, atau literal {"a","b"}. */
    public static function parseArray(mixed $value): array
    {
        if (is_array($value)) {
            return array_values($value);
        }
        if (! is_string($value) || $value === '') {
            return [];
        }
        $json = json_decode($value, true);
        if (is_array($json)) {
            return array_values($json);
        }

        return array_values(array_filter(str_getcsv(trim($value, '{}'), ',', '"')));
    }

    /** Nomor telepon -> tautan tel: dan WhatsApp (normalisasi sama dengan Flutter). */
    public static function contact(?string $raw): ?array
    {
        $tel = preg_replace('/[^0-9+]/', '', trim((string) $raw));
        if ($tel === '' || $tel === '+') {
            return null;
        }

        $wa = ltrim($tel, '+');
        if (str_starts_with($wa, '0')) {
            $wa = '62'.substr($wa, 1);
        }

        return ['tel' => $tel, 'wa' => $wa];
    }

    public static function rupiah(mixed $price): string
    {
        return 'Rp '.number_format((float) $price, 0, ',', '.');
    }

    /** Teks harga layanan: hotel per malam, lainnya dengan durasi bila ada. */
    public static function serviceMeta(object $service, ?string $mitraType): string
    {
        $price = self::rupiah($service->price ?? 0);

        if (strtoupper((string) $mitraType) === 'PAW_HOTEL') {
            return $price.' / malam';
        }
        if (! empty($service->duration_minutes)) {
            return $price.' • '.(int) $service->duration_minutes.' menit';
        }

        return $price;
    }

    public static function dayName(int $index): string
    {
        return self::DAYS[$index] ?? '-';
    }

    /** Indeks hari ini (0 = Senin) dalam WIB. */
    public static function todayIndex(): int
    {
        return Carbon::now(self::TZ)->dayOfWeekIso - 1;
    }

    public static function hourLabel(object $h): string
    {
        if (! empty($h->is_closed)) {
            return 'Libur';
        }
        if (! empty($h->is_24h)) {
            return 'Buka 24 Jam';
        }
        if (empty($h->open_time) || empty($h->close_time)) {
            return 'Belum diatur';
        }

        return substr($h->open_time, 0, 5).' - '.substr($h->close_time, 0, 5);
    }

    /** Status buka/tutup hari ini (WIB). Null bila tidak bisa ditentukan. */
    public static function todayStatus(Collection $hours): ?array
    {
        if ($hours->isEmpty()) {
            return null;
        }

        $now = Carbon::now(self::TZ);
        $h = $hours->last(fn ($x) => (int) $x->day_of_week === $now->dayOfWeekIso - 1);
        if (! $h) {
            return null;
        }

        if (! empty($h->is_closed)) {
            return ['text' => 'Tutup hari ini', 'open' => false];
        }
        if (! empty($h->is_24h)) {
            return ['text' => 'Buka 24 jam', 'open' => true];
        }
        if (empty($h->open_time) || empty($h->close_time)) {
            return null;
        }

        $open = substr($h->open_time, 0, 8);
        $close = substr($h->close_time, 0, 8);
        $nowStr = $now->format('H:i:s');
        $isOpen = strcmp($nowStr, $open) >= 0 && strcmp($nowStr, $close) < 0;

        return $isOpen
            ? ['text' => 'Buka sampai '.substr($close, 0, 5), 'open' => true]
            : ['text' => 'Tutup • buka '.substr($open, 0, 5), 'open' => false];
    }

    /** "5 Okt 2026" dalam WIB. */
    public static function date(?string $value): string
    {
        if (! $value) {
            return '';
        }

        try {
            $d = Carbon::parse($value)->setTimezone(self::TZ);
        } catch (\Throwable) {
            return '';
        }

        return $d->day.' '.self::MONTHS[$d->month - 1].' '.$d->year;
    }

    public static function routeUrl(mixed $lat, mixed $lng): ?string
    {
        if (! is_numeric($lat) || ! is_numeric($lng)) {
            return null;
        }

        return 'https://www.google.com/maps/dir/?api=1&destination='.(float) $lat.','.(float) $lng;
    }
}