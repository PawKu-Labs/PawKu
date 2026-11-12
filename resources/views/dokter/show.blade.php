@extends('dokter.layout')
@section('title', $cat['nama'])

@section('content')
    <div class="pg-head">
        <div>
            <h1>{{ $cat['nama'] }}</h1>
            <p>Riwayat kesehatan lengkap. Hanya terlihat sesuai izin akses owner.</p>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <a href="{{ route('dokter.pasien') }}" class="dk-btn">Kembali</a>
            <a href="{{ route('dokter.pemeriksaan.create', ['cat' => $cat['id']]) }}" class="dk-btn primary">
                <i class="fa-solid fa-stethoscope"></i> Periksa {{ $cat['nama'] }}
            </a>
        </div>
    </div>

    <section class="panel">
        <div class="panel-b">
            <div class="cat-head">
                <div class="big-av"><i class="fa-solid fa-cat"></i></div>
                <div>
                    <h2>{{ $cat['nama'] }}</h2>
                    <span class="cat-sub">{{ $cat['ras'] }}, {{ $cat['jk'] }}, {{ $cat['umur'] }}. Pemilik:
                        {{ $cat['owner'] }}</span>
                </div>
            </div>
            <dl class="kv">
                <div>
                    <dt>Berat terakhir</dt>
                    <dd>{{ $cat['berat'] }} kg</dd>
                </div>
                <div>
                    <dt>Warna</dt>
                    <dd>{{ $cat['warna'] }}</dd>
                </div>
                <div>
                    <dt>Status steril</dt>
                    <dd>{{ $cat['steril'] }}</dd>
                </div>
                <div>
                    <dt>Microchip</dt>
                    <dd>{{ $cat['microchip'] }}</dd>
                </div>
                <div>
                    <dt>Golongan darah</dt>
                    <dd>{{ $cat['golongan'] }}</dd>
                </div>
            </dl>
        </div>
    </section>

    <div class="two-col">
        <section class="panel">
            <div class="panel-h">
                <h2>Riwayat kesehatan</h2>
                <div class="tag-list">
                    <span class="chip verified">Terverifikasi</span>
                    <span class="chip observasi">Observasi mitra</span>
                    <span class="chip owner">Dilaporkan owner</span>
                </div>
            </div>
            <div class="panel-b">
                <ul class="tl">
                    @foreach($timeline as $t)
                        <li class="{{ $t['tipe'] }}">
                            <div class="tl-date">
                                {{ $t['tanggal'] }}
                                @if($t['tipe'] === 'verified')
                                    <span class="chip verified">Terverifikasi</span>
                                @elseif($t['tipe'] === 'observasi')
                                    <span class="chip observasi">Observasi mitra</span>
                                @else
                                    <span class="chip owner">Dilaporkan owner</span>
                                @endif
                                @if($t['status'] === 'final')
                                    <span class="chip final"><i class="fa-solid fa-lock"></i> Final</span>
                                @endif
                            </div>
                            <div class="tl-title">{{ $t['judul'] }}</div>
                            <div class="tl-body">{{ $t['isi'] }}</div>
                            <div class="tl-src">{{ $t['sumber'] }}</div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>

        <div>
            <section class="panel">
                <div class="panel-h">
                    <h2>Alergi</h2>
                </div>
                <div class="panel-b">
                    @if(count($cat['alergi']))
                        <div class="tag-list">
                            @foreach($cat['alergi'] as $a)
                                <span class="chip warn"><i class="fa-solid fa-triangle-exclamation"></i> {{ $a }}</span>
                            @endforeach
                        </div>
                    @else
                        <span class="cat-sub">Belum ada alergi tercatat.</span>
                    @endif
                </div>
            </section>

            <section class="panel">
                <div class="panel-h">
                    <h2>Kondisi kronis</h2>
                </div>
                <div class="panel-b">
                    @if(count($cat['kondisi']))
                        <div class="tag-list">
                            @foreach($cat['kondisi'] as $k)
                                <span class="chip draft">{{ $k }}</span>
                            @endforeach
                        </div>
                    @else
                        <span class="cat-sub">Belum ada kondisi kronis tercatat.</span>
                    @endif
                </div>
            </section>

            <section class="panel">
                <div class="panel-h">
                    <h2>Dokumen dari owner</h2>
                </div>
                <div class="panel-b">
                    <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;">
                        <div>
                            <strong style="font-size:.88rem;">Hasil lab darah.pdf</strong>
                            <div class="cat-sub">Diunggah 10 Okt 2026</div>
                        </div>
                        <button type="button" class="dk-btn sm"
                            onclick="Swal.fire({icon:'info',title:'Verifikasi dokumen',text:'Tandai dokumen ini sebagai diverifikasi dokter? (prototipe)',showCancelButton:true,confirmButtonText:'Verifikasi',cancelButtonText:'Batal',confirmButtonColor:'#F59E3D'})">
                            Verifikasi
                        </button>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection