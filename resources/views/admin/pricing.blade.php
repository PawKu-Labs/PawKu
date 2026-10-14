@extends('admin.layout')
@section('title', 'Harga & Promo')

@push('styles')
    <style>
        .plan-card {
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            gap: 14px
        }

        .plan-card::before {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            height: 5px;
            background: var(--accent, var(--grad-brand))
        }

        .plan-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            margin-top: 4px
        }

        .plan-name {
            font-size: 17px;
            font-weight: 800;
            color: var(--heading)
        }

        .plan-desc {
            font-size: 12.5px;
            color: var(--muted);
            margin-top: 4px;
            line-height: 1.5
        }

        .plan-type {
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            background: var(--primary-light);
            color: #B45309;
            white-space: nowrap
        }

        .price-box {
            background: #faf6f1;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 14px 16px
        }

        .price-box+.price-box {
            margin-top: 10px
        }

        .price-label {
            font-size: 11.5px;
            font-weight: 600;
            color: var(--muted)
        }

        .price-row {
            display: flex;
            align-items: baseline;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 4px
        }

        .price-final {
            font-size: 26px;
            font-weight: 800;
            color: var(--heading);
            line-height: 1.1
        }

        .price-final small {
            font-size: 12px;
            font-weight: 600;
            color: var(--muted)
        }

        .price-old {
            font-size: 14px;
            color: var(--muted);
            text-decoration: line-through
        }

        .price-tag {
            font-size: 11px;
            font-weight: 700;
            background: var(--grad-rose);
            color: #fff;
            padding: 2px 8px;
            border-radius: 20px
        }

        .plan-features {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 8px;
            font-size: 13px;
            color: var(--body)
        }

        .plan-features i {
            color: var(--green);
            margin-right: 8px
        }

        .plan-foot {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: auto
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

        .p-scheduled {
            background: #e0f2fe;
            color: #0369a1
        }

        .p-ended {
            background: #f3efea;
            color: #5b5047
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

        .disc {
            font-weight: 800;
            color: var(--danger)
        }

        /* switch */
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

        /* form */
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

        .checks {
            display: flex;
            flex-direction: column;
            gap: 8px;
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

        .modal-foot {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            margin-top: 18px;
            flex-wrap: wrap
        }

        .hint {
            font-size: 11.5px;
            color: var(--muted)
        }

        .preview {
            margin-top: 12px;
            padding: 10px 14px;
            border-radius: 10px;
            background: var(--green-light);
            color: #065f46;
            font-size: 12.5px;
            font-weight: 600
        }

        .seg {
            display: flex;
            gap: 6px
        }

        .seg label {
            flex: 1;
            text-align: center;
            padding: 9px;
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            color: var(--body)
        }

        .seg input {
            display: none
        }

        .seg input:checked+span {
            color: #fff
        }

        .seg label:has(input:checked) {
            background: var(--grad-brand);
            color: #fff;
            border-color: transparent
        }
    </style>
@endpush

@section('content')
    <div class="hero">
        <div>
            <h1 class="page-title">Harga &amp; Promo</h1>
            <p class="page-sub">Atur harga paket monetisasi PawKu dan kelola promo. Harga akhir otomatis dihitung dari
                promo yang sedang aktif.</p>
        </div>
        <div>
            <button type="button" class="btn" onclick="openPromo()">
                <i class="fa-solid fa-plus"></i> Tambah Promo
            </button>
        </div>
    </div>

    {{-- ====== KATALOG PAKET ====== --}}
    <section>
        <div class="section-head">
            <div class="group-title">Katalog Paket</div>
            <span class="hint">Data dummy &middot; perubahan belum tersimpan ke database</span>
        </div>
        <div class="grid g2" id="plans"></div>
    </section>

    {{-- ====== RINGKASAN PROMO ====== --}}
    <section>
        <div class="grid g3">
            <div class="card stat a-green">
                <div class="stat-top"><span class="stat-label">Promo Aktif</span>
                    <div class="stat-icon i-green"><i class="fa-solid fa-bolt"></i></div>
                </div>
                <div class="stat-value" id="sActive">0</div>
                <div class="sub">Sedang berlaku hari ini</div>
            </div>
            <div class="card stat a-blue">
                <div class="stat-top"><span class="stat-label">Dijadwalkan</span>
                    <div class="stat-icon i-blue"><i class="fa-solid fa-calendar-days"></i></div>
                </div>
                <div class="stat-value" id="sSched">0</div>
                <div class="sub">Akan berlaku di kemudian hari</div>
            </div>
            <div class="card stat a-gold">
                <div class="stat-top"><span class="stat-label">Berakhir / Nonaktif</span>
                    <div class="stat-icon i-gold"><i class="fa-solid fa-clock-rotate-left"></i></div>
                </div>
                <div class="stat-value" id="sEnded">0</div>
                <div class="sub">Sudah lewat atau dimatikan</div>
            </div>
        </div>
    </section>

    {{-- ====== TABEL PROMO ====== --}}
    <section>
        <div class="section-head">
            <div class="group-title">Daftar Promo</div>
            <div class="tabs" id="promoTabs">
                <a href="#" class="tab active" data-f="all">Semua</a>
                <a href="#" class="tab" data-f="active">Aktif</a>
                <a href="#" class="tab" data-f="scheduled">Dijadwalkan</a>
                <a href="#" class="tab" data-f="ended">Berakhir</a>
            </div>
        </div>
        <div class="card" style="padding:0;">
            <div class="table-wrap">
                <table class="responsive">
                    <thead>
                        <tr>
                            <th class="cell-first">Nama Promo</th>
                            <th>Diskon</th>
                            <th>Berlaku di Paket</th>
                            <th>Mulai</th>
                            <th>Sampai</th>
                            <th>Status</th>
                            <th>On/Off</th>
                            <th class="cell-last">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="promoBody"></tbody>
                </table>
            </div>
        </div>
    </section>

    {{-- ====== MODAL EDIT HARGA ====== --}}
    <dialog id="priceModal">
        <div class="modal-header">
            <div class="modal-title" id="pmTitle">Edit Harga</div>
            <button type="button" class="modal-close" onclick="priceModal.close()" aria-label="Tutup">&times;</button>
        </div>
        <div class="form-grid" id="pmFields"></div>
        <div class="modal-foot">
            <button type="button" class="btn btn-outline" onclick="priceModal.close()">Batal</button>
            <button type="button" class="btn btn-primary" onclick="savePrice()">Simpan Harga</button>
        </div>
    </dialog>

    {{-- ====== MODAL PROMO ====== --}}
    <dialog id="promoModal">
        <div class="modal-header">
            <div class="modal-title" id="prTitle">Tambah Promo</div>
            <button type="button" class="modal-close" onclick="promoModal.close()" aria-label="Tutup">&times;</button>
        </div>
        <div class="form-grid">
            <div class="field full">
                <label for="prName">Nama Promo</label>
                <input class="input" id="prName" placeholder="Contoh: Promo Ultah PawKu">
            </div>
            <div class="field full">
                <label>Tipe Diskon</label>
                <div class="seg">
                    <label><input type="radio" name="prType" value="percent" checked><span>Persen (%)</span></label>
                    <label><input type="radio" name="prType" value="nominal"><span>Nominal (Rp)</span></label>
                </div>
            </div>
            <div class="field full">
                <label for="prValue" id="prValueLbl">Besar Diskon (%)</label>
                <input class="input" id="prValue" type="number" min="1" placeholder="10">
            </div>
            <div class="field">
                <label for="prStart">Mulai</label>
                <input class="input" id="prStart" type="date">
            </div>
            <div class="field">
                <label for="prEnd">Sampai</label>
                <input class="input" id="prEnd" type="date">
            </div>
            <div class="field full">
                <label>Diterapkan di Paket</label>
                <div class="checks" id="prTargets"></div>
            </div>
        </div>
        <div class="preview" id="prPreview" style="display:none;"></div>
        <div class="modal-foot">
            <button type="button" class="btn btn-outline" onclick="promoModal.close()">Batal</button>
            <button type="button" class="btn btn-primary" onclick="savePromo()">Simpan Promo</button>
        </div>
    </dialog>
@endsection

@push('scripts')
    <script>
        /* =============== DATA DUMMY =============== */
        // Setiap "target" = satu harga yang bisa dikenai promo
        const plans = [
            {
                id: 'cat', name: 'Profil Kucing Unlimited', icon: 'fa-cat', accent: 'var(--grad-sky)',
                type: 'Sekali Bayar · Selamanya',
                desc: 'Daftarkan profil kucing tanpa batas. Cukup bayar sekali, berlaku seumur hidup akun.',
                prices: [{ key: 'cat_lifetime', label: 'Harga Lifetime', unit: 'sekali bayar', value: 99000 }],
                features: ['Profil kucing unlimited', 'Berlaku selamanya', 'Tanpa biaya perpanjangan']
            },
            {
                id: 'mitra', name: 'Mitra Booking Online', icon: 'fa-store', accent: 'var(--grad-green)',
                type: 'Langganan · Bulanan / Tahunan',
                desc: 'Aktifkan fitur booking online untuk petshop mitra. Pilih bayar bulanan atau hemat tahunan.',
                prices: [
                    { key: 'mitra_year', label: 'Harga Tahunan', unit: '/ tahun', value: 120000 },
                    { key: 'mitra_month', label: 'Harga Bulanan', unit: '/ bulan', value: 10000 }
                ],
                features: ['Booking online pelanggan', 'Manajemen jadwal layanan', 'Notifikasi booking masuk']
            }
        ];

        const targets = [
            { key: 'cat_lifetime', label: 'Profil Kucing Unlimited' },
            { key: 'mitra_year', label: 'Mitra Booking – Tahunan' },
            { key: 'mitra_month', label: 'Mitra Booking – Bulanan' }
        ];

        const today = new Date(); today.setHours(0, 0, 0, 0);
        const iso = (d) => new Date(d.getTime() - d.getTimezoneOffset() * 60000).toISOString().slice(0, 10);
        const shift = (n) => { const d = new Date(today); d.setDate(d.getDate() + n); return iso(d); };

        let promos = [
            { id: 1, name: 'Promo Ultah PawKu', type: 'percent', value: 10, start: shift(-5), end: shift(20), targets: ['cat_lifetime', 'mitra_year', 'mitra_month'], on: true },
            { id: 2, name: 'Diskon Mitra Baru', type: 'nominal', value: 20000, start: shift(-10), end: shift(40), targets: ['mitra_year'], on: true },
            { id: 3, name: 'Promo Akhir Tahun', type: 'percent', value: 25, start: shift(50), end: shift(80), targets: ['cat_lifetime', 'mitra_year'], on: true },
            { id: 4, name: 'Flash Sale Kucing', type: 'percent', value: 15, start: shift(-40), end: shift(-20), targets: ['cat_lifetime'], on: true },
            { id: 5, name: 'Promo Ramadan', type: 'percent', value: 20, start: shift(-8), end: shift(15), targets: ['mitra_month'], on: false }
        ];
        let nextId = 6, editingPromo = null, editingPlan = null, filter = 'all';

        /* =============== HELPER =============== */
        const $ = (id) => document.getElementById(id);
        const rp = (n) => 'Rp' + Math.round(n).toLocaleString('id-ID');
        const fmtDate = (s) => new Date(s + 'T00:00:00').toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
        const esc = (s) => String(s).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
        const priceOf = (key) => { for (const p of plans) for (const x of p.prices) if (x.key === key) return x; };
        const label = (key) => targets.find(t => t.key === key)?.label || key;

        function statusOf(p) {
            if (!p.on) return 'ended';
            const s = new Date(p.start + 'T00:00:00'), e = new Date(p.end + 'T00:00:00');
            if (today < s) return 'scheduled';
            if (today > e) return 'ended';
            return 'active';
        }
        const statusPill = { active: ['p-active', 'Aktif'], scheduled: ['p-scheduled', 'Dijadwalkan'], ended: ['p-ended', 'Berakhir'] };
        const discText = (p) => p.type === 'percent' ? `-${p.value}%` : `-${rp(p.value)}`;
        const applyDisc = (price, p) => Math.max(0, p.type === 'percent' ? price * (1 - p.value / 100) : price - p.value);

        // Hitung harga akhir: promo aktif yang berlaku, ambil yang memberi harga terendah (tidak ditumpuk)
        function finalPrice(key) {
            const base = priceOf(key).value;
            let best = base, used = null;
            promos.filter(p => statusOf(p) === 'active' && p.targets.includes(key)).forEach(p => {
                const v = applyDisc(base, p);
                if (v < best) { best = v; used = p; }
            });
            return { base, final: best, promo: used };
        }

        /* =============== RENDER KATALOG =============== */
        function renderPlans() {
            $('plans').innerHTML = plans.map(pl => `
                <div class="card plan-card" style="--accent:${pl.accent}">
                    <div class="plan-head">
                        <div>
                            <div class="plan-name"><i class="fa-solid ${pl.icon}" style="color:var(--primary);margin-right:8px"></i>${esc(pl.name)}</div>
                            <div class="plan-desc">${esc(pl.desc)}</div>
                        </div>
                        <span class="plan-type">${esc(pl.type)}</span>
                    </div>
                    <div>
                        ${pl.prices.map(x => {
                const f = finalPrice(x.key);
                return `<div class="price-box">
                                <div class="price-label">${esc(x.label)}</div>
                                <div class="price-row">
                                    <span class="price-final">${rp(f.final)} <small>${esc(x.unit)}</small></span>
                                    ${f.promo ? `<span class="price-old">${rp(f.base)}</span><span class="price-tag">${esc(f.promo.name)} ${discText(f.promo)}</span>` : ''}
                                </div>
                            </div>`;
            }).join('')}
                    </div>
                    <ul class="plan-features">${pl.features.map(f => `<li><i class="fa-solid fa-circle-check"></i>${esc(f)}</li>`).join('')}</ul>
                    <div class="plan-foot">
                        <button class="btn btn-primary" onclick="openPrice('${pl.id}')"><i class="fa-solid fa-pen"></i> Edit Harga</button>
                        <button class="btn btn-outline" onclick="openPromo(null,'${pl.prices[0].key}')"><i class="fa-solid fa-tag"></i> Buat Promo</button>
                    </div>
                </div>`).join('');
        }

        /* =============== RENDER PROMO =============== */
        function renderPromos() {
            const all = promos.map(p => ({ ...p, st: statusOf(p) }));
            $('sActive').textContent = all.filter(p => p.st === 'active').length;
            $('sSched').textContent = all.filter(p => p.st === 'scheduled').length;
            $('sEnded').textContent = all.filter(p => p.st === 'ended').length;

            const rows = filter === 'all' ? all : all.filter(p => p.st === filter);
            $('promoBody').innerHTML = rows.length ? rows.map(p => `
                <tr>
                    <td class="cell-first" data-label="Promo"><strong>${esc(p.name)}</strong></td>
                    <td data-label="Diskon"><span class="disc">${discText(p)}</span></td>
                    <td data-label="Paket" class="stack">${p.targets.map(k => `<span class="chip">${esc(label(k))}</span>`).join('')}</td>
                    <td data-label="Mulai">${fmtDate(p.start)}</td>
                    <td data-label="Sampai">${fmtDate(p.end)}</td>
                    <td data-label="Status"><span class="pill ${statusPill[p.st][0]}">${statusPill[p.st][1]}</span></td>
                    <td data-label="On/Off">
                        <label class="switch"><input type="checkbox" ${p.on ? 'checked' : ''} onchange="togglePromo(${p.id})" aria-label="Aktifkan promo"><span class="slider"></span></label>
                    </td>
                    <td class="cell-last" data-label="Aksi">
                        <div class="actions">
                            <button class="btn btn-outline" onclick="openPromo(${p.id})"><i class="fa-solid fa-pen"></i></button>
                            <button class="btn btn-danger" onclick="deletePromo(${p.id})"><i class="fa-solid fa-trash"></i></button>
                        </div>
                    </td>
                </tr>`).join('')
                : `<tr><td colspan="8" class="empty" data-label=""><div style="font-size:26px;margin-bottom:8px"><i class="fa-solid fa-tags"></i></div>Belum ada promo di kategori ini.</td></tr>`;
        }

        const renderAll = () => { renderPlans(); renderPromos(); };

        /* =============== EDIT HARGA =============== */
        function openPrice(id) {
            editingPlan = plans.find(p => p.id === id);
            $('pmTitle').textContent = 'Edit Harga – ' + editingPlan.name;
            $('pmFields').innerHTML = editingPlan.prices.map(x => `
                <div class="field full">
                    <label for="pf_${x.key}">${esc(x.label)} (Rp ${esc(x.unit)})</label>
                    <input class="input" id="pf_${x.key}" type="number" min="0" step="1000" value="${x.value}">
                </div>`).join('') + `<div class="full hint">Harga dasar sebelum promo. Promo aktif akan otomatis mengurangi harga ini.</div>`;
            priceModal.showModal();
        }
        function savePrice() {
            for (const x of editingPlan.prices) {
                const v = parseInt($('pf_' + x.key).value, 10);
                if (isNaN(v) || v < 0) return Swal.fire({ icon: 'error', title: 'Harga tidak valid', text: 'Masukkan angka 0 atau lebih.' });
                x.value = v;
            }
            priceModal.close(); renderAll(); toast('Harga berhasil diperbarui');
        }

        /* =============== PROMO CRUD =============== */
        function openPromo(id, presetKey) {
            editingPromo = id ? promos.find(p => p.id === id) : null;
            $('prTitle').textContent = editingPromo ? 'Edit Promo' : 'Tambah Promo';
            const p = editingPromo || { name: '', type: 'percent', value: '', start: iso(today), end: shift(30), targets: presetKey ? [presetKey] : [] };
            $('prName').value = p.name; $('prValue').value = p.value;
            $('prStart').value = p.start; $('prEnd').value = p.end;
            document.querySelector(`input[name=prType][value=${p.type}]`).checked = true;
            $('prTargets').innerHTML = targets.map(t => `
                <label><input type="checkbox" value="${t.key}" ${p.targets.includes(t.key) ? 'checked' : ''}> ${esc(t.label)}</label>`).join('');
            syncType(); updatePreview();
            promoModal.showModal();
        }
        function syncType() {
            const pct = document.querySelector('input[name=prType]:checked').value === 'percent';
            $('prValueLbl').textContent = pct ? 'Besar Diskon (%)' : 'Besar Diskon (Rp)';
            $('prValue').placeholder = pct ? '10' : '20000';
        }
        function updatePreview() {
            const type = document.querySelector('input[name=prType]:checked').value;
            const val = parseFloat($('prValue').value);
            const keys = [...document.querySelectorAll('#prTargets input:checked')].map(i => i.value);
            const box = $('prPreview');
            if (!val || !keys.length) { box.style.display = 'none'; return; }
            box.style.display = 'block';
            box.innerHTML = '<i class="fa-solid fa-eye"></i> Simulasi: ' + keys.map(k => {
                const base = priceOf(k).value;
                return `${esc(label(k))}: ${rp(base)} → <u>${rp(applyDisc(base, { type, value: val }))}</u>`;
            }).join(' &nbsp;|&nbsp; ');
        }
        function savePromo() {
            const name = $('prName').value.trim();
            const type = document.querySelector('input[name=prType]:checked').value;
            const value = parseFloat($('prValue').value);
            const start = $('prStart').value, end = $('prEnd').value;
            const keys = [...document.querySelectorAll('#prTargets input:checked')].map(i => i.value);
            const err = (t) => Swal.fire({ icon: 'error', title: 'Periksa kembali', text: t });
            if (!name) return err('Nama promo wajib diisi.');
            if (!value || value <= 0) return err('Besar diskon harus lebih dari 0.');
            if (type === 'percent' && value > 100) return err('Diskon persen maksimal 100%.');
            if (!start || !end) return err('Tanggal mulai dan sampai wajib diisi.');
            if (end < start) return err('Tanggal selesai tidak boleh sebelum tanggal mulai.');
            if (!keys.length) return err('Pilih minimal satu paket.');

            if (editingPromo) Object.assign(editingPromo, { name, type, value, start, end, targets: keys });
            else promos.unshift({ id: nextId++, name, type, value, start, end, targets: keys, on: true });
            promoModal.close(); renderAll(); toast('Promo berhasil disimpan');
        }
        function togglePromo(id) {
            const p = promos.find(x => x.id === id); p.on = !p.on; renderAll();
        }
        function deletePromo(id) {
            Swal.fire({
                title: 'Hapus promo?', text: 'Promo yang dihapus tidak dapat dikembalikan.', icon: 'warning',
                showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus', cancelButtonText: 'Batal', reverseButtons: true
            }).then(r => { if (r.isConfirmed) { promos = promos.filter(p => p.id !== id); renderAll(); toast('Promo dihapus'); } });
        }
        function toast(t) {
            Swal.fire({ icon: 'success', title: t, timer: 2200, showConfirmButton: false, toast: true, position: 'top-end' });
        }

        /* =============== EVENT =============== */
        document.querySelectorAll('input[name=prType]').forEach(r => r.addEventListener('change', () => { syncType(); updatePreview(); }));
        $('prValue').addEventListener('input', updatePreview);
        $('prTargets').addEventListener('change', updatePreview);
        $('promoTabs').addEventListener('click', (e) => {
            const a = e.target.closest('.tab'); if (!a) return; e.preventDefault();
            document.querySelectorAll('#promoTabs .tab').forEach(t => t.classList.remove('active'));
            a.classList.add('active'); filter = a.dataset.f; renderPromos();
        });

        const priceModal = $('priceModal'), promoModal = $('promoModal');
        renderAll();
    </script>
@endpush