@extends('owner.layout')
@section('title', 'Dashboard')

@push('styles')
    <style>
        .dash-section {
            font-size: 14px;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            color: var(--heading);
            margin: 26px 0 12px;
        }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 16px;
        }

        .stat-card {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 22px 24px;
            border-left: 4px solid var(--accent);
        }

        .stat-label {
            font-size: 13.5px;
            color: var(--muted);
        }

        .stat-value {
            font-size: 34px;
            font-weight: 800;
            color: var(--heading);
            margin-top: 14px;
            line-height: 1;
        }

        .stat-ico {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            background: var(--accent);
            box-shadow: 0 6px 14px color-mix(in srgb, var(--accent) 35%, transparent);
        }

        .quick-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
        }

        .quick-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 6px;
            padding: 26px 16px;
            text-decoration: none;
            color: inherit;
            transition: transform .2s, box-shadow .2s, border-color .2s;
        }

        .quick-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 24px rgba(0, 0, 0, .08);
            border-color: var(--accent);
        }

        .quick-ico {
            font-size: 38px;
            line-height: 1;
            margin-bottom: 6px;
        }

        .quick-title {
            font-weight: 700;
            font-size: 16px;
            color: var(--heading);
        }

        .quick-card:hover .quick-title {
            color: var(--accent);
        }

        .quick-desc {
            font-size: 12.5px;
            color: var(--muted);
        }
    </style>
@endpush

@section('content')
    @php
        $displayName = $profile['full_name'] ?? ($profile['username'] ?? ($user['email'] ?? 'Pengguna'));
    @endphp

    <h1 class="page-title">Selamat Datang, {{ $displayName }}! 😺</h1>
    <p class="page-sub">Kelola profil kucing dan pantau jadwal grooming Anda.</p>

    <div class="dash-section">Ringkasan</div>
    <div class="stat-grid">
        <div class="card stat-card" style="--accent:#2E9E6B;">
            <div>
                <div class="stat-label">Kucing Saya</div>
                <div class="stat-value">{{ $stats['cats'] }}</div>
            </div>
            <div class="stat-ico">🐾</div>
        </div>

        <div class="card stat-card" style="--accent:#F59E0B;">
            <div>
                <div class="stat-label">Total Booking</div>
                <div class="stat-value">{{ $stats['bookings'] }}</div>
            </div>
            <div class="stat-ico">📅</div>
        </div>

        <div class="card stat-card" style="--accent:#F5A623;">
            <div>
                <div class="stat-label">Booking Mendatang</div>
                <div class="stat-value">{{ $stats['upcoming_bookings'] }}</div>
            </div>
            <div class="stat-ico">⏳</div>
        </div>
    </div>

    <div class="dash-section">Aksi Cepat</div>
    <div class="quick-grid">
        <a href="{{ route('owner.cats.index') }}" class="card quick-card" style="--accent:#F59E0B;">
            <span class="quick-ico">😺</span>
            <span class="quick-title">Profil Kucing</span>
            <span class="quick-desc">Lihat profil kucing Anda</span>
        </a>

        <a href="{{ route('owner.vaccine.calculator') }}" class="card quick-card" style="--accent:#3B82F6;">
            <span class="quick-ico">💉</span>
            <span class="quick-title">Kalkulator Vaksin</span>
            <span class="quick-desc">Hitung jadwal vaksinasi kucing</span>
        </a>

        <a href="{{ route('owner.bookings.index') }}" class="card quick-card" style="--accent:#8B5CF6;">
            <span class="quick-ico">📋</span>
            <span class="quick-title">Riwayat Booking</span>
            <span class="quick-desc">Lihat riwayat booking grooming</span>
        </a>

        <a href="{{ route('owner.mitra') }}" class="card quick-card" style="--accent:#14B8A6;">
            <span class="quick-ico">🏪</span>
            <span class="quick-title">Mitra Saya</span>
            <span class="quick-desc">Kelola petshop &amp; booking masuk</span>
        </a>
    </div>
@endsection