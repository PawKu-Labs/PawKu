@extends('admin.layout')
@section('title', 'Dashboard Monitoring')

@section('content')
    <div class="hero">
        <div>
            <h1 class="page-title">Dashboard Monitoring</h1>
            <p class="page-sub">Ringkasan data pengguna, status petshop mitra, dan aktivitas aplikasi PawKu dalam satu
                tempat.</p>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            {{-- Fitur backup (dari branch teman) --}}
            <a href="{{ route('admin.backup') }}" class="btn btn-outline">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <ellipse cx="12" cy="5" rx="9" ry="3" />
                    <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3" />
                    <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5" />
                </svg>
                Backup Data
            </a>

            <a href="{{ route('admin.petshops') }}" class="btn">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                    <polyline points="9 22 9 12 15 12 15 22" />
                </svg>
                Persetujuan Petshop
                @if(($petshops['pending'] ?? 0) > 0)
                    <span style="background:#ff4d4f;color:#fff;font-size:10.5px;padding:1px 7px;border-radius:10px;">
                        {{ $petshops['pending'] }}
                    </span>
                @endif
            </a>
        </div>
    </div>

    @if(($petshops['pending'] ?? 0) > 0)
        <div class="alert-bar">
            <div style="display:flex;align-items:center;gap:12px;">
                <div class="alert-icon">!</div>
                <div>
                    <div style="font-weight:700;color:#874d00;font-size:14px;">Menunggu Tindakan Admin</div>
                    <div style="font-size:13px;color:#ad6800;margin-top:2px;">
                        Ada <strong>{{ $petshops['pending'] }}</strong> pendaftaran petshop mitra baru yang siap ditinjau.
                    </div>
                </div>
            </div>
            <a href="{{ route('admin.petshops', ['status' => 'pending']) }}" class="btn btn-green">
                Tinjau sekarang
            </a>
        </div>
    @endif

    <section>
        <div class="group-title">Pengguna</div>
        <div class="grid g3">
            <div class="card stat a-blue">
                <div class="stat-top">
                    <span class="stat-label">Total Pemilik Kucing</span>
                    <div class="stat-icon i-blue">🐱</div>
                </div>
                <div class="stat-value">{{ number_format($users['owners'] ?? 0) }}</div>
                <div class="sub">Pengguna terdaftar pemilik anabul</div>
            </div>

            <div class="card stat a-gold">
                <div class="stat-top">
                    <span class="stat-label">Total Mitra Petshop</span>
                    <div class="stat-icon i-gold">🏪</div>
                </div>
                <div class="stat-value">{{ number_format($users['mitra'] ?? 0) }}</div>
                <div class="sub">Semua mitra terdaftar di sistem</div>
            </div>

            <div class="card stat a-purple">
                <div class="stat-top">
                    <span class="stat-label">Total Admin</span>
                    <div class="stat-icon i-purple">🛡️</div>
                </div>
                <div class="stat-value">{{ number_format($users['admins'] ?? 0) }}</div>
                <div class="sub">Pengelola operasional PawKu</div>
            </div>
        </div>
    </section>

    <section>
        <div class="group-title">Status Petshop</div>
        <div class="grid g3">
            <a href="{{ route('admin.petshops', ['status' => 'approved']) }}" class="card stat a-green">
                <div class="stat-top">
                    <span class="stat-label">Total Disetujui</span>
                    <div class="stat-icon i-green">✔</div>
                </div>
                <div class="stat-value" style="color:#2E9E6B;">{{ number_format($petshops['approved'] ?? 0) }}</div>
                <div class="sub" style="color:#2E9E6B;">Lihat mitra aktif &rarr;</div>
            </a>

            <a href="{{ route('admin.petshops', ['status' => 'pending']) }}" class="card stat a-gold"
                style="@if(($petshops['pending'] ?? 0) > 0) border-color:#fbbf24;box-shadow:0 0 0 3px rgba(250,173,20,.15); @endif">
                <div class="stat-top">
                    <span class="stat-label">Menunggu Persetujuan</span>
                    <div class="stat-icon i-gold">⏳</div>
                </div>
                <div class="stat-value" style="color:#d48806;">{{ number_format($petshops['pending'] ?? 0) }}</div>
                <div class="sub" style="color:#d48806;">Lihat yang perlu ditinjau &rarr;</div>
            </a>

            <a href="{{ route('admin.petshops', ['status' => 'rejected']) }}" class="card stat a-red">
                <div class="stat-top">
                    <span class="stat-label">Total Ditolak</span>
                    <div class="stat-icon i-red">✖</div>
                </div>
                <div class="stat-value" style="color:#ef4444;">{{ number_format($petshops['rejected'] ?? 0) }}</div>
                <div class="sub" style="color:#ef4444;">Lihat riwayat ditolak &rarr;</div>
            </a>
        </div>
    </section>

    <section>
        <div class="group-title">Aktivitas</div>
        <div class="grid g2">
            <div class="card stat a-blue">
                <div class="stat-top">
                    <span class="stat-label">Total Booking</span>
                    <div class="stat-icon i-blue">📅</div>
                </div>
                <div class="stat-value">{{ number_format($activity['bookings'] ?? 0) }}</div>
                <div class="sub">Total transaksi &amp; reservasi layanan di PawKu</div>
            </div>

            <div class="card stat a-green">
                <div class="stat-top">
                    <span class="stat-label">Total Profil Kucing</span>
                    <div class="stat-icon i-green">🐾</div>
                </div>
                <div class="stat-value">{{ number_format($activity['cats'] ?? 0) }}</div>
                <div class="sub">Kucing yang didaftarkan oleh para pemilik</div>
            </div>
        </div>
    </section>

    <section>
        <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:12px;">
            <div class="group-title" style="margin:0;">Aktivitas terbaru</div>
            <a href="{{ route('admin.activity') }}" class="btn btn-outline">Lihat semua log</a>
        </div>
        <div class="card">
            @if(is_iterable($recent) && count($recent))
                <ul class="feed">
                    @foreach($recent as $log)
                        <li>
                            <span class="dot {{ $log->category }}"></span>
                            <div style="min-width:0;flex:1;">
                                <div class="feed-text">{{ $log->description }}</div>
                                <div class="sub">
                                    {{ $log->actor_name ?: 'Sistem' }} &middot;
                                    {{ \Carbon\Carbon::parse($log->created_at)->locale('id')->diffForHumans() }}
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="empty" style="padding:24px 0;">
                    Belum ada aktivitas tercatat. Jalankan migrasi <code>activity_logs</code> bila belum, lalu aktivitas akan
                    muncul di sini.
                </div>
            @endif
        </div>
    </section>
@endsection