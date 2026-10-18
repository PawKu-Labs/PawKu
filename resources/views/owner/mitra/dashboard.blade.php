@extends('owner.layout')
@section('title', 'Dashboard Kelola Mitra')

@section('content')
<style>
    .mitra-context-bar {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--rl);
        padding: 14px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        box-shadow: var(--shadow);
    }

    .mitra-info-cluster { display: flex; align-items: center; gap: 12px; min-width: 0; }

    .mitra-dot {
        width: 12px; height: 12px; border-radius: 50%; flex-shrink: 0;
        box-shadow: 0 0 0 3px rgba(46, 158, 107, 0.2);
    }

    .mitra-name {
        font-size: 16px; font-weight: 700; color: var(--heading);
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }

    .mitra-type-badge {
        font-size: 11px; font-weight: 700; letter-spacing: 0.3px; padding: 3px 9px;
        border-radius: 6px; display: inline-flex; align-items: center; gap: 5px;
    }

    .type-paw-health { background: #e0f2fe; color: #0369a1; }
    .type-paw-grooming { background: #f3e8ff; color: #7e22ce; }
    .type-paw-hotel { background: #fef3c7; color: #b45309; }
    .type-paw-shop { background: #dcfce7; color: #15803d; }

    .pill.p-serving { background: #dbeafe; color: #1d4ed8; }

    .flash {
        padding: 12px 16px; border-radius: var(--r); font-size: 13.5px; font-weight: 600; margin-top: 14px;
    }
    .flash-ok { background: #ecfdf5; border: 1px solid #a7f3d0; color: #047857; }

    .btn-switch-mitra {
        display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px;
        background: #f8fafc; border: 1px solid var(--border); color: var(--primary);
        font-size: 12.5px; font-weight: 600; border-radius: var(--r); cursor: pointer; transition: .2s;
    }
    .btn-switch-mitra:hover { background: var(--primary-light); border-color: var(--primary); }

    .floating-nav-wrapper { position: sticky; top: calc(var(--header) + 12px); z-index: 40; margin: 6px 0 16px; }

    .mitra-floating-nav {
        background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px);
        border: 1px solid rgba(226, 232, 240, 0.9); border-radius: 28px; padding: 6px;
        display: flex; gap: 6px; box-shadow: 0 8px 24px -4px rgba(0, 0, 0, 0.08); max-width: 640px;
    }

    .nav-tab-btn {
        flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px;
        height: 44px; padding: 0 16px; border: none; background: transparent; color: var(--muted);
        font-size: 13px; font-weight: 600; border-radius: 22px; cursor: pointer;
        transition: all 0.24s cubic-bezier(0.2, 0, 0, 1); user-select: none;
    }
    .nav-tab-btn:hover { color: var(--heading); background: #f1f5f9; }
    .nav-tab-btn.active { background: var(--primary); color: #fff; box-shadow: 0 4px 12px rgba(46, 158, 107, 0.28); }
    .nav-tab-btn svg { width: 18px; height: 18px; flex-shrink: 0; }
    .nav-tab-btn .tab-badge { font-size: 10px; font-weight: 700; padding: 2px 6px; border-radius: 10px; background: rgba(0, 0, 0, 0.06); }
    .nav-tab-btn.active .tab-badge { background: rgba(255, 255, 255, 0.25); color: #fff; }

    .tab-panel { display: none; animation: fadeInTab .25s ease-out; }
    .tab-panel.active { display: flex; flex-direction: column; gap: 24px; }

    @keyframes fadeInTab {
        from { opacity: 0; transform: translateY(6px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .action-card {
        background: #fff; border: 1px solid var(--border); border-radius: var(--rl); padding: 20px;
        cursor: pointer; transition: all .2s ease; display: flex; align-items: center; gap: 16px; text-align: left;
    }
    .action-card:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06); border-color: #cbd5e1; }

    .action-icon {
        width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center;
        justify-content: center; font-size: 22px; flex-shrink: 0;
    }

    .booking-card {
        background: #fff; border: 1px solid var(--border); border-radius: var(--rl); padding: 18px 20px;
        display: flex; flex-direction: column; gap: 14px; transition: .2s;
    }
    .booking-card:hover { border-color: #cbd5e1; }

    .booking-header {
        display: flex; align-items: center; justify-content: space-between; gap: 12px;
        border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;
    }

    .queue-pill {
        background: var(--primary-light); color: var(--primary); font-weight: 700; font-size: 13px;
        padding: 4px 10px; border-radius: 8px; letter-spacing: 0.5px;
    }

    .booking-body { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; }

    .booking-meta-label {
        font-size: 11px; color: var(--muted); text-transform: uppercase; font-weight: 700;
        letter-spacing: 0.5px; margin-bottom: 3px;
    }
    .booking-meta-val { font-size: 13.5px; font-weight: 600; color: var(--heading); }

    .booking-actions {
        display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
        border-top: 1px solid #f1f5f9; padding-top: 12px;
    }

    .btn-action-sm {
        padding: 6px 12px; font-size: 12px; font-weight: 600; border-radius: 6px;
        border: 1px solid transparent; cursor: pointer; transition: .15s; text-decoration: none;
        display: inline-flex; align-items: center; gap: 5px;
    }
    .btn-confirm { background: #ecfdf5; color: #047857; border-color: #a7f3d0; }
    .btn-confirm:hover { background: #d1fae5; }
    .btn-serve { background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; }
    .btn-serve:hover { background: #dbeafe; }
    .btn-finish { background: #f5f3ff; color: #6d28d9; border-color: #ddd6fe; }
    .btn-finish:hover { background: #ede9fe; }
    .btn-reject { background: #fff1f2; color: #be123c; border-color: #fecdd3; }
    .btn-reject:hover { background: #ffe4e6; }

    .filter-group { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 14px; }
    .filter-btn {
        padding: 6px 14px; font-size: 12.5px; font-weight: 600; border-radius: 20px;
        border: 1px solid var(--border); background: #fff; color: var(--body); cursor: pointer; transition: .2s;
    }
    .filter-btn.active { background: var(--heading); color: #fff; border-color: var(--heading); }

    .modal-overlay {
        position: fixed; inset: 0; background: rgba(15, 23, 42, 0.45); backdrop-filter: blur(4px);
        z-index: 999; display: none; align-items: center; justify-content: center; padding: 20px;
    }
    .modal-overlay.open { display: flex; }
    .modal-box {
        background: #fff; border-radius: var(--rl); width: 100%; max-width: 480px; max-height: 85vh;
        overflow-y: auto; padding: 24px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }
</style>

@if (!$activeMitra)
    <div class="card" style="text-align: center; padding: 60px 24px; max-width: 680px; margin: 40px auto;">
        <div style="width: 72px; height: 72px; margin: 0 auto 20px; border-radius: 20px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 36px;">
            🏪
        </div>
        <h2 style="font-size: 22px; font-weight: 700; color: var(--heading); margin-bottom: 8px;">
            Fitur Kemitraan PawKu
        </h2>
        <p style="font-size: 14px; color: var(--body); line-height: 1.6; margin-bottom: 24px;">
            Akun Anda saat ini belum memiliki unit mitra yang terdaftar. Fitur dashboard mitra diperuntukkan bagi pemilik kucing yang memiliki unit usaha petshop, klinik hewan, hotel kucing, atau salon grooming yang telah diverifikasi oleh admin.
        </p>

        <div style="background: var(--bg); border: 1px solid var(--border); border-radius: var(--r); padding: 16px 20px; text-align: left; margin-bottom: 24px;">
            <div style="font-weight: 700; font-size: 13px; color: var(--heading); margin-bottom: 6px;">Cara Menjadi Mitra PawKu:</div>
            <ul style="font-size: 13px; color: var(--body); padding-left: 20px; line-height: 1.6;">
                <li>Ajukan permohonan pendaftaran mitra kepada administrator PawKu.</li>
                <li>Admin akan meninjau kelayakan dan menerbitkan persetujuan.</li>
                <li>Setelah disetujui, dashboard mitra akan otomatis aktif dan dapat dikelola langsung dari menu ini.</li>
            </ul>
        </div>

        <a href="{{ route('owner.dashboard') }}" class="btn-logout" style="background: var(--primary); color: #fff; border: none; text-decoration: none; padding: 10px 24px; border-radius: var(--r); display: inline-block;">
            Kembali ke Dashboard Utama
        </a>
    </div>
@else
@php
    $typeMeta = [
        'PAW_HEALTH' => ['label' => 'Paw Health (Klinik Hewan)', 'class' => 'type-paw-health', 'dot' => '#0284c7', 'icon' => '🏥'],
        'PAW_GROOMING' => ['label' => 'Paw Grooming (Salon Kucing)', 'class' => 'type-paw-grooming', 'dot' => '#9333ea', 'icon' => '✂️'],
        'PAW_HOTEL' => ['label' => 'Paw Hotel (Penitipan)', 'class' => 'type-paw-hotel', 'dot' => '#d97706', 'icon' => '🏨'],
        'PAW_SHOP' => ['label' => 'Paw Shop (Toko Petshop)', 'class' => 'type-paw-shop', 'dot' => '#16a34a', 'icon' => '🏪'],
    ];
    $metaOf = fn($m) => $typeMeta[strtoupper($m->mitra_type ?? 'PAW_SHOP')] ?? $typeMeta['PAW_SHOP'];
    $currentType = $metaOf($activeMitra);

    $statusLabel = ['approved' => 'Disetujui', 'pending' => 'Menunggu', 'rejected' => 'Ditolak'];
    $statusClass = ['approved' => 'p-approved', 'pending' => 'p-pending', 'rejected' => 'p-rejected'];

    $bStatusMap = [
        'menunggu' => ['Menunggu', 'p-pending'],
        'dikonfirmasi' => ['Dikonfirmasi', 'p-approved'],
        'dilayani' => ['Sedang Dilayani', 'p-serving'],
        'selesai' => ['Selesai', 'p-approved'],
        'ditolak' => ['Ditolak', 'p-rejected'],
        'dibatalkan' => ['Dibatalkan', 'p-rejected'],
    ];

    // [status tujuan, class tombol, label, teks konfirmasi]
    $actionMap = [
        'menunggu' => [
            ['dikonfirmasi', 'btn-confirm', '✓ Konfirmasi', null],
            ['ditolak', 'btn-reject', '✕ Tolak', 'Tolak booking ini?'],
        ],
        'dikonfirmasi' => [
            ['dilayani', 'btn-serve', '🐾 Mulai Layani', null],
            ['selesai', 'btn-finish', '✓ Selesaikan', null],
        ],
        'dilayani' => [
            ['selesai', 'btn-finish', '✓ Selesaikan Layanan', null],
        ],
    ];

    $bkDate = fn($bk) => $bk->slot_date
        ?? \Carbon\Carbon::parse($bk->created_at)->timezone('Asia/Jakarta')->toDateString();

    // Nomor lokal 08xx -> format internasional 628xx untuk wa.me
    $waNumber = function (?string $phone) {
        $d = preg_replace('/\D/', '', (string) $phone);
        if ($d !== '' && str_starts_with($d, '0')) {
            $d = '62' . substr($d, 1);
        }

        return $d;
    };

    $tabs = ['beranda', 'booking', 'layanan', 'profil'];
@endphp

@if (session('success'))
    <div class="flash flash-ok">✅ {{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="alert alert-err" style="margin-top: 14px;">⚠️ {{ session('error') }}</div>
@endif

{{-- Context Bar --}}
<div class="mitra-context-bar" style="margin-top: 14px;">
    <div class="mitra-info-cluster">
        <div class="mitra-dot" style="background-color: {{ $currentType['dot'] }};"></div>
        <div>
            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                <span class="mitra-name">{{ $activeMitra->name }}</span>
                <span class="mitra-type-badge {{ $currentType['class'] }}">
                    {{ $currentType['icon'] }} {{ $currentType['label'] }}
                </span>
                <span class="pill p-approved" style="font-size: 10.5px;">Disetujui</span>
            </div>
            @if (!empty($activeMitra->address))
                <div style="font-size: 12px; color: var(--muted); margin-top: 2px;">📍 {{ $activeMitra->address }}</div>
            @endif
        </div>
    </div>

    @if ($userMitras->count() > 1)
        <button type="button" class="btn-switch-mitra" onclick="openMitraSwitcher()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px;">
                <path d="m16 3 4 4-4 4" /><path d="M20 7H4" /><path d="m8 21-4-4 4-4" /><path d="M4 17h16" />
            </svg>
            <span>Ganti Mitra ({{ $userMitras->count() }})</span>
        </button>
    @endif
</div>

{{-- Floating Nav --}}
<div class="floating-nav-wrapper">
    <div class="mitra-floating-nav">
        <button type="button" data-tab="beranda" class="nav-tab-btn {{ $currentTab === 'beranda' ? 'active' : '' }}" onclick="switchMitraTab('beranda')">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7" /><rect x="14" y="3" width="7" height="7" />
                <rect x="14" y="14" width="7" height="7" /><rect x="3" y="14" width="7" height="7" />
            </svg>
            <span>Beranda</span>
        </button>
        <button type="button" data-tab="booking" class="nav-tab-btn {{ $currentTab === 'booking' ? 'active' : '' }}" onclick="switchMitraTab('booking')">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="22 12 16 12 14 15 10 15 8 12 2 12" />
                <path d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z" />
            </svg>
            <span>Booking</span>
            @if ($stats['pending_bookings'] > 0)
                <span class="tab-badge">{{ $stats['pending_bookings'] }}</span>
            @endif
        </button>
        <button type="button" data-tab="layanan" class="nav-tab-btn {{ $currentTab === 'layanan' ? 'active' : '' }}" onclick="switchMitraTab('layanan')">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="6" cy="6" r="3" /><circle cx="6" cy="18" r="3" />
                <line x1="20" y1="4" x2="8.12" y2="15.88" /><line x1="14.47" y1="14.48" x2="20" y2="20" />
                <line x1="8.12" y1="8.12" x2="12" y2="12" />
            </svg>
            <span>Layanan</span>
            <span class="tab-badge">{{ $stats['services_count'] }}</span>
        </button>
        <button type="button" data-tab="profil" class="nav-tab-btn {{ $currentTab === 'profil' ? 'active' : '' }}" onclick="switchMitraTab('profil')">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" /><circle cx="12" cy="7" r="4" />
            </svg>
            <span>Profil</span>
        </button>
    </div>
</div>

{{-- TAB 1: BERANDA --}}
<div id="panel-beranda" class="tab-panel {{ $currentTab === 'beranda' ? 'active' : '' }}">
    <section>
        <div class="group-title">Ringkasan Operasional</div>
        <div class="grid g3">
            <div class="card stat">
                <div class="stat-top"><span class="stat-label">Total Booking Masuk</span><div class="stat-icon i-blue">📅</div></div>
                <div class="stat-value">{{ number_format($stats['total_bookings']) }}</div>
            </div>
            <div class="card stat">
                <div class="stat-top"><span class="stat-label">Booking Hari Ini</span><div class="stat-icon i-gold">📋</div></div>
                <div class="stat-value">{{ number_format($stats['today_bookings']) }}</div>
            </div>
            <div class="card stat">
                <div class="stat-top"><span class="stat-label">Perlu Konfirmasi Segera</span><div class="stat-icon i-red">⏳</div></div>
                <div class="stat-value">{{ number_format($stats['pending_bookings']) }}</div>
            </div>
            <div class="card stat">
                <div class="stat-top"><span class="stat-label">Katalog Layanan Aktif</span><div class="stat-icon i-green">🛁</div></div>
                <div class="stat-value">{{ number_format($stats['services_count']) }}</div>
            </div>
            <div class="card stat">
                <div class="stat-top"><span class="stat-label">Sedang Dilayani</span><div class="stat-icon i-purple">🐾</div></div>
                <div class="stat-value">{{ number_format($stats['serving_bookings']) }}</div>
            </div>
            <div class="card stat">
                <div class="stat-top"><span class="stat-label">Rating &amp; Ulasan</span><div class="stat-icon i-gold">⭐</div></div>
                <div class="stat-value" style="font-size: 24px;">
                    @if ($stats['total_reviews'] > 0)
                        {{ $stats['avg_rating'] }}
                        <span style="font-size: 13px; font-weight: 500; color: var(--muted);">({{ $stats['total_reviews'] }} ulasan)</span>
                    @else
                        <span style="font-size: 16px; font-weight: 600; color: var(--muted);">-</span>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section>
        <div class="group-title">Aksi Cepat</div>
        <div class="grid g3">
            <div class="action-card" onclick="switchMitraTab('booking')">
                <div class="action-icon" style="background: #e0f2fe; color: #0284c7;">📥</div>
                <div>
                    <div style="font-weight: 700; color: var(--heading); font-size: 14.5px;">Buka Booking Masuk</div>
                    <div style="font-size: 12px; color: var(--muted); margin-top: 3px;">Lihat antrean dan respon reservasi pelanggan</div>
                </div>
            </div>
            <div class="action-card" onclick="switchMitraTab('layanan')">
                <div class="action-icon" style="background: #dcfce7; color: #16a34a;">✂️</div>
                <div>
                    <div style="font-weight: 700; color: var(--heading); font-size: 14.5px;">Kelola Layanan</div>
                    <div style="font-size: 12px; color: var(--muted); margin-top: 3px;">Periksa daftar layanan, tarif harga &amp; kuota</div>
                </div>
            </div>
            <div class="action-card" onclick="switchMitraTab('profil')">
                <div class="action-icon" style="background: #f3e8ff; color: #9333ea;">🏪</div>
                <div>
                    <div style="font-weight: 700; color: var(--heading); font-size: 14.5px;">Profil &amp; Jam Buka</div>
                    <div style="font-size: 12px; color: var(--muted); margin-top: 3px;">Lihat jam operasional dan ketentuan petshop</div>
                </div>
            </div>
        </div>
    </section>

    <section>
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
            <div class="group-title" style="margin-bottom: 0;">Booking Terbaru</div>
            <button type="button" onclick="switchMitraTab('booking')" style="background: none; border: none; color: var(--primary); font-size: 12.5px; font-weight: 600; cursor: pointer;">
                Lihat Semua ({{ $bookings->count() }}) &rarr;
            </button>
        </div>

        @if ($bookings->isEmpty())
            <div class="card" style="text-align: center; padding: 36px 20px; color: var(--muted);">
                <div style="font-size: 32px; margin-bottom: 8px;">📭</div>
                <p style="font-weight: 600;">Belum ada booking masuk untuk unit mitra ini.</p>
            </div>
        @else
            <div style="display: flex; flex-direction: column; gap: 12px;">
                @foreach ($bookings->take(3) as $bk)
                    @php [$bsLabel, $bsClass] = $bStatusMap[$bk->status] ?? ['Menunggu', 'p-pending']; @endphp
                    <div class="booking-card">
                        <div class="booking-header">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span class="queue-pill">#{{ str_pad($bk->queue_number ?? $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                <div>
                                    <div style="font-weight: 700; font-size: 14px; color: var(--heading);">{{ $bk->cat_name ?? 'Kucing Pelanggan' }}</div>
                                    <div style="font-size: 12px; color: var(--muted);">Pemilik: {{ $bk->customer_name ?? $bk->customer_username ?? 'Pelanggan' }}</div>
                                </div>
                            </div>
                            <span class="pill {{ $bsClass }}">{{ $bsLabel }}</span>
                        </div>
                        <div class="booking-body">
                            <div>
                                <div class="booking-meta-label">Layanan</div>
                                <div class="booking-meta-val">{{ $bk->service_name ?? 'Layanan Mitra' }}</div>
                            </div>
                            <div>
                                <div class="booking-meta-label">Jadwal / Waktu</div>
                                <div class="booking-meta-val">
                                    {{ $bkDate($bk) }}
                                    @if (!empty($bk->slot_start_time))
                                        • {{ substr($bk->slot_start_time, 0, 5) }}
                                    @endif
                                </div>
                            </div>
                            <div>
                                <div class="booking-meta-label">Total Biaya</div>
                                <div class="booking-meta-val" style="color: var(--primary);">
                                    Rp {{ number_format((float) ($bk->service_price ?? 0), 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
</div>

{{-- TAB 2: BOOKING --}}
<div id="panel-booking" class="tab-panel {{ $currentTab === 'booking' ? 'active' : '' }}">
    <section>
        <div style="margin-bottom: 14px;">
            <h2 style="font-size: 18px; font-weight: 700; color: var(--heading);">Kelola Reservasi &amp; Antrean Booking</h2>
            <p style="font-size: 13px; color: var(--muted);">Daftar seluruh pesanan dan antrean masuk dari pemilik kucing.</p>
        </div>

        <div class="filter-group">
            <button type="button" class="filter-btn active" onclick="filterBookings('all', this)">Semua ({{ $bookings->count() }})</button>
            <button type="button" class="filter-btn" onclick="filterBookings('menunggu', this)">Menunggu ({{ $bookings->where('status', 'menunggu')->count() }})</button>
            <button type="button" class="filter-btn" onclick="filterBookings('dikonfirmasi', this)">Dikonfirmasi ({{ $bookings->where('status', 'dikonfirmasi')->count() }})</button>
            <button type="button" class="filter-btn" onclick="filterBookings('dilayani', this)">Sedang Dilayani ({{ $bookings->where('status', 'dilayani')->count() }})</button>
            <button type="button" class="filter-btn" onclick="filterBookings('selesai', this)">Selesai ({{ $bookings->where('status', 'selesai')->count() }})</button>
            <button type="button" class="filter-btn" onclick="filterBookings('batal_tolak', this)">Ditolak / Batal ({{ $bookings->whereIn('status', ['ditolak', 'dibatalkan'])->count() }})</button>
        </div>

        @if ($bookings->isEmpty())
            <div class="card" style="text-align: center; padding: 48px 20px; color: var(--muted);">
                <div style="font-size: 40px; margin-bottom: 10px;">📭</div>
                <h3 style="font-size: 16px; font-weight: 700; color: var(--heading); margin-bottom: 4px;">Belum Ada Booking</h3>
                <p style="font-size: 13px;">Belum ada pesanan masuk untuk petshop ini.</p>
            </div>
        @else
            <div id="booking-items-container" style="display: flex; flex-direction: column; gap: 14px;">
                @foreach ($bookings as $bk)
                    @php
                        [$bsLabel, $bsClass] = $bStatusMap[$bk->status] ?? ['Menunggu', 'p-pending'];
                        $wa = $waNumber($bk->customer_phone ?? null);
                    @endphp
                    <div class="booking-card booking-item-row" data-status="{{ $bk->status }}">
                        <div class="booking-header">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <span class="queue-pill">#{{ str_pad($bk->queue_number ?? $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                <div>
                                    <div style="font-weight: 700; font-size: 15px; color: var(--heading);">
                                        {{ $bk->cat_name ?? 'Kucing Pelanggan' }}
                                        @if (!empty($bk->cat_breed))
                                            <span style="font-size: 12px; font-weight: 500; color: var(--muted);">({{ $bk->cat_breed }})</span>
                                        @endif
                                    </div>
                                    <div style="font-size: 12.5px; color: var(--body); margin-top: 2px;">
                                        Pemilik: <strong>{{ $bk->customer_name ?? $bk->customer_username ?? 'Pelanggan' }}</strong>
                                        @if ($wa !== '')
                                            • <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener" style="color: var(--primary); text-decoration: none;">📱 {{ $bk->customer_phone }}</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <span class="pill {{ $bsClass }}">{{ $bsLabel }}</span>
                        </div>

                        <div class="booking-body">
                            <div>
                                <div class="booking-meta-label">Layanan Dipesan</div>
                                <div class="booking-meta-val">{{ $bk->service_name ?? 'Layanan' }}</div>
                                <div style="font-size: 12px; color: var(--primary); font-weight: 700; margin-top: 2px;">
                                    Rp {{ number_format((float) ($bk->service_price ?? 0), 0, ',', '.') }}
                                </div>
                            </div>
                            <div>
                                <div class="booking-meta-label">Waktu Booking / Slot</div>
                                <div class="booking-meta-val">{{ $bkDate($bk) }}</div>
                                @if (!empty($bk->slot_start_time))
                                    <div style="font-size: 12px; color: var(--muted); margin-top: 2px;">
                                        ⏰ {{ substr($bk->slot_start_time, 0, 5) }} - {{ substr($bk->slot_end_time ?? '', 0, 5) }}
                                    </div>
                                @elseif (!empty($bk->check_in_at))
                                    <div style="font-size: 12px; color: var(--muted); margin-top: 2px;">
                                        Check-in: {{ \Carbon\Carbon::parse($bk->check_in_at)->timezone('Asia/Jakarta')->format('d M H:i') }}
                                    </div>
                                @endif
                            </div>
                            @if (!empty($bk->notes))
                                <div style="grid-column: 1 / -1;">
                                    <div class="booking-meta-label">Catatan Pelanggan</div>
                                    <div style="font-size: 13px; color: var(--body); background: var(--bg); padding: 8px 12px; border-radius: 6px;">
                                        "{{ $bk->notes }}"
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="booking-actions">
                            @if (isset($actionMap[$bk->status]))
                                <span style="font-size: 12px; color: var(--muted); font-weight: 600; margin-right: 4px;">Ubah Status:</span>
                                @foreach ($actionMap[$bk->status] as [$toStatus, $btnClass, $btnLabel, $confirmText])
                                    <form action="{{ route('owner.mitra.booking.status', ['mitra' => $mitraId, 'booking' => $bk->id]) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <input type="hidden" name="status" value="{{ $toStatus }}">
                                        <button type="submit" class="btn-action-sm {{ $btnClass }}"
                                            @if ($confirmText) onclick="return confirm('{{ e($confirmText) }}')" @endif>
                                            {{ $btnLabel }}
                                        </button>
                                    </form>
                                @endforeach
                            @else
                                <span style="font-size: 12px; color: var(--muted); font-style: italic;">Booking telah {{ strtolower($bsLabel) }}.</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
</div>

{{-- TAB 3: LAYANAN --}}
<div id="panel-layanan" class="tab-panel {{ $currentTab === 'layanan' ? 'active' : '' }}">
    <section>
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; flex-wrap: wrap; gap: 10px;">
            <div>
                <h2 style="font-size: 18px; font-weight: 700; color: var(--heading);">Katalog Layanan Petshop</h2>
                <p style="font-size: 13px; color: var(--muted);">Daftar layanan yang disediakan oleh {{ $activeMitra->name }}.</p>
            </div>
            <div style="font-size: 12.5px; font-weight: 600; color: var(--muted);">
                Total: <strong style="color: var(--heading);">{{ $services->count() }} Layanan</strong>
            </div>
        </div>

        @if ($services->isEmpty())
            <div class="card" style="text-align: center; padding: 48px 20px; color: var(--muted);">
                <div style="font-size: 40px; margin-bottom: 10px;">✂️</div>
                <h3 style="font-size: 16px; font-weight: 700; color: var(--heading); margin-bottom: 4px;">Belum Ada Layanan Terdaftar</h3>
                <p style="font-size: 13px;">Belum ada item layanan yang ditambahkan pada petshop ini.</p>
            </div>
        @else
            <div class="grid g3">
                @foreach ($services as $svc)
                    <div class="card" style="display: flex; flex-direction: column; justify-content: space-between; gap: 14px;">
                        <div>
                            <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 8px;">
                                <h3 style="font-size: 15.5px; font-weight: 700; color: var(--heading);">{{ $svc->name }}</h3>
                                @if ($svc->is_active ?? true)
                                    <span class="pill p-approved" style="font-size: 10px;">Aktif</span>
                                @else
                                    <span class="pill p-rejected" style="font-size: 10px;">Nonaktif</span>
                                @endif
                            </div>
                            <div style="font-size: 19px; font-weight: 800; color: var(--primary); margin: 10px 0 6px;">
                                Rp {{ number_format((float) ($svc->price ?? 0), 0, ',', '.') }}
                            </div>
                            @if (!empty($svc->duration_minutes))
                                <div style="font-size: 12.5px; color: var(--muted);">⏱️ Durasi: <strong>{{ $svc->duration_minutes }} menit</strong></div>
                            @endif
                            @if (!empty($svc->capacity))
                                <div style="font-size: 12.5px; color: var(--muted); margin-top: 2px;">👥 Kuota: <strong>{{ $svc->capacity }} kucing / slot</strong></div>
                            @endif
                            @if (!empty($svc->room_class))
                                <div style="font-size: 12.5px; color: var(--muted); margin-top: 2px;">🏠 Kelas: <strong>{{ $svc->room_class }}</strong></div>
                            @endif
                        </div>
                        <div style="font-size: 11px; color: var(--muted); border-top: 1px solid #f1f5f9; padding-top: 8px;">
                            ID Layanan: {{ substr((string) $svc->id, 0, 8) }}...
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
</div>

{{-- TAB 4: PROFIL --}}
<div id="panel-profil" class="tab-panel {{ $currentTab === 'profil' ? 'active' : '' }}">
    <section>
        <div class="grid g2">
            <div class="card" style="display: flex; flex-direction: column; gap: 16px;">
                <div style="display: flex; align-items: center; gap: 14px;">
                    <div style="width: 56px; height: 56px; border-radius: 16px; background: var(--bg); border: 1px solid var(--border); overflow: hidden; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0;">
                        @if (!empty($activeMitra->logo_url))
                            <img src="{{ $activeMitra->logo_url }}" alt="{{ $activeMitra->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                        @elseif (!empty($activeMitra->photo_url))
                            <img src="{{ $activeMitra->photo_url }}" alt="{{ $activeMitra->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            {{ $currentType['icon'] }}
                        @endif
                    </div>
                    <div>
                        <h3 style="font-size: 18px; font-weight: 700; color: var(--heading);">{{ $activeMitra->name }}</h3>
                        <span class="mitra-type-badge {{ $currentType['class'] }}" style="margin-top: 4px;">
                            {{ $currentType['icon'] }} {{ $currentType['label'] }}
                        </span>
                    </div>
                </div>

                <div style="border-top: 1px solid #f1f5f9; padding-top: 14px; display: flex; flex-direction: column; gap: 10px; font-size: 13.5px;">
                    <div>
                        <span style="color: var(--muted); display: block; font-size: 11.5px; font-weight: 600; text-transform: uppercase;">Alamat Lengkap</span>
                        <span style="color: var(--heading); font-weight: 500;">{{ $activeMitra->address ?? '-' }}</span>
                    </div>
                    <div>
                        <span style="color: var(--muted); display: block; font-size: 11.5px; font-weight: 600; text-transform: uppercase;">Nomor Telepon</span>
                        <span style="color: var(--heading); font-weight: 500;">{{ $activeMitra->phone_number ?? '-' }}</span>
                    </div>
                    <div>
                        <span style="color: var(--muted); display: block; font-size: 11.5px; font-weight: 600; text-transform: uppercase;">Deskripsi</span>
                        <span style="color: var(--body);">{{ $activeMitra->description ?? 'Tidak ada deskripsi.' }}</span>
                    </div>
                    @if (!empty($activeMitra->rules_text))
                        <div>
                            <span style="color: var(--muted); display: block; font-size: 11.5px; font-weight: 600; text-transform: uppercase;">Ketentuan / Aturan Petshop</span>
                            <div style="background: var(--bg); padding: 10px 14px; border-radius: var(--r); margin-top: 4px; color: var(--heading); font-size: 13px;">
                                {{ $activeMitra->rules_text }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;">
                    <h3 style="font-size: 16px; font-weight: 700; color: var(--heading);">Jam Operasional</h3>
                    <span style="font-size: 11.5px; color: var(--muted);">Jadwal Mingguan</span>
                </div>

                @php
                    $dayNames = [0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'];
                @endphp

                @if ($operatingHours->isEmpty())
                    <div style="text-align: center; padding: 24px 10px; color: var(--muted); font-size: 13px;">
                        Jadwal operasional belum diatur.
                    </div>
                @else
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        @foreach ($dayNames as $dayIndex => $dayLabel)
                            @php $oh = $operatingHours->first(fn($h) => (int) $h->day_of_week === $dayIndex); @endphp
                            <div style="display: flex; align-items: center; justify-content: space-between; padding: 8px 12px; border-radius: 8px; background: var(--bg); font-size: 13px;">
                                <span style="font-weight: 600; color: var(--heading);">{{ $dayLabel }}</span>
                                @if (!$oh)
                                    <span style="color: var(--muted);">-</span>
                                @elseif (!empty($oh->is_closed))
                                    <span class="pill p-rejected" style="font-size: 10.5px;">Tutup (Libur)</span>
                                @elseif (!empty($oh->is_24h))
                                    <span class="pill p-approved" style="font-size: 10.5px;">Buka 24 Jam</span>
                                @else
                                    <span style="font-weight: 600; color: var(--primary);">
                                        {{ substr((string) $oh->open_time, 0, 5) }} - {{ substr((string) $oh->close_time, 0, 5) }}
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>

{{-- MODAL GANTI MITRA --}}
@if ($userMitras->count() > 1)
    <div id="mitra-switcher-modal" class="modal-overlay" onclick="closeMitraSwitcher()">
        <div class="modal-box" onclick="event.stopPropagation()">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                <div>
                    <h3 style="font-size: 17px; font-weight: 700; color: var(--heading);">Pilih Unit Mitra</h3>
                    <p style="font-size: 12.5px; color: var(--muted);">Pilih mitra yang ingin Anda kelola saat ini.</p>
                </div>
                <button type="button" onclick="closeMitraSwitcher()" style="background: none; border: none; font-size: 20px; color: var(--muted); cursor: pointer;">✕</button>
            </div>

            <div style="display: flex; flex-direction: column; gap: 10px;">
                @foreach ($userMitras as $mItem)
                    @php
    $mMeta = $metaOf($mItem);
    $isActiveItem = ((string) $mItem->id === (string) $activeMitra->id);
    $mHref = \App\Support\MitraUrl::for($mItem) ?? '#';
@endphp
                    <a href="{{ $mHref }}"
                       style="display: flex; align-items: center; justify-content: space-between; padding: 14px; border-radius: var(--r); border: 2px solid {{ $isActiveItem ? 'var(--primary)' : 'var(--border)' }}; background: {{ $isActiveItem ? 'var(--primary-light)' : '#fff' }}; text-decoration: none; transition: .2s;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 10px; height: 10px; border-radius: 50%; background: {{ $mMeta['dot'] }};"></div>
                            <div>
                                <div style="font-weight: 700; font-size: 14px; color: var(--heading);">
                                    {{ $mItem->name }}
                                    @if ($isActiveItem)
                                        <span style="font-size: 11px; color: var(--primary); font-weight: 600;">(Sedang Aktif)</span>
                                    @endif
                                </div>
                                <span class="mitra-type-badge {{ $mMeta['class'] }}" style="margin-top: 4px; font-size: 10px;">
                                    {{ $mMeta['icon'] }} {{ $mMeta['label'] }}
                                </span>
                            </div>
                        </div>
                        <span class="pill {{ $statusClass[$mItem->status] ?? 'p-pending' }}" style="font-size: 10px;">
                            {{ $statusLabel[$mItem->status] ?? 'Menunggu' }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@endif

{{-- Script ditaruh langsung di sini (bukan @push) supaya tetap jalan walau layout tidak punya @stack('scripts') --}}
<script>
    function switchMitraTab(tabName) {
        document.querySelectorAll('.tab-panel').forEach(function (p) { p.classList.remove('active'); });
        document.querySelectorAll('.nav-tab-btn').forEach(function (b) {
            b.classList.toggle('active', b.dataset.tab === tabName);
        });

        var panel = document.getElementById('panel-' + tabName);
        if (panel) panel.classList.add('active');

        var url = new URL(window.location);
        url.searchParams.set('tab', tabName);
        window.history.replaceState({}, '', url);
    }

    function openMitraSwitcher() {
        var modal = document.getElementById('mitra-switcher-modal');
        if (modal) modal.classList.add('open');
    }

    function closeMitraSwitcher() {
        var modal = document.getElementById('mitra-switcher-modal');
        if (modal) modal.classList.remove('open');
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeMitraSwitcher();
    });

    function filterBookings(status, btn) {
        document.querySelectorAll('.filter-btn').forEach(function (b) { b.classList.remove('active'); });
        btn.classList.add('active');

        document.querySelectorAll('.booking-item-row').forEach(function (row) {
            var s = row.getAttribute('data-status');
            var show = status === 'all'
                || (status === 'batal_tolak' && (s === 'ditolak' || s === 'dibatalkan'))
                || s === status;
            row.style.display = show ? 'flex' : 'none';
        });
    }
</script>
@endif
@endsection