<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;

class MitraUrl
{
    /**
     * URL tujuan untuk satu mitra: dashboard bila approved, halaman status bila belum.
     * Mengembalikan null bila id kosong, sehingga route() tidak pernah dipanggil dengan parameter kosong.
     */
    public static function for(?object $mitra, array $query = []): ?string
    {
        $id = (string) ($mitra->id ?? '');

        if ($id === '') {
            return null;
        }

        $name = ($mitra->status ?? null) === 'approved'
            ? (Route::has('owner.mitra.dashboard.unit') ? 'owner.mitra.dashboard.unit' : 'owner.mitra.dashboard')
            : 'owner.mitra.status';

        return route($name, ['mitra' => $id] + $query);
    }
}
