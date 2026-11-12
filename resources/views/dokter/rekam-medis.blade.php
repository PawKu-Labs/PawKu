@extends('dokter.layout')
@section('title', 'Rekam Medis Saya')

@section('content')
    <div class="pg-head">
        <div>
            <h1>Rekam Medis Saya</h1>
            <p>Daftar riwayat pemeriksaan yang pernah Anda buat atau tangani.</p>
        </div>
        <div>
            <a href="{{ route('dokter.pemeriksaan.create') }}" class="dk-btn primary">
                <i class="fa-solid fa-plus"></i> Buat Pemeriksaan Baru
            </a>
        </div>
    </div>

    <section class="panel">
        <div class="panel-h">
            <h2>Riwayat Pemeriksaan</h2>
            <span class="chip owner">{{ count($records) }} catatan</span>
        </div>
        <div class="panel-b flush tbl-wrap">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tanggal</th>
                        <th>Pasien</th>
                        <th>Pemilik</th>
                        <th>Diagnosis</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $r)
                        <tr>
                            <td>#{{ $r['id'] }}</td>
                            <td>{{ $r['tanggal'] }}</td>
                            <td>
                                <strong>{{ $r['cat'] }}</strong>
                            </td>
                            <td>{{ $r['owner'] }}</td>
                            <td>{{ $r['diagnosis'] }}</td>
                            <td>
                                <span class="chip {{ $r['status'] === 'final' ? 'owner' : ($r['status'] === 'draft' ? 'draft' : 'warn') }}">
                                    {{ ucfirst($r['status']) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 24px; color: #888;">
                                Belum ada rekam medis.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection

