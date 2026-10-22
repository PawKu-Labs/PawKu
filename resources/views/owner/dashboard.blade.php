@extends('owner.layout')
@section('title', 'Dashboard')

@section('content')
    <div>
        <h1 class="page-title">Selamat Datang, {{ $profile['full_name'] ?? $profile['username'] ?? 'Pemilik Kucing' }}! 🐱
        </h1>
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
        <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">
            <a href="{{ route('owner.cats.index') }}" class="card"
                style="text-align:center; padding:28px 20px; text-decoration:none; display:block; transition:transform .2s, box-shadow .2s;">
                <div style="font-size:36px; margin-bottom:10px;">🐱</div>
                <p style="font-weight:600; color:var(--heading); margin-bottom:4px;">Profil Kucing</p>
                <p style="font-size:12px; color:var(--muted);">Lihat profil kucing Anda</p>
            </a>
            <a href="{{ route('owner.vaccine.calculator') }}" class="card"
                style="text-align:center; padding:28px 20px; text-decoration:none; display:block; transition:transform .2s, box-shadow .2s;">
                <div style="font-size:36px; margin-bottom:10px;">💉</div>
                <p style="font-weight:600; color:var(--heading); margin-bottom:4px;">Kalkulator Vaksin</p>
                <p style="font-size:12px; color:var(--muted);">Hitung jadwal vaksinasi kucing</p>
            </a>
            <div class="card" style="text-align:center; padding:28px 20px;">
                <div style="font-size:36px; margin-bottom:10px;">📋</div>
                <p style="font-weight:600; color:var(--heading); margin-bottom:4px;">Riwayat Booking</p>
                <p style="font-size:12px; color:var(--muted);">Lihat riwayat booking grooming</p>
            </div>
            @if ($layoutHasMitra ?? false)
                <a href="{{ route('owner.mitra') }}" class="card"
                    style="text-align:center; padding:28px 20px; text-decoration:none; display:block; transition:transform .2s, box-shadow .2s; border-color: rgba(46, 158, 107, 0.4); background: #fbfdfc;">
                    <div style="font-size:36px; margin-bottom:10px;">🏪</div>
                    <p style="font-weight:600; color:var(--primary); margin-bottom:4px;">Mitra Saya</p>
                    <p style="font-size:12px; color:var(--muted);">Kelola petshop & booking masuk</p>
                </a>
            @else
                <div class="card" onclick="showMitraDisabledInfo()"
                    style="text-align:center; padding:28px 20px; cursor:not-allowed; opacity: 0.65; background:#fafafa;">
                    <div style="font-size:36px; margin-bottom:10px; filter: grayscale(1);">🏪</div>
                    <p style="font-weight:600; color:var(--muted); margin-bottom:4px;">Dashboard Mitra</p>
                    <p style="font-size:12px; color:var(--muted);">Terkunci (Belum Terdaftar)</p>
                </div>
            @endif
        </div>
    </section>
@endsection