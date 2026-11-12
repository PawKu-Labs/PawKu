@extends('admin.layout')
@section('title', 'Kelola Artikel')

@push('styles')
    <style>
        .tab {
            cursor: pointer;
            font-family: inherit
        }

        .catalog-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(min(280px, 100%), 1fr));
            gap: 20px
        }

        .article-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: var(--rl);
            overflow: hidden;
            box-shadow: var(--shadow);
            display: flex;
            flex-direction: column;
            transition: transform .2s ease, box-shadow .2s ease
        }

        .article-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md)
        }

        .article-thumb {
            position: relative;
            aspect-ratio: 16 / 9;
            background: var(--primary-light);
            overflow: hidden
        }

        .article-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block
        }

        .article-thumb .pill {
            position: absolute;
            top: 12px;
            left: 12px;
            box-shadow: 0 4px 10px -4px rgba(0, 0, 0, .25)
        }

        .article-body {
            padding: 16px 18px 18px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            flex: 1
        }

        .article-body .pill {
            align-self: flex-start
        }

        .article-title {
            font-size: 14.5px;
            line-height: 1.4;
            font-weight: 700;
            color: var(--heading);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden
        }

        .article-desc {
            font-size: 12.5px;
            line-height: 1.55;
            color: var(--body);
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden
        }

        .article-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 6px 14px;
            font-size: 11.5px;
            color: var(--muted);
            margin-top: auto;
            padding-top: 4px
        }

        .article-meta span {
            display: inline-flex;
            align-items: center;
            gap: 5px
        }

        .article-actions {
            display: flex;
            gap: 8px;
            padding: 12px 18px;
            border-top: 1px solid #eef0f6;
            background: #fafbff
        }

        .article-actions .btn {
            flex: 1
        }

        .empty-state {
            display: none
        }

        .empty-state.show {
            display: block
        }

        /* Modal */
        #articleModal {
            max-height: 92vh;
            overflow-y: auto
        }

        .form-stack {
            display: flex;
            flex-direction: column;
            gap: 16px
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px
        }

        @media (max-width: 560px) {
            .form-row {
                grid-template-columns: 1fr
            }
        }

        textarea.input {
            resize: vertical;
            min-height: 90px;
            line-height: 1.5
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px
        }

        /* Dropzone */
        .dropzone {
            position: relative;
            border: 2px dashed var(--border);
            background: #f8fafc;
            border-radius: var(--r);
            padding: 22px 16px;
            text-align: center;
            cursor: pointer;
            transition: .2s
        }

        .dropzone:hover,
        .dropzone.dragover {
            border-color: var(--primary);
            background: var(--primary-light)
        }

        .dropzone-icon {
            width: 40px;
            height: 40px;
            margin: 0 auto 10px;
            border-radius: 50%;
            background: #fff;
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--shadow)
        }

        .dropzone-text {
            font-size: 13px;
            font-weight: 600;
            color: var(--heading)
        }

        .dropzone-text span {
            color: var(--primary)
        }

        .dropzone-hint {
            font-size: 11.5px;
            color: var(--muted);
            margin-top: 4px
        }

        .dropzone.has-image {
            padding: 0;
            border-style: solid;
            overflow: hidden;
            cursor: default
        }

        .dropzone-preview {
            display: none;
            position: relative;
            aspect-ratio: 16 / 9
        }

        .dropzone.has-image .dropzone-preview {
            display: block
        }

        .dropzone.has-image .dropzone-empty {
            display: none
        }

        .dropzone-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block
        }

        .dropzone-actions {
            position: absolute;
            top: 10px;
            right: 10px;
            display: flex;
            gap: 8px
        }

        .dz-btn {
            border: none;
            background: rgba(255, 255, 255, .95);
            color: var(--heading);
            font-size: 12px;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 8px;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .15)
        }

        .dz-btn.danger {
            color: var(--danger)
        }

        .dropzone-filename {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            padding: 8px 12px;
            font-size: 11.5px;
            color: #fff;
            background: linear-gradient(transparent, rgba(0, 0, 0, .6));
            text-align: left;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis
        }

        .dz-error {
            display: none;
            font-size: 12px;
            color: #cf1322;
            margin-top: 4px
        }

        .dz-error.show {
            display: block
        }
    </style>
@endpush

@section('content')
    @php
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
        $fallbackImg = 'https://raw.githubusercontent.com/fashaputri/asset/main/pawku.png';
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
        <button type="button" class="tab active" data-status="all">Semua <span
                class="n">({{ $totalArtikel }})</span></button>
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
            <article class="article-card" data-status="{{ $a['status'] }}" data-category="{{ strtolower($a['category']) }}"
                data-title="{{ strtolower($a['title']) }}">
                <div class="article-thumb">
                    <img src="{{ $a['image'] }}" alt="{{ $a['title'] }}" loading="lazy"
                        onerror="this.onerror=null;this.src='{{ $fallbackImg }}';">
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
                        data-action="{{ route('admin.articles.update', $a['id']) }}"
                        onclick="openModal(JSON.parse(this.dataset.article), this.dataset.action)">Edit</button>
                    <button type="button" class="btn btn-danger" data-title="{{ $a['title'] }}"
                        data-action="{{ route('admin.articles.destroy', $a['id']) }}"
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
    <form id="deleteForm" method="POST" hidden>
        @csrf
        @method('DELETE')
    </form>

    <dialog id="articleModal">
        <form id="articleForm" method="POST" action="{{ route('admin.articles.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" id="fMethod" value="POST" disabled>
            <input type="hidden" name="remove_image" id="fRemoveImage" value="0">

            <div class="modal-header">
                <div class="modal-title" id="modalTitle">Tambah Artikel</div>
                <button class="modal-close" type="button" aria-label="Tutup" onclick="closeModal()">&times;</button>
            </div>

            <div class="form-stack">
                <div class="field">
                    <label for="fTitle">Judul</label>
                    <input type="text" id="fTitle" name="title" class="input" placeholder="Judul artikel" required
                        maxlength="200">
                </div>

                <div class="form-row">
                    <div class="field">
                        <label for="fCategory">Kategori</label>
                        <select id="fCategory" name="category" class="input" required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="fStatus">Status</label>
                        <select id="fStatus" name="status" class="input">
                            <option value="publish">Terbit</option>
                            <option value="draft">Draf</option>
                        </select>
                    </div>
                </div>

                <div class="field">
                    <label>Gambar Artikel</label>
                    <input type="file" id="fImage" name="image" accept="image/png,image/jpeg,image/webp" hidden>
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
                    <textarea id="fDesc" name="description" class="input" required maxlength="300"
                        placeholder="Ringkasan untuk kartu di Beranda"></textarea>
                </div>

                <div class="field">
                    <label for="fContent">Isi Artikel</label>
                    <textarea id="fContent" name="content" class="input" style="min-height:140px;"
                        placeholder="Pisahkan antar paragraf dengan baris kosong"></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn btn-primary" id="btnSave">Simpan</button>
            </div>
        </form>
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
                    text: '"' + btn.dataset.title + '" beserta gambarnya akan dihapus permanen.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        const f = document.getElementById('deleteForm');
                        f.action = btn.dataset.action;
                        f.submit();
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
            document.getElementById('dzChange')?.addEventListener('click', e => { e.stopPropagation(); fileInput.click(); });
            document.getElementById('dzRemove')?.addEventListener('click', e => {
                e.stopPropagation();
                showDzError('');
                setPreview('');
                document.getElementById('fRemoveImage').value = '1';
            });
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
            const form = document.getElementById('articleForm');
            const STORE_URL = @json(route('admin.articles.store'));

            function openModal(article, action) {
                const isEdit = !!article;
                document.getElementById('modalTitle').textContent = isEdit ? 'Edit Artikel' : 'Tambah Artikel';

                const b = document.getElementById('btnSave');
                b.disabled = false;
                b.textContent = isEdit ? 'Simpan Perubahan' : 'Simpan';

                form.action = isEdit ? action : STORE_URL;
                const method = document.getElementById('fMethod');
                method.value = 'PUT';
                method.disabled = !isEdit;           // hanya kirim _method=PUT saat edit

                document.getElementById('fTitle').value = isEdit ? article.title : '';
                document.getElementById('fCategory').value = isEdit ? article.category : (document.getElementById('fCategory').options[0]?.value || '');
                document.getElementById('fStatus').value = isEdit ? article.status : 'publish';
                document.getElementById('fDesc').value = isEdit ? article.description : '';
                document.getElementById('fContent').value = isEdit ? (article.content || '') : '';
                document.getElementById('fRemoveImage').value = '0';

                showDzError('');
                setPreview(isEdit && article.has_image ? article.image : '', isEdit && article.has_image ? 'Gambar saat ini' : '');
                fileInput.value = '';

                modal.showModal();
            }

            function closeModal() { modal.close(); }

            // cegah klik ganda saat submit
            form.addEventListener('submit', () => {
                const b = document.getElementById('btnSave');
                b.disabled = true;
                b.textContent = 'Menyimpan...';
            });

            // Tutup hanya saat klik langsung pada backdrop (bukan klik dari dalam dialog)
            modal.addEventListener('click', e => {
                if (e.target !== modal) return;
                const r = modal.getBoundingClientRect();
                const inside = e.clientX >= r.left && e.clientX <= r.right && e.clientY >= r.top && e.clientY <= r.bottom;
                if (!inside) closeModal();
            });
        </script>
    @endpush
@endsection