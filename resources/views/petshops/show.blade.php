@extends('petshops.layout')
@section('title', $shop->name)

@section('content')
    @php
        $t = $shop->type;
        // Bila header sudah memakai foto galeri pertama (tidak ada logo), jangan diulang di baris galeri.
        $galleryToShow = $shop->logo === null && count($shop->gallery) > 0 ? array_slice($shop->gallery, 1) : $shop->gallery;
        $viewerUrls = array_values(array_filter([$shop->image, ...$galleryToShow]));
        $hasDesc = trim((string) $shop->description) !== '';
        $hasRules = trim((string) $shop->rules_text) !== '';
        $avgText = $avg !== null ? number_format($avg, 1, '.', '') : null;
        $dist = [];
        for ($s = 5; $s >= 1; $s--) {
            $dist[$s] = $reviews->where('stars', $s)->count();
        }
        $reviewTotal = max(1, $reviews->count());
        $topReviews = $reviews->take(4);
        $restReviews = $reviews->slice(4);
    @endphp

    <section class="ps-page">
        <a href="{{ route('petshops.index') }}" class="ps-back">← Semua petshop</a>

        {{-- ---------- HERO ---------- --}}
        <div class="ps-hero" @if ($shop->image) data-lb="0" style="cursor:zoom-in;" @endif>
            @if ($shop->image)
                <img src="{{ $shop->image }}" alt="Foto {{ $shop->name }}">
            @else
                <div class="ps-hero-fallback" style="background: {{ $t['soft'] }};">{{ $t['icon'] }}</div>
            @endif
        </div>

        <h1 class="ps-name">{{ $shop->name }}</h1>
        <div class="ps-pills">
            <span class="ps-pill" style="color: {{ $t['color'] }}; background: {{ $t['soft'] }};">{{ $t['icon'] }}
                {{ $t['label'] }}</span>
            @if ($today)
                <span class="ps-pill"
                    style="color: {{ $today['open'] ? '#16a34a' : '#ef4444' }}; background: {{ $today['open'] ? '#f0fdf4' : '#fef2f2' }};">
                    🕒 {{ $today['text'] }}</span>
            @endif
            <span class="ps-pill" style="color:#b45309; background:#fffbeb;">
                @if ($avgText !== null)
                    ★ {{ $avgText }} ({{ $reviewCount }})
                @else
                    Belum ada ulasan
                @endif
            </span>
        </div>

        {{-- ---------- AKSI CEPAT ---------- --}}
        @if ($shop->contact || $routeUrl)
            <div class="ps-actions">
                @if ($shop->contact)
                    <a href="tel:{{ $shop->contact['tel'] }}" class="ps-action"
                        style="background:#eef7f2; color:var(--ps-primary);">📞 Telepon</a>
                    <a href="https://wa.me/{{ $shop->contact['wa'] }}" target="_blank" rel="noopener noreferrer"
                        class="ps-action" style="background:#e9fbef; color:#1da851;">💬 WhatsApp</a>
                @endif
                @if ($routeUrl)
                    <a href="{{ $routeUrl }}" target="_blank" rel="noopener noreferrer" class="ps-action"
                        style="background: {{ $t['soft'] }}; color: {{ $t['color'] }};">🧭 Rute</a>
                @endif
            </div>
        @endif

        {{-- ---------- GALERI ---------- --}}
        @if (count($galleryToShow) > 0)
            <div class="ps-gallery">
                @foreach ($galleryToShow as $i => $url)
                    <img src="{{ $url }}" alt="Foto galeri {{ $shop->name }}" loading="lazy"
                        data-lb="{{ $shop->image ? $i + 1 : $i }}">
                @endforeach
            </div>
        @endif

        {{-- ---------- ATURAN & INFORMASI ---------- --}}
        @if ($hasRules)
            <div class="ps-section">
                <div class="ps-box ps-box-warn">
                    <div style="font-weight:800; margin-bottom:6px;">ℹ️ Aturan &amp; Informasi</div>
                    {!! nl2br(e(trim($shop->rules_text))) !!}
                </div>
            </div>
        @endif

        {{-- ---------- TENTANG ---------- --}}
        @if ($hasDesc)
            <div class="ps-section">
                <h2 class="ps-h2">Tentang</h2>
                <div class="ps-box">{!! nl2br(e(trim($shop->description))) !!}</div>
            </div>
        @endif

        {{-- ---------- LOKASI ---------- --}}
        @if (!empty($shop->address))
            <div class="ps-section">
                <h2 class="ps-h2">Lokasi</h2>
                <div class="ps-box">📍 {{ $shop->address }}</div>
            </div>
        @endif

        {{-- ---------- JAM OPERASIONAL (hanya user login) ---------- --}}
        <div class="ps-section">
            <h2 class="ps-h2">Jam Operasional</h2>
            @if (!$loggedIn)
                <div class="ps-box ps-lock">
                    🔒 <a href="{{ route('login') }}" style="color:var(--ps-primary); font-weight:700;">Masuk</a>
                    untuk melihat jam operasional.
                </div>
            @elseif ($hours->isEmpty())
                <div class="ps-box ps-lock">Jam operasional belum diatur.</div>
            @else
                <div class="ps-box">
                    @foreach ($hours as $h)
                        @php $isToday = (int) $h->day_of_week === $todayIndex; @endphp
                        <div class="ps-hours-row {{ $isToday ? 'today' : '' }}">
                            <span>{{ \App\Support\MitraDisplay::dayName((int) $h->day_of_week) }}@if ($isToday)
                                    <small>(hari ini)</small>
                                @endif
                            </span>
                            <span style="{{ !empty($h->is_closed) ? 'color:var(--ps-danger);' : '' }}">
                                {{ \App\Support\MitraDisplay::hourLabel($h) }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ---------- LAYANAN ---------- --}}
        <div class="ps-section">
            <h2 class="ps-h2">Layanan &amp; Harga</h2>
            @forelse ($services as $svc)
                <div class="ps-service">
                    <div class="ps-service-ico" style="background: {{ $t['soft'] }};">{{ $t['icon'] }}</div>
                    <div style="min-width:0; flex:1;">
                        <div style="font-weight:700;">{{ $svc->name }}</div>
                        <div style="font-size:13px; color:var(--ps-muted);">
                            {{ \App\Support\MitraDisplay::serviceMeta($svc, $shop->mitra_type) }}</div>
                    </div>
                </div>
            @empty
                <div class="ps-box ps-lock">{{ $t['label'] }} ini belum menambahkan layanan.</div>
            @endforelse
            @if ($services->isNotEmpty())
                <p style="font-size:12.5px; color:var(--ps-muted); margin:8px 2px 0;">
                    Pemesanan layanan saat ini tersedia lewat aplikasi PawKu.
                </p>
            @endif
        </div>

        {{-- ---------- ULASAN ---------- --}}
        <div class="ps-section" id="ulasan">
            @if (!$loggedIn)
                @if ($reviewCount > 0)
                    <h2 class="ps-h2">Ulasan Pelanggan</h2>
                    <div class="ps-box ps-lock">
                        🔒 <a href="{{ route('login') }}" style="color:var(--ps-primary); font-weight:700;">Masuk</a>
                        untuk membaca {{ $reviewCount }} ulasan pelanggan.
                    </div>
                @endif
            @elseif ($reviews->isNotEmpty())
                <h2 class="ps-h2">Ulasan Pelanggan <span class="ps-pill"
                        style="color:var(--ps-primary); background:#eef7f2; margin:0 0 0 6px;">{{ $reviews->count() }}</span>
                </h2>

                <div class="ps-box" style="display:flex; gap:22px; align-items:center; flex-wrap:wrap;">
                    <div style="text-align:center; min-width:90px;">
                        <div style="font-size:34px; font-weight:800; line-height:1;">{{ $avgText ?? '-' }}</div>
                        <div class="ps-stars">
                            {{ str_repeat('★', $avg !== null ? (int) round($avg) : 0) }}{{ str_repeat('☆', 5 - ($avg !== null ? (int) round($avg) : 0)) }}
                        </div>
                        <div style="font-size:12px; color:var(--ps-muted);">{{ $reviewCount }} ulasan</div>
                    </div>
                    <div style="flex:1; min-width:180px;">
                        @foreach ($dist as $star => $n)
                            <div style="display:flex; align-items:center; gap:8px; margin:4px 0; font-size:12px;">
                                <span style="width:10px; font-weight:700;">{{ $star }}</span>
                                <div class="ps-bar"><span style="width: {{ round(($n / $reviewTotal) * 100) }}%;"></span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="ps-chips">
                    @foreach (['terbaru' => 'Terbaru', 'terlama' => 'Terlama', 'nama' => 'Nama A–Z'] as $key => $label)
                        <a href="{{ route('petshops.show', ['mitra' => $shop->id, 'sort' => $key]) }}#ulasan"
                            class="ps-chip {{ $sort === $key ? 'on' : '' }}">{{ $label }}</a>
                    @endforeach
                </div>

                @foreach ($topReviews as $r)
                    @include('petshops._review', ['r' => $r])
                @endforeach

                @if ($restReviews->isNotEmpty())
                    <details>
                        <summary style="cursor:pointer; font-weight:700; color:var(--ps-primary); margin:6px 0 12px;">
                            Lihat {{ $restReviews->count() }} ulasan lainnya</summary>
                        @foreach ($restReviews as $r)
                            @include('petshops._review', ['r' => $r])
                        @endforeach
                    </details>
                @endif
            @endif
        </div>
    </section>

    {{-- ---------- LIGHTBOX FOTO ---------- --}}
    @if (count($viewerUrls) > 0)
        <dialog class="ps-lb" id="psLightbox" aria-label="Pratinjau foto">
            <img id="psLbImg" src="" alt="Pratinjau foto">
            <div class="ps-lb-bar">
                <button type="button" id="psLbPrev" @disabled(count($viewerUrls) < 2)>‹ Sebelumnya</button>
                <button type="button" id="psLbClose">Tutup</button>
                <button type="button" id="psLbNext" @disabled(count($viewerUrls) < 2)>Berikutnya ›</button>
            </div>
        </dialog>
    @endif
@endsection

@push('scripts')
    @if (count($viewerUrls) > 0)
        <script>
            (function () {
                const urls = @js($viewerUrls);
                const dlg = document.getElementById('psLightbox');
                const img = document.getElementById('psLbImg');
                let cur = 0;

                function show(n) {
                    cur = (n + urls.length) % urls.length;
                    img.src = urls[cur];
                }

                document.querySelectorAll('[data-lb]').forEach(function (el) {
                    el.addEventListener('click', function () {
                        show(parseInt(el.dataset.lb, 10) || 0);
                        dlg.showModal();
                    });
                });
                document.getElementById('psLbPrev').addEventListener('click', function () { show(cur - 1); });
                document.getElementById('psLbNext').addEventListener('click', function () { show(cur + 1); });
                document.getElementById('psLbClose').addEventListener('click', function () { dlg.close(); });
                dlg.addEventListener('click', function (e) { if (e.target === dlg) dlg.close(); });
            })();
        </script>
    @endif
@endpush