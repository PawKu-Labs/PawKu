<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Riwayat Booking milik pemilik kucing.
 *
 * PENTING (keamanan): koneksi Laravel memakai role "postgres" yang MELEWATI RLS,
 * jadi setiap query WAJIB memfilter bookings.owner_id = user yang login.
 */
class OwnerBookingController extends Controller
{
    /** Status akhir: booking sudah tidak aktif (dipakai juga untuk hitung "Booking Mendatang"). */
    public const FINAL_STATUSES = ['selesai', 'dibatalkan', 'ditolak', 'kedaluwarsa'];

    /** Label & warna badge. Status di luar daftar ini tetap tampil (label otomatis). */
    private const STATUS_META = [
        'selesai'     => ['Selesai', 'ok'],
        'dibatalkan'  => ['Dibatalkan', 'muted'],
        'ditolak'     => ['Ditolak', 'err'],
        'kedaluwarsa' => ['Kedaluwarsa', 'muted'],
    ];

    public function index(Request $request): View
    {
        $userId = $this->userId($request);

        $status = trim((string) $request->query('status', ''));
        $catId = trim((string) $request->query('cat_id', ''));
        if ($catId !== '' && ! Str::isUuid($catId)) {
            $catId = '';
        }

        try {
            $bookings = DB::table('bookings as b')
                ->leftJoin('cats as c', 'c.id', '=', 'b.cat_id')
                ->leftJoin('mitras as m', 'm.id', '=', 'b.mitra_id')
                ->where('b.owner_id', $userId)
                ->when($status !== '', fn ($q) => $q->where('b.status', $status))
                ->when($catId !== '', fn ($q) => $q->where('b.cat_id', $catId))
                ->select([
                    'b.id', 'b.status', 'b.notes', 'b.queue_number',
                    'b.service_name', 'b.service_price',
                    'b.slot_date', 'b.slot_start_time', 'b.slot_end_time',
                    'b.check_in_at', 'b.check_out_at', 'b.created_at',
                    'c.name as cat_name', 'c.photo_url as cat_photo',
                    'm.name as mitra_name',
                ])
                ->orderByRaw('b.slot_date desc nulls last, b.slot_start_time desc nulls last, b.created_at desc')
                ->paginate(10)
                ->withQueryString();

            $cats = DB::table('cats')
                ->where('owner_id', $userId)
                ->orderBy('name')
                ->get(['id', 'name']);

            // Opsi filter diambil dari status yang benar-benar ada di data user ini.
            $statuses = DB::table('bookings')
                ->where('owner_id', $userId)
                ->whereNotNull('status')
                ->distinct()
                ->orderBy('status')
                ->pluck('status')
                ->map(fn ($s) => [
                    'value' => $s,
                    'label' => self::STATUS_META[$s][0] ?? Str::headline($s),
                ]);
        } catch (\Throwable $e) {
            report($e);
            abort(503, 'Riwayat booking tidak dapat dimuat. Coba lagi.');
        }

        $bookings->getCollection()->transform(function ($b) {
            [$label, $tone] = self::STATUS_META[$b->status]
                ?? [Str::headline((string) ($b->status ?: 'Tidak diketahui')), 'info'];

            $b->status_label = $label;
            $b->status_tone = $tone;
            $b->date_label = $b->slot_date
                ? Carbon::parse($b->slot_date)->locale('id')->translatedFormat('l, d F Y')
                : null;
            $b->time_label = $b->slot_start_time
                ? substr($b->slot_start_time, 0, 5).($b->slot_end_time ? ' – '.substr($b->slot_end_time, 0, 5) : '')
                : null;
            $b->created_label = $b->created_at
                ? Carbon::parse($b->created_at)->timezone('Asia/Jakarta')->locale('id')->translatedFormat('d M Y, H:i')
                : null;
            $b->check_in_label = $b->check_in_at
                ? Carbon::parse($b->check_in_at)->timezone('Asia/Jakarta')->format('H:i')
                : null;
            $b->check_out_label = $b->check_out_at
                ? Carbon::parse($b->check_out_at)->timezone('Asia/Jakarta')->format('H:i')
                : null;
            $b->price_label = $b->service_price !== null
                ? 'Rp '.number_format((float) $b->service_price, 0, ',', '.')
                : null;

            return $b;
        });

        return view('owner.bookings.index', [
            'bookings' => $bookings,
            'cats' => $cats,
            'statuses' => $statuses,
            'filtered' => $status !== '' || $catId !== '',
            'status' => $status,
            'catId' => $catId,
        ]);
    }

    private function userId(Request $request): string
    {
        $id = (string) $request->session()->get('supabase.user.id', '');
        abort_if(! Str::isUuid($id), 403, 'Anda tidak memiliki akses ke halaman ini.');

        return $id;
    }
}