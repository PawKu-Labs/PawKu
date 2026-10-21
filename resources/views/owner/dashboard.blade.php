@extends('owner.layout')
@section('title', 'Dashboard')

@section('content')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

        /* Palet Pawku (selaras dengan AppColors di Flutter) */
        .pawku {
            --primary: #E87D2F;
            --primary-dark: #C9611A;
            --primary-soft: #FCE3CC;
            --bg: #FDF3E7;
            --surface: #FFFFFF;
            --heading: #5E2F20;
            --muted: #8C6B5D;
            --divider: #F0E0D0;
            --dot-inactive: #E9D5C2;

            --warning: #E8A33D;
            --warning-soft: #FCEFD6;
            --info: #3FA9D6;
            --info-soft: #DCF3FC;
            --vaccine: #FF6B8B;
            --vaccine-soft: #FFE1E7;
            --booking: #B185DB;
            --booking-soft: #F0E4FA;
            --petshop: #FFB100;
            --petshop-soft: #FFF3D6;

            font-family: 'Montserrat', system-ui, sans-serif;
            color: var(--heading);
        }

        .pawku .page-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--heading);
            margin: 0 0 4px;
        }

        .pawku .page-sub {
            font-size: 13px;
            color: var(--muted);
            line-height: 1.4;
            margin: 0 0 24px;
        }

        .pawku section { margin-bottom: 28px; }

        .pawku .group-title {
            font-size: 15px;
            font-weight: 600;
            color: var(--heading);
            margin-bottom: 12px;
        }

        .pawku .grid { display: grid; gap: 16px; }
        .pawku .g3 { grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); }
        .pawku .g-actions { grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); }

        .pawku .card {
            background: var(--surface);
            border: 1px solid var(--divider);
            border-radius: 16px;
            box-shadow: none;
        }

        /* Ikon SVG */
        .pawku .ic {
            width: 22px;
            height: 22px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .pawku .ic-lg { width: 28px; height: 28px; }

        /* Kartu statistik */
        .pawku .stat { padding: 20px; }

        .pawku .stat-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
        }

        .pawku .stat-label { font-size: 13px; font-weight: 500; color: var(--muted); }
        .pawku .stat-value { font-size: 30px; font-weight: 700; color: var(--heading); line-height: 1; }

        .pawku .stat-icon,
        .pawku .action-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
        }

        .pawku .stat-icon { width: 42px; height: 42px; }
        .pawku .action-icon { width: 56px; height: 56px; margin: 0 auto 14px; }

        .pawku .i-orange { background: var(--primary-soft); color: var(--primary); }
        .pawku .i-blue { background: var(--info-soft); color: var(--info); }
        .pawku .i-gold { background: var(--warning-soft); color: var(--warning); }
        .pawku .i-pink { background: var(--vaccine-soft); color: var(--vaccine); }
        .pawku .i-purple { background: var(--booking-soft); color: var(--booking); }
        .pawku .i-yellow { background: var(--petshop-soft); color: var(--petshop); }
        .pawku .i-muted { background: #F3EBE4; color: var(--muted); }

        /* Kartu aksi cepat */
        .pawku .action {
            text-align: center;
            padding: 28px 20px;
            text-decoration: none;
            display: block;
            transition: transform .2s, box-shadow .2s, border-color .2s;
        }

        .pawku a.action:hover {
            transform: translateY(-3px);
            border-color: var(--primary);
            box-shadow: 0 8px 20px rgba(232, 125, 47, .12);
        }

        .pawku .action-title { font-weight: 600; font-size: 15px; color: var(--heading); margin: 0 0 4px; }
        .pawku .action-desc { font-size: 12px; color: var(--muted); line-height: 1.4; margin: 0; }

        .pawku .action.is-mitra { border-color: rgba(232, 125, 47, .4); }
        .pawku .action.is-mitra .action-title { color: var(--primary); }

        .pawku .action.is-locked {
            cursor: not-allowed;
            opacity: .65;
            background: #FBF7F3;
        }

        .pawku .action.is-locked .action-title { color: var(--muted); }
    </style>

    <div class="pawku">
        <div>
            <h1 class="page-title">Selamat Datang, {{ $profile['full_name'] ?? ($profile['username'] ?? 'Pemilik Kucing') }}</h1>
            <p class="page-sub">Kelola profil kucing dan pantau jadwal grooming Anda.</p>
        </div>

        <section>
            <div class="group-title">Ringkasan</div>
            <div class="grid g3">
                <div class="card stat">
                    <div class="stat-top">
                        <span class="stat-label">Kucing Saya</span>
                        <div class="stat-icon i-orange">
                            <svg class="ic" viewBox="0 0 24 24" aria-hidden="true">
                                <circle cx="11" cy="4" r="2" />
                                <circle cx="18" cy="8" r="2" />
                                <circle cx="20" cy="16" r="2" />
                                <path d="M9 10a5 5 0 0 1 5 5v3.5a3.5 3.5 0 0 1-6.84 1.045Q6.52 17.48 4.46 16.84A3.5 3.5 0 0 1 5.5 10Z" />
                            </svg>
                        </div>
                    </div>
                    <div class="stat-value">{{ number_format($stats['cats']) }}</div>
                </div>

                <div class="card stat">
                    <div class="stat-top">
                        <span class="stat-label">Total Booking</span>
                        <div class="stat-icon i-blue">
                            <svg class="ic" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M8 2v4" />
                                <path d="M16 2v4" />
                                <rect width="18" height="18" x="3" y="4" rx="2" />
                                <path d="M3 10h18" />
                            </svg>
                        </div>
                    </div>
                    <div class="stat-value">{{ number_format($stats['bookings']) }}</div>
                </div>

                <div class="card stat">
                    <div class="stat-top">
                        <span class="stat-label">Booking Mendatang</span>
                        <div class="stat-icon i-gold">
                            <svg class="ic" viewBox="0 0 24 24" aria-hidden="true">
                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12 6 12 12 16 14" />
                            </svg>
                        </div>
                    </div>
                    <div class="stat-value">{{ number_format($stats['upcoming_bookings']) }}</div>
                </div>
            </div>
        </section>

        <section>
            <div class="group-title">Aksi Cepat</div>
            <div class="grid g-actions">
                <div class="card action">
                    <div class="action-icon i-orange">
                        <svg class="ic ic-lg" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 5c.67 0 1.35.09 2 .26 1.78-2 5.03-2.84 6.42-2.26 1.4.58-.42 3.33-1 4.5.65 1.18 1 2.54 1 4 0 4.97-4 8-9 8s-9-3.03-9-8c0-1.46.35-2.82 1-4-.58-1.17-2.4-3.92-1-4.5 1.39-.58 4.72.23 6.5 2.23A9.04 9.04 0 0 1 12 5Z" />
                            <path d="M8 14v.5" />
                            <path d="M16 14v.5" />
                            <path d="M11.25 16.25h1.5L12 17l-.75-.75Z" />
                        </svg>
                    </div>
                    <p class="action-title">Profil Kucing</p>
                    <p class="action-desc">Lihat profil kucing Anda</p>
                </div>

                <a href="{{ route('owner.vaccine.calculator') }}" class="card action">
                    <div class="action-icon i-pink">
                        <svg class="ic ic-lg" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="m18 2 4 4" />
                            <path d="m17 7 3-3" />
                            <path d="M19 9 8.7 19.3c-1 1-2.5 1-3.4 0l-.6-.6c-1-.9-1-2.5 0-3.4L15 5" />
                            <path d="m9 11 4 4" />
                            <path d="m5 19-3 3" />
                            <path d="m14 4 6 6" />
                        </svg>
                    </div>
                    <p class="action-title">Kalkulator Vaksin</p>
                    <p class="action-desc">Hitung jadwal vaksinasi kucing</p>
                </a>

                <div class="card action">
                    <div class="action-icon i-purple">
                        <svg class="ic ic-lg" viewBox="0 0 24 24" aria-hidden="true">
                            <rect width="8" height="4" x="8" y="2" rx="1" />
                            <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" />
                            <path d="M12 11h4" />
                            <path d="M12 16h4" />
                            <path d="M8 11h.01" />
                            <path d="M8 16h.01" />
                        </svg>
                    </div>
                    <p class="action-title">Riwayat Booking</p>
                    <p class="action-desc">Lihat riwayat booking grooming</p>
                </div>

                @if ($layoutHasMitra ?? false)
                    <a href="{{ route('owner.mitra') }}" class="card action is-mitra">
                        <div class="action-icon i-yellow">
                            <svg class="ic ic-lg" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7" />
                                <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8" />
                                <path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4" />
                                <path d="M2 7h20" />
                                <path d="M22 7v3a2 2 0 0 1-2 2a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 16 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 12 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 8 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 4 12a2 2 0 0 1-2-2V7" />
                            </svg>
                        </div>
                        <p class="action-title">Mitra Saya</p>
                        <p class="action-desc">Kelola petshop &amp; booking masuk</p>
                    </a>
                @else
                    <div class="card action is-locked" onclick="showMitraDisabledInfo()">
                        <div class="action-icon i-muted">
                            <svg class="ic ic-lg" viewBox="0 0 24 24" aria-hidden="true">
                                <rect width="18" height="11" x="3" y="11" rx="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                        </div>
                        <p class="action-title">Dashboard Mitra</p>
                        <p class="action-desc">Terkunci (Belum Terdaftar)</p>
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection