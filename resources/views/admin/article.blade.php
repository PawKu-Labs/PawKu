@extends('admin.layout')
@section('title', 'Kelola Artikel')

@push('styles')
    <style>
        .tab { cursor: pointer; font-family: inherit }

        .catalog-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(min(280px, 100%), 1fr)); gap: 20px }

        .article-card {
            background: #fff; border: 1px solid var(--border); border-radius: var(--rl);
            overflow: hidden; box-shadow: var(--shadow); display: flex; flex-direction: column;
            transition: transform .2s ease, box-shadow .2s ease
        }
        .article-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-md) }

        .article-thumb { position: relative; aspect-ratio: 16 / 9; background: var(--primary-light); overflow: hidden }
        .article-thumb img { width: 100%; height: 100%; object-fit: cover; display: block }
        .article-thumb .pill { position: absolute; top: 12px; left: 12px; box-shadow: 0 4px 10px -4px rgba(0, 0, 0, .25) }

        .article-body { padding: 16px 18px 18px; display: flex; flex-direction: column; gap: 10px; flex: 1 }
        .article-body .pill { align-self: flex-start }

        .article-title {
            font-size: 14.5px; line-height: 1.4; font-weight: 700; color: var(--heading);
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden
        }
        .article-desc {
            font-size: 12.5px; line-height: 1.55; color: var(--body);
            display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden
        }

        .article-meta { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 14px; font-size: 11.5px; color: var(--muted); margin-top: auto; padding-top: 4px }
        .article-meta span { display: inline-flex; align-items: center; gap: 5px }

        .article-actions { display: flex; gap: 8px; padding: 12px 18px; border-top: 1px solid #eef0f6; background: #fafbff }
        .article-actions .btn { flex: 1 }

        .empty-state { display: none }
        .empty-state.show { display: block }

        /* Modal */
        #articleModal { max-height: 92vh; overflow-y: auto }
        .form-stack { display: flex; flex-direction: column; gap: 16px }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px }
        @media (max-width: 560px) { .form-row { grid-template-columns: 1fr } }
        textarea.input { resize: vertical; min-height: 90px; line-height: 1.5 }
        .modal-footer { display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px }

        /* Dropzone */
        .dropzone {
            position: relative; border: 2px dashed var(--border); background: #f8fafc;
            border-radius: var(--r); padding: 22px 16px; text-align: center; cursor: pointer; transition: .2s
        }
        .dropzone:hover, .dropzone.dragover { border-color: var(--primary); background: var(--primary-light) }
        .dropzone-icon {
            width: 40px; height: 40px; margin: 0 auto 10px; border-radius: 50%; background: #fff;
            color: var(--primary); display: flex; align-items: center; justify-content: center; box-shadow: var(--shadow)
        }
        .dropzone-text { font-size: 13px; font-weight: 600; color: var(--heading) }
        .dropzone-text span { color: var(--primary) }
        .dropzone-hint { font-size: 11.5px; color: var(--muted); margin-top: 4px }
        .dropzone.has-image { padding: 0; border-style: solid; overflow: hidden; cursor: default }
        .dropzone-preview { display: none; position: relative; aspect-ratio: 16 / 9 }
        .dropzone.has-image .dropzone-preview { display: block }
        .dropzone.has-image .dropzone-empty { display: none }
        .dropzone-preview img { width: 100%; height: 100%; object-fit: cover; display: block }
        .dropzone-actions { position: absolute; top: 10px; right: 10px; display: flex; gap: 8px }
        .dz-btn {
            border: none; background: rgba(255, 255, 255, .95); color: var(--heading); font-size: 12px; font-weight: 600;
            padding: 6px 12px; border-radius: 8px; cursor: pointer; box-shadow: 0 2px 6px rgba(0, 0, 0, .15)
        }
        .dz-btn.danger { color: var(--danger) }
        .dropzone-filename {
            position: absolute; left: 0; right: 0; bottom: 0; padding: 8px 12px; font-size: 11.5px; color: #fff;
            background: linear-gradient(transparent, rgba(0, 0, 0, .6)); text-align: left;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis
        }
        .dz-error { display: none; font-size: 12px; color: #cf1322; margin-top: 4px }
        .dz-error.show { display: block }
    </style>
@endpush

@section('content')
    @php
        // ------------------------------------------------------------------
        // DATA DUMMY ARTIKEL (nanti ganti dengan data dari controller/DB)
        // ------------------------------------------------------------------
        $articles = $articles ?? [
            [
                'id' => 'a1',
                'title' => '5 Tanda Kucing Kamu Sedang Stres dan Cara Mengatasinya',
                'description' => 'Kucing sering menyembunyikan rasa tidak nyaman. Kenali perubahan nafsu makan, kebiasaan grooming, dan perilaku bersembunyi sebelum masalahnya menjadi lebih serius.',
                'category' => 'Kucing',
                'author' => 'Admin PawKu',
                'status' => 'publish',
                'views' => 1284,
                'date' => '2026-10-10',
                'image' => 'https://picsum.photos/seed/pawku1/1000/600',
            ],
            [
                'id' => 'a2',
                'title' => 'Jadwal Vaksin Anjing yang Wajib Diketahui Pemilik Baru',
                'description' => 'Vaksinasi sejak dini melindungi anjing dari penyakit berbahaya. Simak kapan vaksin pertama diberikan dan seberapa sering pengulangannya dilakukan.',
                'category' => 'Anjing',
                'author' => 'drh. Sinta',
                'status' => 'publish',
                'views' => 932,
                'date' => '2026-10-07',
                'image' => 'https://picsum.photos/seed/pawku2/1000/600',
            ],
            [
                'id' => 'a3',
                'title' => 'Memilih Makanan Kering yang Tepat untuk Hewan Kesayangan',
                'description' => 'Tidak semua makanan cocok untuk setiap usia dan ukuran. Pelajari cara membaca label komposisi agar nutrisi harian hewanmu tercukupi dengan baik.',
                'category' => 'Nutrisi',
                'author' => 'Admin PawKu',
                'status' => 'publish',
                'views' => 756,
                'date' => '2026-10-03',
                'image' => 'https://picsum.photos/seed/pawku3/1000/600',
            ],
            [
                'id' => 'a4',
                'title' => 'Cara Memotong Kuku Kucing Tanpa Drama di Rumah',
                'description' => 'Memotong kuku bisa jadi momen menegangkan. Ikuti langkah sederhana dan tips membuat kucing tenang agar proses grooming kuku berjalan aman dan nyaman.',
                'category' => 'Perawatan',
                'author' => 'Rina Kusuma',
                'status' => 'draft',
                'views' => 0,
                'date' => '2026-10-12',
                'image' => 'https://picsum.photos/seed/pawku4/1000/600',
            ],
            [
                'id' => 'a5',
                'title' => 'Mengenal Gejala Awal Kutu dan Jamur pada Kulit Hewan',
                'description' => 'Garukan berlebihan dan bulu rontok bisa jadi tanda masalah kulit. Kenali gejala awal kutu dan jamur serta langkah penanganan pertama sebelum ke dokter hewan.',
                'category' => 'Kesehatan',
                'author' => 'drh. Sinta',
                'status' => 'publish',
                'views' => 511,
                'date' => '2026-09-28',
                'image' => 'https://picsum.photos/seed/pawku5/1000/600',
            ],
            [
                'id' => 'a6',
                'title' => 'Tips Membawa Anjing Bepergian Naik Mobil dengan Aman',
                'description' => 'Perjalanan jauh bisa membuat anjing mabuk atau cemas. Siapkan perlengkapan, atur jeda istirahat, dan biasakan anjing sebelum hari keberangkatan.',
                'category' => 'Anjing',
                'author' => 'Admin PawKu',
                'status' => 'draft',
                'views' => 0,
                'date' => '2026-10-13',
                'image' => 'https://picsum.photos/seed/pawku6/1000/600',
            ],
        ];

        $bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $fmtDate = function ($d) use ($bulan) {
            $t = strtotime($d);
            return date('j', $t) . ' ' . $bulan[(int) date('n', $t) - 1] . ' ' . date('Y', $t);
        };

        $collection = collect($articles);
        $totalArtikel = $collection->count();
        $totalPublish = $collection->where('status', 'publish')->count();
        $totalDraft = $collection->where('status', 'draft')->count();
        $totalViews = $collection->sum('views');
        $categories = $collection->pluck('category')->unique()->values();
    @endphp

    <div class="hero">
        <div>
            <h1 class="page-title">Kelola Artikel</h1>
            <p class="page-sub">Atur artikel edukasi yang tampil di Beranda aplikasi PawKu.</p>
        </div>
        <div>
            <button type="button" class="btn" onclick="openModal()">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                    stroke-linecap="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Tambah Artikel
            </button>
        </div>
    </div>

    <section>
        <div class="group-title">Ringkasan</div>
        <div class="grid g4">
            <div class="card stat a-blue">
                <div class="stat-top">
                    <span class="stat-label">Total Artikel</span>
                    <div class="stat-icon i-blue">📄</div>
                </div>
                <div class="stat-value">{{ $totalArtikel }}</div>
            </div>

            <div class="card stat a-green">
                <div class="stat-top">
                    <span class="stat-label">Terbit</span>
                    <div class="stat-icon i-green">✔</div>
                </div>
                <div class="stat-value">{{ $totalPublish }}</div>
            </div>

            <div class="card stat a-gold">
                <div class="stat-top">
                    <span class="stat-label">Draf</span>
                    <div class="stat-icon i-gold">✎</div>
                </div>
                <div class="stat-value">{{ $totalDraft }}</div>
            </div>

            <div class="card stat a-purple">
                <div class="stat-top">
                    <span class="stat-label">Total Dibaca</span>
                    <div class="stat-icon i-purple">👁</div>
                </div>
                <div class="stat-value">{{ number_format($totalViews, 0, ',', '.') }}</div>
            </div>
        </div>
    </section>

    <div class="tabs" id="statusTabs">
        <button type="button" class="tab active" data-status="all">Semua <span class="n">({{ $totalArtikel }})</span></button>
        <button type="button" class="tab" data-status="publish">Terbit <span class="n">({{ $totalPublish }})</span></button>
        <button type="button" class="tab" data-status="draft">Draf <span class="n">({{ $totalDraft }})</span></button>
    </div>

    <div class="card filters">
        <div class="field grow">
            <label for="articleSearch">Cari</label>
            <input class="input" id="articleSearch" type="search" placeholder="Cari judul artikel...">
        </div>
        <div class="field">
            <label for="categoryFilter">Kategori</label>
            <select class="input" id="categoryFilter">
                <option value="all">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ strtolower($cat) }}">{{ $cat }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="catalog-grid" id="catalogGrid">
        @foreach($articles as $a)
            <article class="article-card" data-status="{{ $a['status'] }}"
                data-category="{{ strtolower($a['category']) }}" data-title="{{ strtolower($a['title']) }}">
                <div class="article-thumb">
                    <img src="{{ $a['image'] }}" alt="{{ $a['title'] }}" loading="lazy">
                    <span class="pill {{ $a['status'] === 'publish' ? 'p-approved' : 'p-pending' }}">
                        {{ $a['status'] === 'publish' ? 'Terbit' : 'Draf' }}
                    </span>
                </div>

                <div class="article-body">
                    <span class="pill c-user">{{ $a['category'] }}</span>
                    <h3 class="article-title">{{ $a['title'] }}</h3>
                    <p class="article-desc">{{ $a['description'] }}</p>

                    <div class="article-meta">
                        <span>📅 {{ $fmtDate($a['date']) }}</span>
                        <span>👤 {{ $a['author'] }}</span>
                        <span>👁 {{ number_format($a['views'], 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="article-actions">
                    <button type="button" class="btn btn-outline" data-article="{{ json_encode($a) }}"
                        onclick="openModal(JSON.parse(this.dataset.article))">Edit</button>
                    <button type="button" class="btn btn-danger" data-title="{{ $a['title'] }}"
                        onclick="confirmDelete(this)">Hapus</button>
                </div>
            </article>
        @endforeach
    </div>

    <div class="card empty empty-state" id="emptyState">
        <div style="font-size:32px;margin-bottom:8px;">📰</div>
        <div>Tidak ada artikel yang cocok dengan filter atau pencarian Anda.</div>
    </div>

    <!-- Modal Tambah / Edit (dummy, belum tersambung ke database) -->
    <dialog id="articleModal">
        <div class="modal-header">
            <div class="modal-title" id="modalTitle">Tambah Artikel</div>
            <button class="modal-close" type="button" aria-label="Tutup" onclick="closeModal()">&times;</button>
        </div>

        <div class="form-stack">
            <div class="field">
                <label for="fTitle">Judul</label>
                <input type="text" id="fTitle" class="input" placeholder="Judul artikel">
            </div>

            <div class="form-row">
                <div class="field">
                    <label for="fCategory">Kategori</label>
                    <select id="fCategory" class="input">
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="fStatus">Status</label>
                    <select id="fStatus" class="input">
                        <option value="publish">Terbit</option>
                        <option value="draft">Draf</option>
                    </select>
                </div>
            </div>

            <div class="field">
                <label>Gambar Artikel</label>
                <input type="file" id="fImage" accept="image/png,image/jpeg,image/webp" hidden>
                <div class="dropzone" id="dropzone" tabindex="0" role="button" aria-label="Unggah gambar artikel">
                    <div class="dropzone-empty">
                        <div class="dropzone-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="17 8 12 3 7 8"></polyline>
                                <line x1="12" y1="3" x2="12" y2="15"></line>
                            </svg>
                        </div>
                        <div class="dropzone-text">Tarik &amp; lepas gambar di sini, atau <span>pilih file</span></div>
                        <div class="dropzone-hint">JPG, PNG, atau WEBP. Maksimal 2 MB. Rasio 16:9 disarankan.</div>
                    </div>
                    <div class="dropzone-preview">
                        <img id="dzImg" src="" alt="Pratinjau gambar">
                        <div class="dropzone-actions">
                            <button type="button" class="dz-btn" id="dzChange">Ganti</button>
                            <button type="button" class="dz-btn danger" id="dzRemove">Hapus</button>
                        </div>
                        <div class="dropzone-filename" id="dzName"></div>
                    </div>
                </div>
                <div class="dz-error" id="dzError" role="alert"></div>
            </div>

            <div class="field">
                <label for="fDesc">Deskripsi Singkat</label>
                <textarea id="fDesc" class="input" placeholder="Ringkasan untuk kartu di Beranda"></textarea>
            </div>

            <div class="field">
                <label for="fContent">Isi Artikel</label>
                <textarea id="fContent" class="input" style="min-height:140px;"
                    placeholder="Pisahkan antar paragraf dengan baris kosong"></textarea>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-outline" onclick="closeModal()">Batal</button>
            <button type="button" class="btn btn-primary" onclick="saveArticle()">Simpan</button>
        </div>
    </dialog>

    @push('scripts')
        <script>
            // ----- Filter & pencarian (client-side) -----
            let activeStatus = 'all';
            const cards = () => document.querySelectorAll('#catalogGrid .article-card');

            function applyFilter() {
                const cat = document.getElementById('categoryFilter').value;
                const q = document.getElementById('articleSearch').value.trim().toLowerCase();
                let visible = 0;

                cards().forEach(card => {
                    const okStatus = activeStatus === 'all' || card.dataset.status === activeStatus;
                    const okCat = cat === 'all' || card.dataset.category === cat;
                    const okSearch = !q || card.dataset.title.includes(q);
                    const show = okStatus && okCat && okSearch;
                    card.style.display = show ? '' : 'none';
                    if (show) visible++;
                });

                document.getElementById('emptyState').classList.toggle('show', visible === 0);
            }

            document.querySelectorAll('#statusTabs .tab').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.querySelectorAll('#statusTabs .tab').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    activeStatus = btn.dataset.status;
                    applyFilter();
                });
            });
            document.getElementById('categoryFilter').addEventListener('change', applyFilter);
            document.getElementById('articleSearch').addEventListener('input', applyFilter);

            // ----- Hapus (dummy: hanya menghapus kartu dari tampilan) -----
            function confirmDelete(btn) {
                Swal.fire({
                    title: 'Hapus artikel?',
                    text: '"' + btn.dataset.title + '" akan dihapus dari daftar.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        btn.closest('.article-card').remove();
                        applyFilter();
                        Swal.fire({ icon: 'success', title: 'Terhapus', text: 'Artikel berhasil dihapus (dummy).', timer: 2200, showConfirmButton: false, toast: true, position: 'top-end' });
                    }
                });
            }

            // ----- Dropzone upload gambar (dummy: hanya pratinjau lokal) -----
            const dropzone = document.getElementById('dropzone');
            const fileInput = document.getElementById('fImage');
            const dzImg = document.getElementById('dzImg');
            const dzName = document.getElementById('dzName');
            const dzError = document.getElementById('dzError');
            const MAX_SIZE = 2 * 1024 * 1024;
            const ALLOWED = ['image/jpeg', 'image/png', 'image/webp'];
            let objectUrl = null;

            function showDzError(msg) {
                dzError.textContent = msg || '';
                dzError.classList.toggle('show', !!msg);
            }

            function setPreview(src, name) {
                if (objectUrl && src !== objectUrl) {
                    URL.revokeObjectURL(objectUrl);
                    objectUrl = null;
                }
                if (src) {
                    dzImg.src = src;
                    dzName.textContent = name || '';
                    dropzone.classList.add('has-image');
                } else {
                    dzImg.removeAttribute('src');
                    dzName.textContent = '';
                    fileInput.value = '';
                    dropzone.classList.remove('has-image');
                }
            }

            function handleFile(file) {
                if (!file) return;
                showDzError('');
                if (!ALLOWED.includes(file.type)) {
                    showDzError('Format tidak didukung. Gunakan gambar JPG, PNG, atau WEBP.');
                    return;
                }
                if (file.size > MAX_SIZE) {
                    showDzError('Ukuran gambar terlalu besar. Maksimal 2 MB.');
                    return;
                }
                const url = URL.createObjectURL(file);
                setPreview(url, file.name + ' · ' + (file.size / 1024).toFixed(0) + ' KB');
                objectUrl = url;
            }

            dropzone.addEventListener('click', () => {
                if (!dropzone.classList.contains('has-image')) fileInput.click();
            });
            dropzone.addEventListener('keydown', e => {
                if ((e.key === 'Enter' || e.key === ' ') && !dropzone.classList.contains('has-image')) {
                    e.preventDefault();
                    fileInput.click();
                }
            });
            document.getElementById('dzChange').addEventListener('click', e => { e.stopPropagation(); fileInput.click(); });
            document.getElementById('dzRemove').addEventListener('click', e => { e.stopPropagation(); showDzError(''); setPreview(''); });
            fileInput.addEventListener('change', () => handleFile(fileInput.files[0]));

            ['dragenter', 'dragover'].forEach(ev => dropzone.addEventListener(ev, e => {
                e.preventDefault();
                dropzone.classList.add('dragover');
            }));
            ['dragleave', 'drop'].forEach(ev => dropzone.addEventListener(ev, e => {
                e.preventDefault();
                dropzone.classList.remove('dragover');
            }));
            dropzone.addEventListener('drop', e => {
                const file = e.dataTransfer.files[0];
                if (!file) return;
                const dt = new DataTransfer();
                dt.items.add(file);
                fileInput.files = dt.files;
                handleFile(file);
            });

            // Cegah browser membuka file jika dilepas di luar dropzone
            ['dragover', 'drop'].forEach(ev => window.addEventListener(ev, e => {
                if (!dropzone.contains(e.target)) e.preventDefault();
            }));

            // ----- Modal tambah / edit -----
            const modal = document.getElementById('articleModal');

            function openModal(article) {
                const isEdit = !!article;
                document.getElementById('modalTitle').textContent = isEdit ? 'Edit Artikel' : 'Tambah Artikel';
                document.getElementById('fTitle').value = isEdit ? article.title : '';
                document.getElementById('fCategory').value = isEdit ? article.category : document.getElementById('fCategory').options[0].value;
                document.getElementById('fStatus').value = isEdit ? article.status : 'publish';
                showDzError('');
                setPreview(isEdit ? article.image : '', isEdit ? 'Gambar saat ini' : '');
                document.getElementById('fDesc').value = isEdit ? article.description : '';
                document.getElementById('fContent').value = '';
                modal.showModal();
            }

            function closeModal() {
                modal.close();
            }

            // Tutup saat klik area gelap di luar dialog
            modal.addEventListener('click', e => {
                const r = modal.getBoundingClientRect();
                const inside = e.clientX >= r.left && e.clientX <= r.right && e.clientY >= r.top && e.clientY <= r.bottom;
                if (!inside) closeModal();
            });

            function saveArticle() {
                closeModal();
                Swal.fire({
                    title: 'Tersimpan (dummy)',
                    text: 'Data belum disimpan ke database. Hubungkan ke controller nanti.',
                    icon: 'info',
                    confirmButtonColor: '#1677ff',
                });
            }
        </script>
    @endpush
@endsection