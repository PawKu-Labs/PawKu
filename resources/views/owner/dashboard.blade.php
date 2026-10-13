@extends('owner.layout')
@section('title', 'Dashboard')

@section('content')
    <div>
        <h1 class="page-title">Selamat Datang, {{ $profile['full_name'] ?? $profile['username'] ?? 'Pemilik Kucing' }}! 🐱</h1>
        <p class="page-sub">Kelola profil kucing dan pantau jadwal grooming Anda.</p>
    </div>

    <section>
        <div class="group-title">Ringkasan</div>
        <div class="grid g3">
            <div class="card stat">
                <div class="stat-top">
                    <span class="stat-label">Kucing Saya</span>
                    <div class="stat-icon i-green">🐾</div>
                </div>
                <div class="stat-value">{{ number_format($stats['cats']) }}</div>
            </div>
            <div class="card stat">
                <div class="stat-top">
                    <span class="stat-label">Total Booking</span>
                    <div class="stat-icon i-blue">📅</div>
                </div>
                <div class="stat-value">{{ number_format($stats['bookings']) }}</div>
            </div>
            <div class="card stat">
                <div class="stat-top">
                    <span class="stat-label">Booking Mendatang</span>
                    <div class="stat-icon i-gold">⏳</div>
                </div>
                <div class="stat-value">{{ number_format($stats['upcoming_bookings']) }}</div>
            </div>
        </div>
    </section>

    <section>
        <div class="group-title">Aksi Cepat</div>
        <div class="grid g2">
            <div class="card" style="text-align:center; padding:32px 22px;">
                <div style="font-size:40px; margin-bottom:12px;">🐱</div>
                <p style="font-weight:600; color:var(--heading); margin-bottom:4px;">Profil Kucing</p>
                <p style="font-size:12px; color:var(--muted);">Lihat dan kelola profil kucing Anda</p>
            </div>
            <div class="card" style="text-align:center; padding:32px 22px;">
                <div style="font-size:40px; margin-bottom:12px;">📋</div>
                <p style="font-weight:600; color:var(--heading); margin-bottom:4px;">Riwayat Booking</p>
                <p style="font-size:12px; color:var(--muted);">Lihat riwayat booking grooming</p>
            </div>
        </div>
    </section>
@endsection
