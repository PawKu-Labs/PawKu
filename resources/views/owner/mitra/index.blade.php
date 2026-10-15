@extends('owner.layout')
@section('title', 'Mitra Saya')

@section('content')
    <style>
        .m-card {
            display: flex;
            gap: 14px;
            align-items: center;
            text-decoration: none;
            color: inherit;
            transition: transform .2s, box-shadow .2s
        }

        .m-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px -6px rgba(0, 0, 0, .12)
        }

        .m-thumb {
            width: 68px;
            height: 68px;
            border-radius: 18px;
            background: var(--bg);
            border: 1px solid var(--border);
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            flex-shrink: 0
        }

        .m-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover
        }

        .m-name {
            font-weight: 800;
            font-size: 15.5px;
            color: var(--heading);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis
        }

        .m-chips {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            margin-top: 6px
        }

        .m-addr {
            font-size: 12px;
            color: var(--muted);
            margin-top: 8px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis
        }

        .m-body {
            min-width: 0;
            flex: 1
        }

        .m-arrow {
            font-size: 22px;
            color: var(--muted);
            flex-shrink: 0
        }

        .btn-add {
            background: var(--primary);
            color: #fff;
            border: 0;
            padding: 10px 18px;
            border-radius: var(--r);
            font-weight: 700;
            font-size: 13px;
            cursor: pointer
        }

        .btn-add:hover {
            background: var(--primary-hover)
        }

        .m-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            flex-wrap: wrap
        }
    </style>

    <div class="m-head">
        <div>
            <h1 class="page-title">Mitra Saya 🏪</h1>
            <p class="page-sub">Satu akun bisa memiliki beberapa usaha.</p>
        </div>
        <button type="button" class="btn-add"
            onclick="Swal.fire({icon:'info',title:'Segera hadir',text:'Formulir pendaftaran mitra tersedia di Sprint 2.'})">+
            Daftarkan usaha lain</button>
    </div>

    @if ($loadError)
        <div class="alert alert-err">Gagal memuat data mitra. Muat ulang halaman.</div>
    @endif

    <section>
        <div class="group-title">Ringkasan</div>
        <div class="grid g3">
            <div class="card stat">
                <div class="stat-top"><span class="stat-label">Aktif</span>
                    <div class="stat-icon i-green">✅</div>
                </div>
                <div class="stat-value">{{ $counts['approved'] }}</div>
            </div>
            <div class="card stat">
                <div class="stat-top"><span class="stat-label">Menunggu</span>
                    <div class="stat-icon i-gold">⏳</div>
                </div>
                <div class="stat-value">{{ $counts['pending'] }}</div>
            </div>
            <div class="card stat">
                <div class="stat-top"><span class="stat-label">Ditolak</span>
                    <div class="stat-icon i-red">❌</div>
                </div>
                <div class="stat-value">{{ $counts['rejected'] }}</div>
            </div>
        </div>
    </section>

    <section>
        <div class="group-title">Daftar Usaha ({{ $mitras->count() }})</div>
        @if ($mitras->isEmpty() && !$loadError)
            <div class="card" style="text-align:center;padding:40px 20px;">
                <div style="font-size:40px;margin-bottom:8px;">🏪</div>
                <p>Anda belum mendaftarkan usaha. Daftarkan pet shop, klinik, hotel, atau grooming Anda.</p>
            </div>
        @else
            <div class="grid g2">
                @foreach ($mitras as $m)
                    @php
                        $mi = \App\Support\MitraType::info($m->mitra_type);
                        $img = $m->logo_url ?: $m->photo_url;
                        $st = [
                            'approved' => ['Aktif', 'p-approved'],
                            'rejected' => ['Ditolak', 'p-rejected'],
                        ][$m->status] ?? ['Menunggu persetujuan', 'p-pending'];

                        $href = \App\Support\MitraUrl::for($m) ?? '#';
                    @endphp

                    <a href="{{ $href }}" class="card m-card">
                        <div class="m-thumb">
                            @if ($img)
                                <img src="{{ $img }}" alt="{{ $m->name }}">
                            @else
                                {{ $mi['icon'] }}
                            @endif
                        </div>

                        <div class="m-body">
                            <div class="m-name">{{ $m->name }}</div>
                            <div class="m-chips">
                                <span class="pill" style="background:var(--bg);color:var(--body);">{{ $mi['label'] }}</span>
                                <span class="pill {{ $st[1] }}">{{ $st[0] }}</span>
                            </div>
                            @if (!empty($m->address))
                                <div class="m-addr">📍 {{ $m->address }}</div>
                            @endif
                        </div>

                        <span class="m-arrow">›</span>
                    </a>
                @endforeach
            </div>
        @endif
    </section>
@endsection