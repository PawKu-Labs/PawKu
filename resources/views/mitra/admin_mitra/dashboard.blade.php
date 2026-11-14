@extends('mitra.layout')
@section('title', 'Beranda')

@section('content')
    @php
        $mi = \App\Support\MitraType::info($clinic->mitra_type);
        $maxService = max(1, (int) ($topServices->max('total') ?? 1));
        $timeOf = fn ($t) => $t ? substr((string) $t, 0, 5) : '-';
    @endphp

    <div>
        <h1 class="page-title">{{ $mi['icon'] }} {{ $clinic->name }}</h1>
        <p class="page-sub">Admin Klinik · Pantau booking, terima atau tolak, lalu teruskan pasien ke dokter.</p>
    </div>

    @if ($stats['menunggu'] > 0)
        <div class="hint" style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap">
            <span><b>{{ number_format($stats['menunggu']) }} booking</b> menunggu konfirmasi Anda.</span>
            <a class="btn btn-primary btn-sm" href="{{ route('admin_mitra.booking', ['mitra' => $clinic->id, 'filter' => 'menunggu']) }}">Tinjau sekarang</a>
        </div>
    @endif

    <section>
        <div class="group-title">Booking Masuk</div>
        <div class="grid g3">
            <div class="card stat">
                <div class="stat-top"><span class="stat-label">Hari Ini</span><div class="stat-icon i-blue">📅</div></div>
                <div class="stat-value">{{ number_format($stats['today']) }}</div>
            </div>
            <div class="card stat">
                <div class="stat-top"><span class="stat-label">7 Hari Terakhir</span><div class="stat-icon i-purple">🗓️</div></div>
                <div class="stat-value">{{ number_format($stats['week']) }}</div>
            </div>
            <div class="card stat">
                <div class="stat-top"><span class="stat-label">30 Hari Terakhir</span><div class="stat-icon i-gold">📆</div></div>
                <div class="stat-value">{{ number_format($stats['month']) }}</div>
            </div>
        </div>
        <p class="page-sub" style="margin-top:12px">Dihitung berdasarkan tanggal booking masuk dan tidak menghitung booking kedaluwarsa.
            @if ($stats['terjadwal'] > 0)
                · <a class="link" href="{{ route('admin_mitra.booking', ['mitra' => $clinic->id, 'filter' => 'aktif']) }}">{{ number_format($stats['terjadwal']) }} booking terjadwal setelah hari ini</a>
            @endif
        </p>
    </section>

    <section>
        <div class="group-title">Status Booking (30 hari terakhir)</div>
        <div class="grid g5">
            <div class="card stat">
                <div class="stat-top"><span class="stat-label">Aktif</span><div class="stat-icon i-green">✅</div></div>
                <div class="stat-value">{{ number_format($stats['aktif']) }}</div>
                <span class="stat-sub">Dikonfirmasi &amp; sedang dilayani</span>
            </div>
            <div class="card stat">
                <div class="stat-top"><span class="stat-label">Menunggu</span><div class="stat-icon i-gold">⏳</div></div>
                <div class="stat-value">{{ number_format($stats['menunggu']) }}</div>
                <span class="stat-sub">Perlu diterima atau ditolak</span>
            </div>
            <div class="card stat">
                <div class="stat-top"><span class="stat-label">Selesai</span><div class="stat-icon i-green">🎉</div></div>
                <div class="stat-value">{{ number_format($stats['selesai']) }}</div>
                <span class="stat-sub">Sudah selesai dilayani</span>
            </div>
            <div class="card stat">
                <div class="stat-top"><span class="stat-label">Ditolak</span><div class="stat-icon i-red">⛔</div></div>
                <div class="stat-value">{{ number_format($stats['ditolak']) }}</div>
                <span class="stat-sub">Ditolak oleh klinik</span>
            </div>
            <div class="card stat">
                <div class="stat-top"><span class="stat-label">Dibatalkan</span><div class="stat-icon i-red">🚫</div></div>
                <div class="stat-value">{{ number_format($stats['dibatalkan']) }}</div>
                <span class="stat-sub">Dibatalkan pemilik kucing</span>
            </div>
        </div>
    </section>

    <section class="grid g21">
        <div class="card">
            <div class="card-head">
                <span class="card-title">Jadwal Hari Ini</span>
                <a class="link" href="{{ route('admin_mitra.booking', ['mitra' => $clinic->id, 'filter' => 'aktif']) }}">Kelola booking →</a>
            </div>

            @if ($schedule->isEmpty())
                <div class="empty">Tidak ada jadwal aktif hari ini.</div>
            @else
                <div class="tbl-wrap">
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th>Antri</th>
                                <th>Jam</th>
                                <th>Kucing</th>
                                <th>Pemilik</th>
                                <th>Layanan</th>
                                <th>Dokter</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($schedule as $r)
                                <tr>
                                    <td><span class="queue">{{ $r->queue_number ?? '-' }}</span></td>
                                    <td class="strong">{{ $timeOf($r->slot_start_time) }}</td>
                                    <td class="strong">{{ $r->cat_name ?? '-' }}</td>
                                    <td>{{ $r->owner_name ?? $r->owner_username ?? '-' }}</td>
                                    <td>{{ $r->service_name ?? '-' }}</td>
                                    <td>
                                        @if ($r->doctor_name)
                                            {{ $r->doctor_name }}
                                        @else
                                            <span class="muted">Belum dipilih</span>
                                        @endif
                                    </td>
                                    <td><span class="st st-{{ $r->status }}">{{ $statusLabels[$r->status] ?? $r->status }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="card">
            <div class="card-head"><span class="card-title">Layanan Terlaris</span></div>
            @if ($topServices->isEmpty())
                <div class="empty">Belum ada booking masuk nih.</div>
            @else
                @foreach ($topServices as $svc)
                    <div class="bar-row">
                        <div class="bar-top"><b>{{ $svc->name }}</b><span>{{ number_format($svc->total) }} booking</span></div>
                        <div class="bar-track"><div class="bar-fill" style="width: {{ round(($svc->total / $maxService) * 100) }}%"></div></div>
                    </div>
                @endforeach
                <p class="stat-sub" style="margin-top:14px">Booking masuk 30 hari terakhir, tanpa yang ditolak / dibatalkan.</p>
            @endif
        </div>
    </section>
@endsection