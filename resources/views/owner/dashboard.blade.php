@extends('owner.layout')
@section('title', 'Dashboard')

@push('styles')
    <style>
        /* ---------- Hero sapaan ---------- */
        .hero {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            padding: 28px 32px;
            border-radius: 20px;
            background: var(--primary-light);
            border: 1px solid #F6D2B0;
            overflow: hidden
        }

        .hero::after {
            /* jejak kaki besar sebagai dekorasi, mengacu ke logo */
            content: '🐾';
            position: absolute;
            right: 150px;
            bottom: -26px;
            font-size: 120px;
            opacity: .12;
            transform: rotate(-18deg);
            pointer-events: none
        }

        .hero-text {
            position: relative;
            z-index: 1;
            min-width: 0
        }

        .hero .page-title {
            font-size: 24px;
            line-height: 1.3
        }

        .hero .page-sub {
            color: var(--body);
            font-size: 14px;
            margin-top: 6px;
            max-width: 46ch
        }

        .hero-logo {
            position: relative;
            z-index: 1;
            width: 128px;
            height: 128px;
            object-fit: contain;
            flex-shrink: 0
        }

        /* ---------- Statistik ---------- */
        .stat-value small {
            font-size: 13px;
            font-weight: 500;
            color: var(--muted);
            margin-left: 4px
        }

        /* ---------- Aksi cepat ---------- */
        .action {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 4px;
            padding: 22px;
            text-decoration: none;
            color: inherit;
            transition: transform .2s, border-color .2s, box-shadow .2s
        }

        a.action:hover {
            transform: translateY(-3px);
            border-color: var(--accent);
            box-shadow: 0 10px 22px -12px var(--accent)
        }

        .action-icon {
            width: 56px;
            height: 56px;
            border-radius: 18px;
            background: var(--accent-soft);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 12px
        }

        .action-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--heading)
        }

        .action-desc {
            font-size: 12.5px;
            line-height: 1.5;
            color: var(--body)
        }

        .action-cta {
            margin-top: 14px;
            font-size: 12.5px;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 30px;
            background: var(--accent-soft);
            color: var(--heading)
        }

        .action.is-soon {
            opacity: .85;
            cursor: default
        }

        .action.is-soon .action-cta {
            background: var(--bg);
            color: var(--muted)
        }

        @media(max-width:600px) {
            .hero {
                padding: 22px 20px
            }

            .hero-logo {
                width: 84px;
                height: 84px
            }

            .hero::after {
                display: none
            }

            .hero .page-title {
                font-size: 19px
            }
        }
    </style>
@endpush

@section('content')
    @php
        $displayName = $profile['full_name'] ?? ($profile['username'] ?? 'Pemilik Kucing');

        // Kartu akan otomatis jadi link kalau route-nya ada.
        // Ganti nama route di bawah kalau berbeda dengan yang Anda pakai.
        $catsRoute = Route::has('owner.cats.index') ? route('owner.cats.index') : null;
        $bookingsRoute = Route::has('owner.bookings.index') ? route('owner.bookings.index') : null;

        $actions = [
            [
                'icon' => '🐱',
                'title' => 'Profil Kucing',
                'desc' => 'Lihat dan kelola profil kucing Anda.',
                'href' => $catsRoute,
                'cta' => 'Lihat profil',
                'accent' => 'var(--feeding)',
                'soft' => 'var(--feeding-soft)',
            ],
            [
                'icon' => '💉',
                'title' => 'Kalkulator Vaksin',
                'desc' => 'Hitung jadwal vaksinasi kucing Anda.',
                'href' => route('owner.vaccine.calculator'),
                'cta' => 'Hitung jadwal',
                'accent' => 'var(--vaccine)',
                'soft' => 'var(--vaccine-soft)',
            ],
            [
                'icon' => '📋',
                'title' => 'Riwayat Booking',
                'desc' => 'Lihat riwayat booking grooming Anda.',
                'href' => $bookingsRoute,
                'cta' => 'Lihat riwayat',
                'accent' => 'var(--booking)',
                'soft' => 'var(--booking-soft)',
            ],
        ];
    @endphp

    <div class="hero">
        <div class="hero-text">
            <h1 class="page-title">Halo, {{ $displayName }}! 🐱</h1>
            <p class="page-sub">Kelola profil kucing dan pantau jadwal grooming Anda di satu tempat.</p>
        </div>
        <img class="hero-logo" src="{{ asset('assets/icon/pawku-nobg.png') }}" alt="PawKu"
            onerror="this.onerror=null;this.src='https://raw.githubusercontent.com/fashaputri/asset/main/pawku.png';">
    </div>

    <section>
        <div class="group-title">Ringkasan</div>
        <div class="grid g3">
            <div class="card stat">
                <div class="stat-top">
                    <span class="stat-label">Kucing Saya</span>
                    <div class="stat-icon" style="background:var(--weight-soft);">🐾</div>
                </div>
                <div class="stat-value">{{ number_format($stats['cats']) }}</div>
            </div>
            <div class="card stat">
                <div class="stat-top">
                    <span class="stat-label">Total Booking</span>
                    <div class="stat-icon" style="background:var(--booking-soft);">📅</div>
                </div>
                <div class="stat-value">{{ number_format($stats['bookings']) }}</div>
            </div>
            <div class="card stat">
                <div class="stat-top">
                    <span class="stat-label">Booking Mendatang</span>
                    <div class="stat-icon" style="background:var(--petshop-soft);">⏳</div>
                </div>
                <div class="stat-value">{{ number_format($stats['upcoming_bookings']) }}</div>
            </div>
        </div>
    </section>

    <section>
        <div class="group-title">Aksi cepat</div>
        <div class="grid g3">
            @foreach ($actions as $a)
                @if ($a['href'])
                    <a href="{{ $a['href'] }}" class="card action"
                        style="--accent:{{ $a['accent'] }}; --accent-soft:{{ $a['soft'] }};">
                        <div class="action-icon">{{ $a['icon'] }}</div>
                        <span class="action-title">{{ $a['title'] }}</span>
                        <span class="action-desc">{{ $a['desc'] }}</span>
                        <span class="action-cta">{{ $a['cta'] }}</span>
                    </a>
                @else
                    <div class="card action is-soon"
                        style="--accent:{{ $a['accent'] }}; --accent-soft:{{ $a['soft'] }};">
                        <div class="action-icon">{{ $a['icon'] }}</div>
                        <span class="action-title">{{ $a['title'] }}</span>
                        <span class="action-desc">{{ $a['desc'] }}</span>
                        <span class="action-cta">Segera hadir</span>
                    </div>
                @endif
            @endforeach
        </div>
    </section>
@endsection