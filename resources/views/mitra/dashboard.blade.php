@extends('mitra.layout')
@section('title', 'Beranda')

@section('content')
    @php $mi = \App\Support\MitraType::info($mitra->mitra_type); @endphp
    <div>
        <h1 class="page-title">{{ $mi['icon'] }} {{ $mitra->name }}</h1>
        <p class="page-sub">{{ $mi['label'] }} · Kelola layanan dan booking dari pelanggan Anda.</p>
    </div>

    <section>
        <div class="group-title">Ringkasan</div>
        <div class="grid g3">
            <div class="card stat">
                <div class="stat-top"><span class="stat-label">Total Booking</span><div class="stat-icon i-blue">📅</div></div>
                <div class="stat-value">{{ number_format($stats['total_bookings']) }}</div>
            </div>
            <div class="card stat">
                <div class="stat-top"><span class="stat-label">Booking Hari Ini</span><div class="stat-icon i-gold">📋</div></div>
                <div class="stat-value">{{ number_format($stats['today_bookings']) }}</div>
            </div>
            <div class="card stat">
                <div class="stat-top"><span class="stat-label">Layanan Aktif</span><div class="stat-icon i-green">🛁</div></div>
                <div class="stat-value">{{ number_format($stats['services']) }}</div>
            </div>
        </div>
        <p class="page-sub" style="margin-top:12px">Total dan hari ini tidak menghitung booking dibatalkan, ditolak, atau kedaluwarsa. Laporan lengkap menyusul di Sprint 3.</p>
    </section>
@endsection