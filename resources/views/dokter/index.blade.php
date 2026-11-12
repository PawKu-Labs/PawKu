@extends('dokter.layout')
@section('title', 'Pasien & Antrian')

@section('content')
    <div class="pg-head">
        <div>
            <h1>Pasien &amp; Antrian</h1>
            <p>Antrian booking hari ini dan semua pasien klinik.</p>
        </div>
    </div>

    <section class="panel">
        <div class="panel-h">
            <h2>Antrian hari ini</h2>
            <span class="chip owner">{{ count($antrian) }} pasien</span>
        </div>
        <div class="panel-b flush tbl-wrap">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Jam</th>
                        <th>Pasien</th>
                        <th>Layanan</th>
                        <th>Catatan penting</th>
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
                                        <a class="cat-name" href="{{ route('dokter.pasien.show', $a['cat']['id']) }}">{{ $a['cat']['nama'] }}</a>
                                        <span class="cat-sub">{{ $a['cat']['ras'] }}, {{ $a['cat']['umur'] }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $a['layanan'] }}</td>
                            <td>
                                @if(count($a['cat']['alergi']))
                                    <span class="chip warn"><i class="fa-solid fa-triangle-exclamation"></i> Alergi {{ implode(', ', $a['cat']['alergi']) }}</span>
                                @else
                                    <span class="cat-sub">Tidak ada</span>
                                @endif
                            </td>
                            <td><span class="chip {{ $a['status'] === 'Sedang diperiksa' ? 'draft' : 'owner' }}">{{ $a['status'] }}</span></td>
                            <td>
                                <a href="{{ route('dokter.pemeriksaan.create', ['cat' => $a['cat']['id']]) }}" class="dk-btn sm teal">Periksa</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <section class="panel">
        <div class="panel-h">
            <h2>Semua pasien klinik</h2>
        </div>
        <form class="filter-bar" method="GET" action="{{ route('dokter.pasien') }}">
            <input type="search" name="q" value="{{ $q }}" placeholder="Cari nama kucing atau pemilik" aria-label="Cari pasien">
            <select name="steril" aria-label="Filter status steril">
                <option value="">Semua status steril</option>
                <option>Sudah</option>
                <option>Belum</option>
            </select>
            <button class="dk-btn" type="submit"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
        </form>
        <div class="panel-b flush tbl-wrap">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Kucing</th>
                        <th>Pemilik</th>
                        <th>Berat</th>
                        <th>Steril</th>
                        <th>Kondisi kronis</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pasien as $p)
                        <tr>
                            <td>
                                <div class="cat-cell">
                                    <div class="cat-av"><i class="fa-solid fa-cat"></i></div>
                                    <div>
                                        <span class="cat-name">{{ $p['nama'] }}</span>
                                        <span class="cat-sub">{{ $p['ras'] }}, {{ $p['jk'] }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $p['owner'] }}</td>
                            <td>{{ $p['berat'] }} kg</td>
                            <td>{{ $p['steril'] }}</td>
                            <td>{{ count($p['kondisi']) ? implode(', ', $p['kondisi']) : '-' }}</td>
                            <td><a href="{{ route('dokter.pasien.show', $p['id']) }}" class="dk-btn sm">Lihat riwayat</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><div class="empty"><i class="fa-solid fa-paw"></i>Tidak ada pasien yang cocok dengan pencarian.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection