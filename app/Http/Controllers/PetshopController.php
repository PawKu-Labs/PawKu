<?php

namespace App\Http\Controllers;

use App\Support\MitraDisplay;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Halaman publik: cari dan lihat detail Mitra (padanan PetshopListScreen / PetshopDetailScreen di Flutter).
 *
 * PENTING (keamanan): koneksi database Laravel melewati RLS, jadi aturan akses ditegakkan di sini:
 *  - hanya Mitra berstatus 'approved' yang bisa dilihat (selain itu 404);
 *  - hanya kolom publik yang diambil (tanpa owner_id, approved_by, rejection_reason, owner_note);
 *  - jam operasional dan ulasan hanya untuk user login, sama seperti policy RLS tabelnya.
 */
class PetshopController extends Controller
{
    private const LIST_COLUMNS = [
        'id', 'name', 'mitra_type', 'address', 'logo_url', 'photo_url', 'gallery_photo_urls',
    ];

    private const DETAIL_COLUMNS = [
        'id', 'name', 'mitra_type', 'phone_number', 'address', 'latitude', 'longitude',
        'description', 'rules_text', 'photo_url', 'logo_url', 'gallery_photo_urls',
    ];

    private const REVIEW_SORTS = ['terbaru', 'terlama', 'nama'];

    public function index(Request $request): View
    {
        $q = mb_substr(trim((string) $request->query('q', '')), 0, 100);

        try {
            $query = DB::table('mitras')
                ->select(self::LIST_COLUMNS)
                ->where('status', 'approved');

            if ($q !== '') {
                // Escape karakter khusus LIKE agar "%" dan "_" dicari apa adanya.
                $like = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $q).'%';
                $query->where(function ($w) use ($like) {
                    $w->where('name', 'ilike', $like)->orWhere('address', 'ilike', $like);
                });
            }

            $shops = $query->orderBy('name')->paginate(12)->withQueryString();
        } catch (\Throwable $e) {
            report($e);
            abort(503, 'Daftar petshop tidak dapat dimuat. Coba lagi.');
        }

        $shops->through(fn ($row) => MitraDisplay::prepare($row));

        return view('petshops.index', ['shops' => $shops, 'q' => $q]);
    }

    public function show(Request $request, string $mitra): View
    {
        abort_unless(Str::isUuid($mitra), 404);

        try {
            $row = DB::table('mitras')
                ->select(self::DETAIL_COLUMNS)
                ->where('id', $mitra)
                ->where('status', 'approved')
                ->first();
        } catch (\Throwable $e) {
            report($e);
            abort(503, 'Data petshop tidak dapat dimuat. Coba lagi.');
        }

        abort_if(! $row, 404);

        $shop = MitraDisplay::prepare($row);
        $loggedIn = MitraDisplay::isLoggedIn();

        $services = $this->safe(fn () => DB::table('mitra_services')
            ->select('id', 'name', 'price', 'duration_minutes')
            ->where('mitra_id', $shop->id)
            ->where('is_active', true)
            ->orderBy('price')
            ->get(), collect());

        // Rata-rata rating bersifat agregat dan tidak memuat data pribadi, jadi tampil untuk semua orang.
        $rating = $this->safe(fn () => DB::selectOne(
            'select avg_rating, review_count from public.get_petshop_rating(?::uuid)',
            [$shop->id]
        ), null);
        $avg = $rating?->avg_rating !== null ? (float) $rating->avg_rating : null;
        $reviewCount = (int) ($rating->review_count ?? 0);

        $hours = collect();
        $reviews = collect();
        $sort = 'terbaru';

        // Jam operasional dan ulasan: hanya user login (mengikuti policy RLS).
        if ($loggedIn) {
            $hours = $this->safe(fn () => DB::table('mitra_operating_hours')
                ->where('mitra_id', $shop->id)
                ->orderBy('day_of_week')
                ->get(), collect());

            $rows = $this->safe(fn () => DB::select(
                'select id, rating, comment, created_at, cat_name, service_name, reviewer_name '
                .'from public.get_petshop_reviews(?::uuid)',
                [$shop->id]
            ), []);

            $requested = (string) $request->query('sort', 'terbaru');
            $sort = in_array($requested, self::REVIEW_SORTS, true) ? $requested : 'terbaru';
            $reviews = $this->prepareReviews($rows, $sort);
        }

        return view('petshops.show', [
            'shop' => $shop,
            'services' => $services,
            'avg' => $avg,
            'reviewCount' => $reviewCount,
            'hours' => $hours,
            'today' => MitraDisplay::todayStatus($hours),
            'todayIndex' => MitraDisplay::todayIndex(),
            'reviews' => $reviews,
            'sort' => $sort,
            'loggedIn' => $loggedIn,
            'routeUrl' => MitraDisplay::routeUrl($shop->latitude, $shop->longitude),
        ]);
    }

    private function prepareReviews(array $rows, string $sort): Collection
    {
        $reviews = collect($rows)->map(function ($r) {
            $r->reviewer = trim((string) $r->reviewer_name) !== '' ? $r->reviewer_name : 'Pengguna Pawku';
            $r->stars = max(0, min(5, (int) $r->rating));
            $r->ts = $r->created_at ? (strtotime((string) $r->created_at) ?: 0) : 0;
            $r->date = MitraDisplay::date($r->created_at);

            return $r;
        });

        return match ($sort) {
            'terlama' => $reviews->sortBy('ts')->values(),
            'nama' => $reviews->sortBy(fn ($r) => mb_strtolower($r->reviewer))->values(),
            default => $reviews->sortByDesc('ts')->values(),
        };
    }

    /** Data pelengkap yang gagal dimuat tidak boleh merusak seluruh halaman. */
    private function safe(callable $fn, mixed $fallback): mixed
    {
        try {
            return $fn();
        } catch (\Throwable $e) {
            report($e);

            return $fallback;
        }
    }
}