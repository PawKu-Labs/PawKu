@extends('superadmin.layout')
@section('title', 'Dashboard Monitoring')

@section('content')
    <div class="hero">
        <div>
            <h1 class="page-title">Dashboard Monitoring</h1>
            <p class="page-sub">Ringkasan pengguna, status petshop, dan aktivitas aplikasi PawKu.</p>
        </div>
    </div>

    @php
        $icons = [
            'owners' => ['Total Pemilik Kucing', 'i-blue', 'a-blue', 'fa-solid fa-cat'],
            'mitra' => ['Total Mitra Petshop', 'i-gold', 'a-gold', 'fa-solid fa-store'],
            'admins' => ['Total Admin', 'i-purple', 'a-purple', 'fa-solid fa-user-shield'],
        ];
    @endphp

    <section>
        <div class="group-title">Pengguna</div>
        <div class="grid g3">
            @foreach($icons as $key => [$label, $iconCls, $accentCls, $faIcon])
                <div class="card stat {{ $accentCls }}">
                    <div class="stat-top">
                        <span class="stat-label">{{ $label }}</span>
                        <div class="stat-icon {{ $iconCls }}"><i class="{{ $faIcon }}"></i></div>
                    </div>
                    <div class="stat-value">{{ number_format($users[$key]) }}</div>
                </div>
            @endforeach
        </div>
    </section>

    <section>
        <div class="group-title">Status Petshop</div>
        <div class="grid g3">
            <a href="{{ route('superadmin.petshops', ['status' => 'approved']) }}" class="card stat a-green">
                <div class="stat-top">
                    <span class="stat-label">Total Disetujui</span>
                    <div class="stat-icon i-green"><i class="fa-solid fa-check"></i></div>
                </div>
                <div class="stat-value">{{ number_format($petshops['approved']) }}</div>
            </a>
            <a href="{{ route('superadmin.petshops', ['status' => 'pending']) }}" class="card stat a-gold">
                <div class="stat-top">
                    <span class="stat-label">Menunggu Persetujuan</span>
                    <div class="stat-icon i-gold"><i class="fa-solid fa-hourglass-half"></i></div>
                </div>
                <div class="stat-value">{{ number_format($petshops['pending']) }}</div>
            </a>
            <a href="{{ route('superadmin.petshops', ['status' => 'rejected']) }}" class="card stat a-red">
                <div class="stat-top">
                    <span class="stat-label">Total Ditolak</span>
                    <div class="stat-icon i-red"><i class="fa-solid fa-xmark"></i></div>
                </div>
                <div class="stat-value">{{ number_format($petshops['rejected']) }}</div>
            </a>
        </div>
    </section>

    <section>
        <div class="group-title">Aktivitas</div>
        <div class="grid g2">
            <div class="card stat a-blue">
                <div class="stat-top">
                    <span class="stat-label">Total Booking</span>
                    <div class="stat-icon i-blue"><i class="fa-solid fa-calendar-days"></i></div>
                </div>
                <div class="stat-value">{{ number_format($activity['bookings']) }}</div>
            </div>
            <div class="card stat a-green">
                <div class="stat-top">
                    <span class="stat-label">Profil Kucing</span>
                    <div class="stat-icon i-green"><i class="fa-solid fa-paw"></i></div>
                </div>
                <div class="stat-value">{{ number_format($activity['cats']) }}</div>
            </div>
        </div>
    </section>
@endsection