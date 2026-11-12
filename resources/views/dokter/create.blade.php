@extends('dokter.layout')
@section('title', 'Pemeriksaan Baru')

@section('content')
    <div class="pg-head">
        <div>
            <h1>Pemeriksaan baru</h1>
            <p>Catatan format SOAP. Setelah difinalisasi, catatan tidak bisa diubah; koreksi dibuat sebagai addendum.</p>
        </div>
    </div>

    <form id="examForm" method="POST" action="{{ route('dokter.pemeriksaan.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="action" id="actionInput" value="draft">
        <input type="hidden" name="cat_id" value="{{ $cat['id'] }}">

        <div class="exam-layout">
            {{-- Ringkasan pasien --}}
            <aside class="exam-side">
                <section class="panel">
                    <div class="panel-b">
                        <div class="field" style="margin-bottom:14px;">
                            <label for="catPick">Pasien</label>
                            <select id="catPick"
                                onchange="location='{{ route('dokter.pemeriksaan.create') }}?cat='+this.value">
                                @foreach($daftarKucing as $k)
                                    <option value="{{ $k['id'] }}" @selected($k['id'] == $cat['id'])>{{ $k['nama'] }}
                                        ({{ $k['owner'] }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="cat-cell" style="margin-bottom:12px;">
                            <div class="cat-av"><i class="fa-solid fa-cat"></i></div>
                            <div>
                                <span class="cat-name">{{ $cat['nama'] }}</span>
                                <span class="cat-sub">{{ $cat['ras'] }}, {{ $cat['jk'] }}, {{ $cat['umur'] }}</span>
                            </div>
                        </div>
                        <dl class="kv" style="grid-template-columns:1fr 1fr;margin-top:0;">
                            <div>
                                <dt>Berat terakhir</dt>
                                <dd>{{ $cat['berat'] }} kg</dd>
                            </div>
                            <div>
                                <dt>Steril</dt>
                                <dd>{{ $cat['steril'] }}</dd>
                            </div>
                        </dl>
                        @if(count($cat['alergi']))
                            <div style="margin-top:14px;">
                                <div class="cat-sub" style="margin-bottom:6px;">Alergi</div>
                                <div class="tag-list">
                                    @foreach($cat['alergi'] as $a)
                                        <span class="chip warn"><i class="fa-solid fa-triangle-exclamation"></i> {{ $a }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        @if(count($cat['kondisi']))
                            <div style="margin-top:14px;">
                                <div class="cat-sub" style="margin-bottom:6px;">Kondisi kronis</div>
                                <div class="tag-list">
                                    @foreach($cat['kondisi'] as $k)
                                        <span class="chip draft">{{ $k }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        <a href="{{ route('dokter.pasien.show', $cat['id']) }}" class="dk-btn sm" style="margin-top:16px;"
                            target="_blank">
                            Lihat riwayat lengkap
                        </a>
                    </div>
                </section>
            </aside>

            <div>
                {{-- S: Subjective --}}
                <section class="panel soap-sec" id="sec-s">
                    <div class="panel-h">
                        <div class="ttl"><span class="soap-letter">S</span>
                            <h2>Keluhan dan anamnesis</h2>
                        </div>
                    </div>
                    <div class="panel-b">
                        <div class="fgrid wide">
                            <div class="field full">
                                <label for="keluhan">Keluhan utama</label>
                                <input id="keluhan" name="keluhan" type="text"
                                    placeholder="Contoh: muntah dan lesu sejak kemarin" required>
                            </div>
                            <div class="field full">
                                <label for="anamnesis">Anamnesis</label>
                                <textarea id="anamnesis" name="anamnesis"
                                    placeholder="Riwayat gejala, nafsu makan, pola minum, BAB dan BAK, obat yang sudah diberikan di rumah"></textarea>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- O: Objective --}}
                <section class="panel soap-sec" id="sec-o">
                    <div class="panel-h">
                        <div class="ttl"><span class="soap-letter">O</span>
                            <h2>Pemeriksaan fisik</h2>
                        </div>
                    </div>
                    <div class="panel-b">
                        <div class="fgrid">
                            <div class="field">
                                <label for="berat">Berat badan</label>
                                <div class="unit-wrap"><input id="berat" name="berat" type="number" step="0.01" min="0"
                                        value="{{ str_replace(',', '.', $cat['berat']) }}"><span>kg</span></div>
                            </div>
                            <div class="field">
                                <label for="suhu">Suhu</label>
                                <div class="unit-wrap"><input id="suhu" name="suhu" type="number" step="0.1" min="30"
                                        max="45" placeholder="38.5"><span>°C</span></div>
                            </div>
                            <div class="field">
                                <label for="hr">Denyut jantung</label>
                                <div class="unit-wrap"><input id="hr" name="denyut_jantung" type="number" min="0"
                                        placeholder="160"><span>/mnt</span></div>
                            </div>
                            <div class="field">
                                <label for="rr">Laju napas</label>
                                <div class="unit-wrap"><input id="rr" name="laju_napas" type="number" min="0"
                                        placeholder="28"><span>/mnt</span></div>
                            </div>
                            <div class="field">
                                <label for="bcs">Body condition score</label>
                                <select id="bcs" name="bcs">
                                    <option value="">Pilih skor</option>
                                    @for($i = 1; $i <= 9; $i++)
                                        <option value="{{ $i }}">{{ $i }} / 9{{ $i == 5 ? ' (ideal)' : '' }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="field">
                                <label for="mukosa">Mukosa</label>
                                <select id="mukosa" name="mukosa">
                                    <option value="">Pilih</option>
                                    <option>Merah muda (normal)</option>
                                    <option>Pucat</option>
                                    <option>Ikterik (kuning)</option>
                                    <option>Sianotik</option>
                                    <option>Hiperemis</option>
                                </select>
                            </div>
                            <div class="field">
                                <label for="hidrasi">Hidrasi</label>
                                <select id="hidrasi" name="hidrasi">
                                    <option value="">Pilih</option>
                                    <option>Normal</option>
                                    <option>Dehidrasi ringan (5%)</option>
                                    <option>Dehidrasi sedang (8%)</option>
                                    <option>Dehidrasi berat (10% atau lebih)</option>
                                </select>
                            </div>
                            <div class="field full">
                                <label for="temuan">Temuan pemeriksaan lain</label>
                                <textarea id="temuan" name="temuan"
                                    placeholder="Kulit dan bulu, mata, telinga, mulut dan gigi, palpasi abdomen, auskultasi"></textarea>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- A: Assessment --}}
                <section class="panel soap-sec" id="sec-a">
                    <div class="panel-h">
                        <div class="ttl"><span class="soap-letter">A</span>
                            <h2>Diagnosis</h2>
                        </div>
                    </div>
                    <div class="panel-b">
                        <div class="fgrid wide">
                            <div class="field">
                                <label for="dx">Diagnosis utama</label>
                                <input id="dx" name="diagnosis_utama" type="text" list="dxList"
                                    placeholder="Ketik untuk mencari" required>
                                <datalist id="dxList">
                                    <option value="Gastritis">
                                    <option value="Infeksi saluran kemih">
                                    <option value="Upper respiratory infection (URI)">
                                    <option value="Dermatitis">
                                    <option value="Infestasi cacing">
                                    <option value="Gangguan ginjal kronis">
                                </datalist>
                            </div>
                            <div class="field">
                                <label for="dxb">Diagnosis banding</label>
                                <input id="dxb" name="diagnosis_banding" type="text" placeholder="Pisahkan dengan koma">
                            </div>
                            <div class="field">
                                <label for="sev">Tingkat keparahan</label>
                                <select id="sev" name="keparahan">
                                    <option value="ringan">Ringan</option>
                                    <option value="sedang">Sedang</option>
                                    <option value="berat">Berat</option>
                                </select>
                            </div>
                            <div class="field full">
                                <label for="asesmen">Catatan asesmen</label>
                                <textarea id="asesmen" name="asesmen"
                                    placeholder="Alasan klinis di balik diagnosis"></textarea>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- P: Plan --}}
                <section class="panel soap-sec" id="sec-p">
                    <div class="panel-h">
                        <div class="ttl"><span class="soap-letter">P</span>
                            <h2>Rencana dan terapi</h2>
                        </div>
                    </div>
                    <div class="panel-b">
                        <h3 class="sub-h">Resep</h3>
                        <div id="rxList"></div>
                        <button type="button" class="dk-btn sm" id="addRx"><i class="fa-solid fa-plus"></i> Tambah
                            obat</button>

                        <h3 class="sub-h">Tindakan</h3>
                        <div class="fgrid wide">
                            <div class="field">
                                <label for="tindakan">Nama tindakan</label>
                                <input id="tindakan" name="tindakan" type="text"
                                    placeholder="Contoh: infus, scaling, steril">
                            </div>
                            <div class="field">
                                <label for="kategori">Kategori</label>
                                <select id="kategori" name="kategori_tindakan">
                                    <option value="">Tanpa tindakan</option>
                                    <option>Vaksinasi</option>
                                    <option>Perawatan</option>
                                    <option>Bedah</option>
                                    <option>Lainnya</option>
                                </select>
                            </div>
                            <div class="field full">
                                <label for="anestesi">Catatan tindakan atau anestesi</label>
                                <textarea id="anestesi" name="catatan_tindakan"></textarea>
                            </div>
                        </div>

                        <h3 class="sub-h">Tindak lanjut</h3>
                        <div class="fgrid wide">
                            <div class="field">
                                <label for="kontrol">Tanggal kontrol</label>
                                <input id="kontrol" name="tanggal_kontrol" type="date">
                                <span class="hint">Masuk otomatis ke pengingat owner.</span>
                            </div>
                            <div class="field">
                                <label for="vaksin">Jadwal vaksin berikutnya</label>
                                <input id="vaksin" name="vaksin_berikutnya" type="date">
                            </div>
                            <div class="field full">
                                <label for="anjuran">Anjuran untuk owner</label>
                                <textarea id="anjuran" name="anjuran"
                                    placeholder="Pakan, perawatan di rumah, tanda yang harus diwaspadai"></textarea>
                            </div>
                            <div class="field full">
                                <label for="internal">Catatan internal klinik</label>
                                <textarea id="internal" name="internal_notes"
                                    placeholder="Tidak terlihat oleh owner"></textarea>
                            </div>
                        </div>

                        <h3 class="sub-h">Lampiran</h3>
                        <div class="field">
                            <label for="files">Foto, hasil lab, atau rontgen</label>
                            <input id="files" name="lampiran[]" type="file" multiple accept="image/*,.pdf">
                            <span class="hint">Format JPG, PNG, atau PDF.</span>
                        </div>
                    </div>
                </section>

                <div class="action-bar">
                    <span class="note"><i class="fa-solid fa-lock"></i> Setelah final, catatan terkunci.</span>
                    <div style="display:flex;gap:10px;flex-wrap:wrap;">
                        <a href="{{ route('dokter.dashboard') }}" class="dk-btn">Batal</a>
                        <button type="submit" class="dk-btn" onclick="document.getElementById('actionInput').value='draft'">
                            <i class="fa-regular fa-floppy-disk"></i> Simpan draf
                        </button>
                        <button type="button" class="dk-btn teal" id="finalBtn">
                            <i class="fa-solid fa-check"></i> Finalisasi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        (function () {
            const list = document.getElementById('rxList');
            let idx = 0;

            function addRow() {
                const i = idx++;
                const row = document.createElement('div');
                row.className = 'rx-row';
                row.innerHTML = `
                        <div class="field"><label>Nama obat</label><input name="resep[${i}][obat]" type="text" placeholder="Amoxicillin"></div>
                        <div class="field"><label>Dosis</label><input name="resep[${i}][dosis]" type="text" placeholder="50 mg"></div>
                        <div class="field"><label>Rute</label>
                            <select name="resep[${i}][rute]">
                                <option>Oral</option><option>Injeksi</option><option>Topikal</option><option>Tetes</option>
                            </select>
                        </div>
                        <div class="field"><label>Frekuensi</label><input name="resep[${i}][frekuensi]" type="text" placeholder="2x sehari"></div>
                        <div class="field"><label>Hari</label><input name="resep[${i}][hari]" type="number" min="1" placeholder="5"></div>
                        <button type="button" class="rm" aria-label="Hapus obat"><i class="fa-solid fa-trash"></i></button>`;
                row.querySelector('.rm').addEventListener('click', () => row.remove());
                list.appendChild(row);
            }

            document.getElementById('addRx').addEventListener('click', addRow);
            addRow();

            document.getElementById('finalBtn').addEventListener('click', function () {
                const form = document.getElementById('examForm');
                if (!form.reportValidity()) return;
                Swal.fire({
                    title: 'Finalisasi rekam medis?',
                    text: 'Setelah final, catatan tidak bisa diubah. Koreksi hanya bisa lewat addendum.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#0F766E',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, finalisasi',
                    cancelButtonText: 'Periksa lagi',
                    reverseButtons: true,
                }).then((r) => {
                    if (r.isConfirmed) {
                        document.getElementById('actionInput').value = 'final';
                        form.submit();
                    }
                });
            });
        })();
    </script>
@endpush