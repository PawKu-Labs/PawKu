@extends('dokter.layout')
@section('title', 'Profil Praktik')

@section('content')
    <div class="pg-head">
        <div>
            <h1>Profil Praktik</h1>
            <p>Informasi kredensial dokter hewan dan klinik tempat Anda berpraktek.</p>
        </div>
    </div>

    <div class="two-col">
        <section class="panel">
            <div class="panel-h">
                <h2>Data Kredensial Praktik</h2>
                <span class="chip {{ $dokter['status'] === 'verified' ? 'owner' : 'warn' }}">
                    {{ ucfirst($dokter['status']) }}
                </span>
            </div>
            <div class="panel-b">
                <form action="{{ route('dokter.profil.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="field" style="margin-bottom: 14px;">
                        <label for="str">Nomor STR</label>
                        <input type="text" id="str" name="str" value="{{ $dokter['str'] }}" required style="width: 100%; padding: 8px 12px; border-radius: 8px; border: 1px solid #ddd;">
                    </div>

                    <div class="field" style="margin-bottom: 14px;">
                        <label for="sip">Nomor SIP</label>
                        <input type="text" id="sip" name="sip" value="{{ $dokter['sip'] }}" required style="width: 100%; padding: 8px 12px; border-radius: 8px; border: 1px solid #ddd;">
                    </div>

                    <div class="field" style="margin-bottom: 18px;">
                        <label for="spesialisasi">Spesialisasi</label>
                        <input type="text" id="spesialisasi" name="spesialisasi" value="{{ $dokter['spesialisasi'] }}" required style="width: 100%; padding: 8px 12px; border-radius: 8px; border: 1px solid #ddd;">
                    </div>

                    <button type="submit" class="dk-btn primary">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                    </button>
                </form>
            </div>
        </section>

        <section class="panel">
            <div class="panel-h">
                <h2>Klinik Terafiliasi</h2>
                <span class="chip owner">{{ count($klinik) }} klinik</span>
            </div>
            <div class="panel-b flush tbl-wrap">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Nama Klinik</th>
                            <th>Peran</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($klinik as $k)
                            <tr>
                                <td><strong>{{ $k['nama'] }}</strong></td>
                                <td>{{ $k['peran'] }}</td>
                                <td>
                                    <span class="chip {{ $k['aktif'] ? 'owner' : 'draft' }}">
                                        {{ $k['aktif'] ? 'Aktif' : 'Non-aktif' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection

