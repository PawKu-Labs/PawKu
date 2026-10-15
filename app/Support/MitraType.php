<?php

namespace App\Support;

class MitraType
{
    // Hex warna = tebakan, samakan dengan AppColors di Flutter.
    private const TYPES = [
        'PAW_SHOP' => ['label' => 'Pet Shop', 'brand' => 'PAW SHOP', 'icon' => '🏪', 'color' => '#7c3aed', 'soft' => '#f5f3ff'],
        'PAW_HEALTH' => ['label' => 'Klinik', 'brand' => 'PAW HEALTH', 'icon' => '🩺', 'color' => '#e11d48', 'soft' => '#fff1f2'],
        'PAW_HOTEL' => ['label' => 'Hotel Kucing', 'brand' => 'PAW HOTEL', 'icon' => '🏨', 'color' => '#2563eb', 'soft' => '#eff6ff'],
        'PAW_GROOMING' => ['label' => 'Pet Grooming', 'brand' => 'PAW GROOMING', 'icon' => '🛁', 'color' => '#0d9488', 'soft' => '#f0fdfa'],
    ];

    /** Jenis tak dikenal jatuh ke Pet Shop, sama seperti mobile. */
    public static function info(?string $code): array
    {
        return self::TYPES[$code] ?? self::TYPES['PAW_SHOP'];
    }
}