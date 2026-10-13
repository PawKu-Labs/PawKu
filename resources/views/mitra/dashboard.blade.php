@extends('mitra.layout')
@section('title', 'Dashboard Mitra')

@section('content')
    <div>
        <h1 class="page-title">Dashboard Mitra 🏪</h1>
        <p class="page-sub">Kelola petshop, layanan, dan booking dari pelanggan Anda.</p>
    </div>

    @if ($stats['petshop_status'] === 'pending')
        <div class="alert alert-err" style="background:#fffbe6; border-color:#ffe58f; color:#ad6800;">
            ⏳ Petshop Anda sedang menunggu persetujuan dari Super Admin. Beberapa fitur belum tersedia.
        </div>
    @elseif($stats['petshop_status'] === 'rejected')
        <div class="alert alert-err">
            ❌ Pendaftaran petshop Anda ditolak. Silakan hubungi admin untuk informasi lebih lanjut.
        </div>
    @endif

    <section>
        <div class="group-title">Status Petshop</div>
        <div class="grid g2">
            <div class="card stat">
                <div class="stat-top">
                    <span class="stat-label">Status Petshop</span>
                    <div class="stat-icon i-purple">🏪</div>
                </div>
                <div>
                    @php
                        $statusMap = [
                            'approved' => ['Disetujui', 'p-approved'],
                            'pending' => ['Menunggu', 'p-pending'],
                            'rejected' => ['Ditolak', 'p-rejected'],
                            'unknown' => ['Belum Terdaftar', 'p-pending'],
                        ];
                        [$statusLabel, $statusClass] = $statusMap[$stats['petshop_status']] ?? $statusMap['unknown'];
                    @endphp
                    <span class="pill {{ $statusClass }}">{{ $statusLabel }}</span>
                </div>
            </div>
            <div class="card stat">
                <div class="stat-top">
                    <span class="stat-label">Total Layanan</span>
                    <div class="stat-icon i-green">🛁</div>
                </div>
                <div class="stat-value">{{ number_format($stats['services']) }}</div>
            </div>
        </div>
    </section>

    <section>
        <div class="group-title">Booking</div>
        <div class="grid g2">
            <div class="card stat">
                <div class="stat-top">
                    <span class="stat-label">Total Booking</span>
                    <div class="stat-icon i-blue">📅</div>
                </div>
                <div class="stat-value">{{ number_format($stats['total_bookings']) }}</div>
            </div>
            <div class="card stat">
                <div class="stat-top">
                    <span class="stat-label">Booking Hari Ini</span>
                    <div class="stat-icon i-gold">📋</div>
                </div>
                <div class="stat-value">{{ number_format($stats['today_bookings']) }}</div>
            </div>
        </div>
    </section>
@endsection
