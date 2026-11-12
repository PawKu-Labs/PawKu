@extends('dokter.layout')
@section('title', 'Dashboard')

@section('content')
    @php
        $dokterName = session('supabase.profile.full_name')
            ?? session('supabase.profile.username')
            ?? session('supabase.user.email', 'Dokter');
    @endphp
    <div class="pg-head">
        <div>
            <h1>Selamat bekerja, drh. {{ $dokterName }}</h1>
            <p>Kamis, 12 November 2026. Ada {{ $stats['antrian'] }} pasien dijadwalkan hari ini.</p>
        </div>
        <a href="{{ route('dokter.pemeriksaan.create') }}" class="dk-btn primary">
            <i class="fa-solid fa-plus"></i> Mulai pemeriksaan
        </a>
    </div>

    <div class="stat-row">
        <div class="stat hot">
            <div class="n">{{ $stats['antrian'] }}</div>
            <div class="l">Pasien hari ini</div>
        </div>
        <div class="stat">
            <div class="n">{{ $stats['selesai'] }}</div>
            <div class="l">Sudah difinalisasi</div>
        </div>
        <div class="stat">
            <div class="n">{{ $stats['draft'] }}</div>
            <div class="l">Draf belum final</div>
        </div>
        <div class="stat">
            <div class="n">{{ $stats['kontrol'] }}</div>
            <div class="l">Kontrol minggu ini</div>
        </div>
    </div>

    <div class="two-col">
        <section class="panel">
            <div class="panel-h">
                <h2>Antrian hari ini</h2>
                <a href="{{ route('dokter.pasien') }}" class="dk-btn sm">Lihat semua</a>
            </div>
            <div class="panel-b flush tbl-wrap">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Jam</th>
                            <th>Pasien</th>
                            <th>Layanan</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($antrian as $a)
                            <tr>
                                <td>{{ $a['jam'] }}</td>
                                <td>
                                    <div class="cat-cell">
                                        <div class="cat-av"><i class="fa-solid fa-cat"></i></div>
                                        <div>
                                            <span class="cat-name">{{ $a['cat']['nama'] }}</span>
                                            <span class="cat-sub">{{ $a['cat']['ras'] }}, pemilik
                                                {{ $a['cat']['owner'] }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $a['layanan'] }}</td>
                                <td>
                                    <span
                                        class="chip {{ $a['status'] === 'Sedang diperiksa' ? 'draft' : 'owner' }}">{{ $a['status'] }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('dokter.pemeriksaan.create', ['cat' => $a['cat']['id']]) }}"
                                        class="dk-btn sm teal">Periksa</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <div>
            <section class="panel">
                <div class="panel-h">
                    <h2>Draf perlu diselesaikan</h2>
                </div>
                <div class="panel-b">
                    @forelse($draftList as $d)
                        <div style="padding:0 0 12px;margin-bottom:12px;border-bottom:1px solid var(--dk-line);">
                            <strong>{{ $d['cat'] }}</strong>
                            <div class="cat-sub">{{ $d['tanggal'] }}</div>
                            <div style="font-size:.85rem;margin-top:4px;">{{ $d['keluhan'] }}</div>
                        </div>
                    @empty
                        <div class="empty"><i class="fa-regular fa-circle-check"></i>Semua catatan sudah final.</div>
                    @endforelse
                    <a href="{{ route('dokter.rekam-medis') }}" class="dk-btn sm">Buka rekam medis</a>
                </div>
            </section>

            <section class="panel">
                <div class="panel-h">
                    <h2>Jadwal kontrol dan vaksin</h2>
                </div>
                <div class="panel-b">
                    @foreach($kontrol as $k)
                        <div style="display:flex;justify-content:space-between;gap:10px;margin-bottom:12px;">
                            <div>
                                <strong>{{ $k['cat'] }}</strong>
                                <div class="cat-sub">{{ $k['alasan'] }}</div>
                            </div>
                            <span class="chip owner">{{ $k['tanggal'] }}</span>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>
    </div>
@endsection