@extends('admin.layout')
@section('title', 'Banner Promo')

@push('styles')
    <style>
        /* ---- Banner (meniru kartu ajakan di beranda aplikasi) ---- */
        .bn {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px;
            border-radius: 22px;
            color: #fff;
            background-size: cover;
            background-position: center;
            box-shadow: 0 10px 22px -10px rgba(43, 33, 24, .45)
        }

        .bn-ico {
            width: 48px;
            height: 48px;
            border-radius: 15px;
            background: rgba(255, 255, 255, .2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 21px;
            flex-shrink: 0
        }

        .bn-txt {
            flex: 1;
            min-width: 0
        }

        .bn-title {
            font-size: 15px;
            font-weight: 800;
            line-height: 1.25;
            overflow-wrap: anywhere
        }

        .bn-sub {
            font-size: 12px;
            margin-top: 3px;
            opacity: .92;
            line-height: 1.45;
            overflow-wrap: anywhere
        }

        .bn-go {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #fff;
            color: #7C5CE0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0
        }

        .bn-cta {
            display: inline-block;
            margin-top: 8px;
            background: #fff;
            color: var(--heading);
            font-size: 11.5px;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 20px
        }

        /* ---- Pratinjau ---- */
        .preview-wrap {
            display: grid;
            grid-template-columns: minmax(0, 380px) 1fr;
            gap: 22px;
            align-items: start
        }

        @media (max-width: 860px) {
            .preview-wrap {
                grid-template-columns: 1fr
            }
        }

        .phone {
            background: #faf6f1;
            border: 1px solid var(--border);
            border-radius: 26px;
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            min-height: 160px
        }

        .phone-bar {
            font-size: 12px;
            font-weight: 700;
            color: var(--muted)
        }

        .pv-side {
            display: flex;
            flex-direction: column;
            gap: 14px
        }

        .note {
            font-size: 12.5px;
            line-height: 1.6;
            color: var(--body)
        }

        .note i {
            color: var(--primary);
            margin-right: 6px
        }

        /* ---- Tabel ---- */
        .thumb {
            width: 64px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 16px;
            background-size: cover;
            background-position: center;
            flex-shrink: 0
        }

        .b-cell {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 220px
        }

        .b-cell .sub {
            max-width: 280px
        }

        .chip {
            display: inline-block;
            font-size: 11px;
            font-weight: 600;
            padding: 3px 9px;
            border-radius: 20px;
            background: var(--primary-light);
            color: #B45309;
            margin: 2px 4px 2px 0;
            white-space: nowrap
        }

        .p-scheduled {
            background: #e0f2fe;
            color: #0369a1
        }

        .p-ended {
            background: #f3efea;
            color: #5b5047
        }

        .order {
            display: inline-flex;
            gap: 4px
        }

        .order .btn {
            padding: 4px 8px;
            min-height: 30px
        }

        .section-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 12px
        }

        .section-head .group-title {
            margin: 0
        }

        .hint {
            font-size: 11.5px;
            color: var(--muted)
        }

        .bar-tools {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center
        }

        .bar-tools .input {
            width: auto;
            min-height: 38px
        }

        /* ---- Switch ---- */
        .switch {
            position: relative;
            width: 40px;
            height: 22px;
            display: inline-block;
            flex-shrink: 0
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0
        }

        .slider {
            position: absolute;
            inset: 0;
            background: #d6cdc2;
            border-radius: 22px;
            cursor: pointer;
            transition: .2s
        }

        .slider::before {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            left: 3px;
            top: 3px;
            background: #fff;
            border-radius: 50%;
            transition: .2s
        }

        .switch input:checked+.slider {
            background: var(--green)
        }

        .switch input:checked+.slider::before {
            transform: translateX(18px)
        }

        .switch input:focus-visible+.slider {
            box-shadow: var(--ring)
        }

        /* ---- Form ---- */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px
        }

        .form-grid .full {
            grid-column: 1/-1
        }

        @media (max-width: 520px) {
            .form-grid {
                grid-template-columns: 1fr
            }
        }

        dialog.wide {
            width: min(640px, 94vw)
        }

        .modal-foot {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            margin-top: 18px;
            flex-wrap: wrap
        }

        .swatches,
        .icons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap
        }

        .swatches label,
        .icons label {
            cursor: pointer
        }

        .swatches input,
        .icons input {
            position: absolute;
            opacity: 0;
            pointer-events: none
        }

        .sw {
            display: block;
            width: 38px;
            height: 38px;
            border-radius: 12px;
            border: 3px solid transparent;
            box-shadow: 0 0 0 1px var(--border)
        }

        .swatches input:checked+.sw {
            border-color: #fff;
            box-shadow: 0 0 0 2px var(--heading)
        }

        .swatches input:focus-visible+.sw,
        .icons input:focus-visible+.ic {
            box-shadow: var(--ring)
        }

        .ic {
            display: flex;
            width: 38px;
            height: 38px;
            border-radius: 12px;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--border);
            color: var(--body);
            background: #fff
        }

        .icons input:checked+.ic {
            background: var(--grad-brand);
            color: #fff;
            border-color: transparent
        }

        .checks {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            padding: 10px 12px;
            border: 1px solid var(--border);
            border-radius: 10px
        }

        .checks label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 500;
            color: var(--heading);
            cursor: pointer
        }

        .mpv {
            margin-top: 14px;
            padding: 14px;
            background: #faf6f1;
            border: 1px solid var(--border);
            border-radius: 16px
        }

        .mpv .hint {
            margin-bottom: 8px;
            display: block
        }
    </style>
@endpush

@section('content')
    <div class="hero">
        <div>
            <h1 class="page-title">Banner Promo</h1>
            <p class="page-sub">Kelola banner ajakan dan promo yang tampil di beranda aplikasi dan website PawKu. Atur
                teks, warna, tujuan tombol, target pengguna, dan jadwal tayang.</p>
        </div>
        <div>
            <button type="button" class="btn" onclick="openBanner()">
                <i class="fa-solid fa-plus"></i> Tambah Banner
            </button>
        </div>
    </div>

    {{-- ====== RINGKASAN ====== --}}
    <section>
        <div class="grid g3">
            <div class="card stat a-green">
                <div class="stat-top"><span class="stat-label">Sedang Tayang</span>
                    <div class="stat-icon i-green"><i class="fa-solid fa-bullhorn"></i></div>
                </div>
                <div class="stat-value" id="sLive">0</div>
                <div class="sub">Banner yang terlihat pengguna hari ini</div>
            </div>
            <div class="card stat a-blue">
                <div class="stat-top"><span class="stat-label">Dijadwalkan</span>
                    <div class="stat-icon i-blue"><i class="fa-solid fa-calendar-days"></i></div>
                </div>
                <div class="stat-value" id="sSched">0</div>
                <div class="sub">Akan tayang di kemudian hari</div>
            </div>
            <div class="card stat a-gold">
                <div class="stat-top"><span class="stat-label">Berakhir / Nonaktif</span>
                    <div class="stat-icon i-gold"><i class="fa-solid fa-eye-slash"></i></div>
                </div>
                <div class="stat-value" id="sOff">0</div>
                <div class="sub">Tidak ditampilkan</div>
            </div>
        </div>
    </section>

    {{-- ====== PRATINJAU ====== --}}
    <section>
        <div class="section-head">
            <div class="group-title">Pratinjau Tampilan</div>
            <div class="bar-tools">
                <select class="input" id="pvAud" aria-label="Pratinjau untuk pengguna">
                    <option value="all">Pengguna: semua</option>
                    <option value="no_mitra">Belum punya Mitra</option>
                    <option value="has_mitra">Sudah punya Mitra</option>
                    <option value="no_cat">Belum punya kucing</option>
                </select>
                <select class="input" id="pvPlace" aria-label="Pratinjau di">
                    <option value="app">Di aplikasi</option>
                    <option value="web">Di website</option>
                </select>
            </div>
        </div>
        <div class="card">
            <div class="preview-wrap">
                <div class="phone">
                    <div class="phone-bar" id="pvBar">Beranda aplikasi</div>
                    <div id="pvList" style="display:flex;flex-direction:column;gap:12px;"></div>
                </div>
                <div class="pv-side">
                    <p class="note"><i class="fa-solid fa-circle-info"></i>Pratinjau hanya menampilkan banner yang aktif
                        hari ini, sesuai target pengguna dan tempat tampil yang dipilih.</p>
                    <p class="note"><i class="fa-solid fa-arrow-down-1-9"></i>Urutan banner mengikuti urutan di tabel
                        di bawah. Gunakan tombol panah untuk memindahkan.</p>
                    <p class="note"><i class="fa-solid fa-flask"></i>Data dummy. Perubahan belum tersimpan ke database.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ====== TABEL ====== --}}
    <section>
        <div class="section-head">
            <div class="group-title">Daftar Banner</div>
            <div class="tabs" id="bTabs">
                <a href="#" class="tab active" data-f="all">Semua</a>
                <a href="#" class="tab" data-f="live">Tayang</a>
                <a href="#" class="tab" data-f="scheduled">Dijadwalkan</a>
                <a href="#" class="tab" data-f="off">Berakhir</a>
            </div>
        </div>
        <div class="card" style="padding:0;">
            <div class="table-wrap">
                <table class="responsive">
                    <thead>
                        <tr>
                            <th class="cell-first">Banner</th>
                            <th>Target Pengguna</th>
                            <th>Tampil di</th>
                            <th>Periode</th>
                            <th>Urutan</th>
                            <th>Status</th>
                            <th>On/Off</th>
                            <th class="cell-last">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="bBody"></tbody>
                </table>
            </div>
        </div>
    </section>

    {{-- ====== MODAL ====== --}}
    <dialog id="bModal" class="wide">
        <div class="modal-header">
            <div class="modal-title" id="bmTitle">Tambah Banner</div>
            <button type="button" class="modal-close" onclick="bModal.close()" aria-label="Tutup">&times;</button>
        </div>
        <div class="form-grid">
            <div class="field full">
                <label for="fTitle">Judul</label>
                <input class="input" id="fTitle" maxlength="60" placeholder="Contoh: Promo Ultah PawKu -10%">
            </div>
            <div class="field full">
                <label for="fSub">Teks pendukung</label>
                <input class="input" id="fSub" maxlength="110" placeholder="Contoh: Berlaku untuk semua paket sampai akhir bulan.">
            </div>
            <div class="field">
                <label for="fLink">Tombol membuka</label>
                <select class="input" id="fLink"></select>
            </div>
            <div class="field">
                <label for="fCta">Teks tombol (khusus website)</label>
                <input class="input" id="fCta" maxlength="20" placeholder="Lihat promo">
            </div>
            <div class="field full" id="urlRow" style="display:none;">
                <label for="fUrl">Alamat tujuan</label>
                <input class="input" id="fUrl" placeholder="https://...">
            </div>
            <div class="field full">
                <label>Warna latar</label>
                <div class="swatches" id="fColors"></div>
            </div>
            <div class="field full">
                <label>Ikon</label>
                <div class="icons" id="fIcons"></div>
            </div>
            <div class="field full">
                <label for="fImg">Gambar latar (opsional)</label>
                <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                    <input class="input" id="fImg" type="file" accept="image/*" style="flex:1 1 220px;">
                    <button type="button" class="btn btn-outline" id="fImgClear" style="display:none;">Hapus gambar</button>
                </div>
                <span class="hint">Gambar diberi lapisan gelap tipis agar teks tetap terbaca. Disarankan rasio lebar.</span>
            </div>
            <div class="field full">
                <label for="fAud">Target pengguna</label>
                <select class="input" id="fAud"></select>
            </div>
            <div class="field full">
                <label>Tampil di</label>
                <div class="checks">
                    <label><input type="checkbox" id="fApp"> Aplikasi (beranda)</label>
                    <label><input type="checkbox" id="fWeb"> Website</label>
                </div>
            </div>
            <div class="field">
                <label for="fStart">Mulai tayang</label>
                <input class="input" id="fStart" type="date">
            </div>
            <div class="field">
                <label for="fEnd">Selesai tayang</label>
                <input class="input" id="fEnd" type="date">
            </div>
        </div>
        <div class="mpv">
            <span class="hint">Pratinjau langsung</span>
            <div id="fPreview"></div>
        </div>
        <div class="modal-foot">
            <button type="button" class="btn btn-outline" onclick="bModal.close()">Batal</button>
            <button type="button" class="btn btn-primary" onclick="saveBanner()">Simpan Banner</button>
        </div>
    </dialog>
@endsection

@push('scripts')
    <script>
        /* =============== KONFIGURASI =============== */
        const gradients = {
            ungu: ['Ungu', 'linear-gradient(135deg,#7C5CE0 0%,#9775FA 100%)', '#7C5CE0'],
            oranye: ['Oranye', 'linear-gradient(135deg,#F7B15C 0%,#EA8A1F 100%)', '#C26A0B'],
            hijau: ['Hijau', 'linear-gradient(135deg,#2E9E6B 0%,#14b8a6 100%)', '#238356'],
            merah: ['Merah muda', 'linear-gradient(135deg,#f43f5e 0%,#fb923c 100%)', '#e11d48']
        };
        const icons = {
            'fa-store': 'Toko', 'fa-cat': 'Kucing', 'fa-tags': 'Promo', 'fa-gift': 'Hadiah',
            'fa-bell': 'Info', 'fa-syringe': 'Vaksin', 'fa-paw': 'Paw', 'fa-calendar-check': 'Booking'
        };
        const audiences = {
            all: 'Semua pengguna', no_mitra: 'Belum punya Mitra',
            has_mitra: 'Sudah punya Mitra', no_cat: 'Belum punya kucing'
        };
        const links = {
            register_mitra: 'Daftar jadi Mitra',
            buy_cat_plan: 'Beli paket Profil Kucing',
            find_petshop: 'Cari petshop',
            add_cat: 'Tambah kucing',
            custom: 'Alamat lain (URL)'
        };

        /* =============== DATA DUMMY =============== */
        const today = new Date(); today.setHours(0, 0, 0, 0);
        const iso = (d) => new Date(d.getTime() - d.getTimezoneOffset() * 60000).toISOString().slice(0, 10);
        const shift = (n) => { const d = new Date(today); d.setDate(d.getDate() + n); return iso(d); };

        let banners = [
            { id: 1, title: 'Jadi Mitra PawKu', sub: 'Buka booking online untuk petshop kamu, mulai Rp10.000 per bulan.', cta: 'Daftar sekarang', link: 'register_mitra', url: '', color: 'ungu', icon: 'fa-store', image: '', aud: 'no_mitra', app: true, web: true, start: shift(-30), end: shift(60), on: true },
            { id: 2, title: 'Punya usaha lain?', sub: 'Daftarkan Mitra baru. Alamat dan jam bisa berbeda.', cta: 'Tambah Mitra', link: 'register_mitra', url: '', color: 'ungu', icon: 'fa-store', image: '', aud: 'has_mitra', app: true, web: false, start: shift(-30), end: shift(90), on: true },
            { id: 3, title: 'Profil Kucing Unlimited', sub: 'Bayar sekali, daftarkan kucing tanpa batas selamanya.', cta: 'Lihat paket', link: 'buy_cat_plan', url: '', color: 'oranye', icon: 'fa-cat', image: '', aud: 'all', app: true, web: true, start: shift(-10), end: shift(45), on: true },
            { id: 4, title: 'Promo Ultah PawKu -10%', sub: 'Diskon semua paket sampai akhir bulan.', cta: 'Ambil promo', link: 'buy_cat_plan', url: '', color: 'merah', icon: 'fa-gift', image: '', aud: 'all', app: true, web: true, start: shift(7), end: shift(21), on: true },
            { id: 5, title: 'Cari petshop terdekat', sub: 'Booking grooming dan konsultasi langsung dari aplikasi.', cta: 'Cari petshop', link: 'find_petshop', url: '', color: 'hijau', icon: 'fa-calendar-check', image: '', aud: 'all', app: true, web: false, start: shift(-60), end: shift(-15), on: true },
            { id: 6, title: 'Tambah kucing pertamamu', sub: 'Mulai pantau berat badan dan jadwal vaksin.', cta: 'Tambah kucing', link: 'add_cat', url: '', color: 'oranye', icon: 'fa-paw', image: '', aud: 'no_cat', app: true, web: true, start: shift(-20), end: shift(100), on: false }
        ];
        let nextId = 7, editing = null, filter = 'all', pendingImage = '';

        /* =============== HELPER =============== */
        const $ = (id) => document.getElementById(id);
        const esc = (s) => String(s).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
        const fmtDate = (s) => new Date(s + 'T00:00:00').toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });

        function statusOf(b) {
            if (!b.on) return 'off';
            const s = new Date(b.start + 'T00:00:00'), e = new Date(b.end + 'T00:00:00');
            if (today < s) return 'scheduled';
            if (today > e) return 'off';
            return 'live';
        }
        const pills = { live: ['p-active', 'Tayang'], scheduled: ['p-scheduled', 'Dijadwalkan'], off: ['p-ended', 'Berakhir'] };

        const bg = (b) => b.image
            ? `linear-gradient(rgba(0,0,0,.38),rgba(0,0,0,.38)),url("${b.image}")`
            : gradients[b.color][1];

        function bannerHTML(b, web) {
            return `<div class="bn" style="background:${bg(b)}">
                <div class="bn-ico"><i class="fa-solid ${b.icon}"></i></div>
                <div class="bn-txt">
                    <div class="bn-title">${esc(b.title || 'Judul banner')}</div>
                    <div class="bn-sub">${esc(b.sub || 'Teks pendukung banner')}</div>
                    ${web ? `<span class="bn-cta">${esc(b.cta || 'Lihat')}</span>` : ''}
                </div>
                ${web ? '' : `<div class="bn-go" style="color:${gradients[b.color][2]}"><i class="fa-solid fa-arrow-right"></i></div>`}
            </div>`;
        }

        /* =============== RENDER =============== */
        function renderPreview() {
            const aud = $('pvAud').value, place = $('pvPlace').value;
            const list = banners.filter(b => statusOf(b) === 'live' && b[place] && (b.aud === 'all' || b.aud === aud));
            $('pvBar').textContent = place === 'app' ? 'Beranda aplikasi' : 'Website PawKu';
            $('pvList').innerHTML = list.length
                ? list.map(b => bannerHTML(b, place === 'web')).join('')
                : `<div class="empty" style="padding:28px 8px"><div style="font-size:24px;margin-bottom:8px"><i class="fa-regular fa-image"></i></div>Tidak ada banner aktif untuk pilihan ini.</div>`;
        }

        function renderTable() {
            const all = banners.map(b => ({ ...b, st: statusOf(b) }));
            $('sLive').textContent = all.filter(b => b.st === 'live').length;
            $('sSched').textContent = all.filter(b => b.st === 'scheduled').length;
            $('sOff').textContent = all.filter(b => b.st === 'off').length;

            const rows = filter === 'all' ? all : all.filter(b => b.st === filter);
            $('bBody').innerHTML = rows.length ? rows.map(b => {
                const idx = banners.findIndex(x => x.id === b.id);
                return `<tr>
                    <td class="cell-first" data-label="Banner">
                        <div class="b-cell">
                            <div class="thumb" style="background:${bg(b)}"><i class="fa-solid ${b.icon}"></i></div>
                            <div style="min-width:0;text-align:left"><strong>${esc(b.title)}</strong><div class="sub">${esc(b.sub)}</div></div>
                        </div>
                    </td>
                    <td data-label="Target"><span class="chip">${esc(audiences[b.aud])}</span></td>
                    <td data-label="Tampil di">${b.app ? '<span class="chip">Aplikasi</span>' : ''}${b.web ? '<span class="chip">Website</span>' : ''}</td>
                    <td data-label="Periode">${fmtDate(b.start)} &ndash; ${fmtDate(b.end)}</td>
                    <td data-label="Urutan">
                        <span class="order">
                            <button class="btn btn-outline" onclick="move(${b.id},-1)" ${idx === 0 ? 'disabled' : ''} aria-label="Naikkan"><i class="fa-solid fa-arrow-up"></i></button>
                            <button class="btn btn-outline" onclick="move(${b.id},1)" ${idx === banners.length - 1 ? 'disabled' : ''} aria-label="Turunkan"><i class="fa-solid fa-arrow-down"></i></button>
                        </span>
                    </td>
                    <td data-label="Status"><span class="pill ${pills[b.st][0]}">${pills[b.st][1]}</span></td>
                    <td data-label="On/Off"><label class="switch"><input type="checkbox" ${b.on ? 'checked' : ''} onchange="toggle(${b.id})" aria-label="Aktifkan banner"><span class="slider"></span></label></td>
                    <td class="cell-last" data-label="Aksi">
                        <div class="actions">
                            <button class="btn btn-outline" onclick="openBanner(${b.id})" aria-label="Edit"><i class="fa-solid fa-pen"></i></button>
                            <button class="btn btn-outline" onclick="duplicate(${b.id})" aria-label="Duplikat"><i class="fa-regular fa-copy"></i></button>
                            <button class="btn btn-danger" onclick="remove(${b.id})" aria-label="Hapus"><i class="fa-solid fa-trash"></i></button>
                        </div>
                    </td>
                </tr>`;
            }).join('')
                : `<tr><td colspan="8" class="empty" data-label=""><div style="font-size:26px;margin-bottom:8px"><i class="fa-regular fa-image"></i></div>Belum ada banner di kategori ini.</td></tr>`;
        }
        const renderAll = () => { renderTable(); renderPreview(); };

        /* =============== FORM =============== */
        function buildFormOptions() {
            $('fLink').innerHTML = Object.entries(links).map(([k, v]) => `<option value="${k}">${esc(v)}</option>`).join('');
            $('fAud').innerHTML = Object.entries(audiences).map(([k, v]) => `<option value="${k}">${esc(v)}</option>`).join('');
            $('fColors').innerHTML = Object.entries(gradients).map(([k, v], i) =>
                `<label title="${v[0]}"><input type="radio" name="fColor" value="${k}" ${i === 0 ? 'checked' : ''}><span class="sw" style="background:${v[1]}"></span></label>`).join('');
            $('fIcons').innerHTML = Object.entries(icons).map(([k, v], i) =>
                `<label title="${v}"><input type="radio" name="fIcon" value="${k}" ${i === 0 ? 'checked' : ''}><span class="ic"><i class="fa-solid ${k}"></i></span></label>`).join('');
        }

        function readForm() {
            return {
                title: $('fTitle').value.trim(), sub: $('fSub').value.trim(), cta: $('fCta').value.trim(),
                link: $('fLink').value, url: $('fUrl').value.trim(),
                color: document.querySelector('input[name=fColor]:checked').value,
                icon: document.querySelector('input[name=fIcon]:checked').value,
                image: pendingImage, aud: $('fAud').value,
                app: $('fApp').checked, web: $('fWeb').checked,
                start: $('fStart').value, end: $('fEnd').value
            };
        }
        function updateFormPreview() {
            $('urlRow').style.display = $('fLink').value === 'custom' ? '' : 'none';
            $('fImgClear').style.display = pendingImage ? '' : 'none';
            const f = readForm();
            $('fPreview').innerHTML = bannerHTML(f, false) +
                `<div style="height:10px"></div>` + bannerHTML(f, true);
        }

        function openBanner(id) {
            editing = id ? banners.find(b => b.id === id) : null;
            $('bmTitle').textContent = editing ? 'Edit Banner' : 'Tambah Banner';
            const b = editing || { title: '', sub: '', cta: 'Lihat promo', link: 'register_mitra', url: '', color: 'ungu', icon: 'fa-store', image: '', aud: 'all', app: true, web: true, start: iso(today), end: shift(30) };
            $('fTitle').value = b.title; $('fSub').value = b.sub; $('fCta').value = b.cta;
            $('fLink').value = b.link; $('fUrl').value = b.url; $('fAud').value = b.aud;
            $('fApp').checked = b.app; $('fWeb').checked = b.web;
            $('fStart').value = b.start; $('fEnd').value = b.end;
            document.querySelector(`input[name=fColor][value=${b.color}]`).checked = true;
            document.querySelector(`input[name=fIcon][value="${b.icon}"]`).checked = true;
            pendingImage = b.image || ''; $('fImg').value = '';
            updateFormPreview();
            bModal.showModal();
        }

        function saveBanner() {
            const f = readForm();
            const err = (t) => Swal.fire({ icon: 'error', title: 'Periksa kembali', text: t });
            if (!f.title) return err('Judul banner wajib diisi.');
            if (f.link === 'custom' && !/^https?:\/\//i.test(f.url)) return err('Alamat tujuan harus diawali http:// atau https://');
            if (!f.app && !f.web) return err('Pilih minimal satu tempat tampil.');
            if (!f.start || !f.end) return err('Tanggal mulai dan selesai wajib diisi.');
            if (f.end < f.start) return err('Tanggal selesai tidak boleh sebelum tanggal mulai.');
            if (editing) Object.assign(editing, f);
            else banners.push({ id: nextId++, ...f, on: true });
            bModal.close(); renderAll(); toast('Banner berhasil disimpan');
        }

        /* =============== AKSI =============== */
        function toggle(id) { const b = banners.find(x => x.id === id); b.on = !b.on; renderAll(); }
        function move(id, dir) {
            const i = banners.findIndex(b => b.id === id), j = i + dir;
            if (j < 0 || j >= banners.length) return;
            [banners[i], banners[j]] = [banners[j], banners[i]]; renderAll();
        }
        function duplicate(id) {
            const i = banners.findIndex(b => b.id === id);
            banners.splice(i + 1, 0, { ...banners[i], id: nextId++, title: banners[i].title + ' (salinan)', on: false });
            renderAll(); toast('Banner diduplikasi (nonaktif)');
        }
        function remove(id) {
            Swal.fire({
                title: 'Hapus banner?', text: 'Banner yang dihapus tidak dapat dikembalikan.', icon: 'warning',
                showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus', cancelButtonText: 'Batal', reverseButtons: true
            }).then(r => { if (r.isConfirmed) { banners = banners.filter(b => b.id !== id); renderAll(); toast('Banner dihapus'); } });
        }
        function toast(t) {
            Swal.fire({ icon: 'success', title: t, timer: 2200, showConfirmButton: false, toast: true, position: 'top-end' });
        }

        /* =============== EVENT =============== */
        const bModal = $('bModal');
        buildFormOptions();
        ['fTitle', 'fSub', 'fCta', 'fUrl'].forEach(i => $(i).addEventListener('input', updateFormPreview));
        $('fLink').addEventListener('change', updateFormPreview);
        $('fColors').addEventListener('change', updateFormPreview);
        $('fIcons').addEventListener('change', updateFormPreview);
        $('fImg').addEventListener('change', (e) => {
            const file = e.target.files[0]; if (!file) return;
            if (file.size > 2 * 1024 * 1024) { e.target.value = ''; return Swal.fire({ icon: 'error', title: 'Gambar terlalu besar', text: 'Maksimal 2 MB.' }); }
            const r = new FileReader();
            r.onload = () => { pendingImage = r.result; updateFormPreview(); };
            r.readAsDataURL(file);
        });
        $('fImgClear').addEventListener('click', () => { pendingImage = ''; $('fImg').value = ''; updateFormPreview(); });
        $('pvAud').addEventListener('change', renderPreview);
        $('pvPlace').addEventListener('change', renderPreview);
        $('bTabs').addEventListener('click', (e) => {
            const a = e.target.closest('.tab'); if (!a) return; e.preventDefault();
            document.querySelectorAll('#bTabs .tab').forEach(t => t.classList.remove('active'));
            a.classList.add('active'); filter = a.dataset.f; renderTable();
        });
        renderAll();
    </script>
@endpush