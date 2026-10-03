@extends('superadmin.layout')
@section('title', 'Dashboard Monitoring')

@section('content')
    <div>
        <h1 class="page-title">Dashboard Monitoring</h1>
        <p class="page-sub">Ringkasan pengguna, status petshop, dan aktivitas aplikasi PawKu.</p>
    </div>

    @php
        $icons = [
            'owners' => ['Total Pemilik Kucing', 'i-blue', '🐱'],
            'mitra' => ['Total Mitra Petshop', 'i-gold', '🏪'],
            'admins' => ['Total Admin', 'i-purple', '🛡️'],
        ];
    @endphp

    <section>
        <div class="group-title">Pengguna</div>
        <div class="grid g3">
            @foreach($icons as $key => [$label, $cls, $emoji])
                <div class="card stat">
                    <div class="stat-top"><span class="stat-label">{{ $label }}</span>
                        <div class="stat-icon {{ $cls }}">{{ $emoji }}</div>
                    </div>
                    <div class="stat-value">{{ number_format($users[$key]) }}</div>
                </div>
            @endforeach
        </div>
    </section>

    <section>
        <div class="group-title">Status Petshop</div>
        <div class="grid g3">
            <a href="{{ route('superadmin.petshops', ['status' => 'approved']) }}" class="card stat"
                style="text-decoration:none">
                <div class="stat-top"><span class="stat-label">Total Disetujui</span>
                    <div class="stat-icon i-green">✔</div>
                </div>
                <div class="stat-value">{{ number_format($petshops['approved']) }}</div>
            </a>
            <a href="{{ route('superadmin.petshops', ['status' => 'pending']) }}" class="card stat"
                style="text-decoration:none">
                <div class="stat-top"><span class="stat-label">Menunggu Persetujuan</span>
                    <div class="stat-icon i-gold">⏳</div>
                </div>
                <div class="stat-value">{{ number_format($petshops['pending']) }}</div>
            </a>
            <a href="{{ route('superadmin.petshops', ['status' => 'rejected']) }}" class="card stat"
                style="text-decoration:none">
                <div class="stat-top"><span class="stat-label">Total Ditolak</span>
                    <div class="stat-icon i-red">✖</div>
                </div>
                <div class="stat-value">{{ number_format($petshops['rejected']) }}</div>
            </a>
        </div>
    </section>

    <section>
        <div class="group-title">Aktivitas</div>
        <div class="grid g2">
            <div class="card stat">
                <div class="stat-top"><span class="stat-label">Total Booking</span>
                    <div class="stat-icon i-blue">📅</div>
                </div>
                <div class="stat-value">{{ number_format($activity['bookings']) }}</div>
            </div>
            <div class="card stat">
                <div class="stat-top"><span class="stat-label">Profil Kucing</span>
                    <div class="stat-icon i-green">🐾</div>
                </div>
                <div class="stat-value">{{ number_format($activity['cats']) }}</div>
            </div>
        </div>
    </section>
@endsection