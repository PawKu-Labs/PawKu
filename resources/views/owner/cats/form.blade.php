@extends('owner.layout')
@section('title', $cat ? 'Edit Kucing' : 'Tambah Kucing')

@push('styles')
    <style>
        .cf-wrap {
            max-width: 680px;
        }

        .cf-back {
            display: inline-block;
            font-size: 13px;
            color: var(--muted);
            text-decoration: none;
            margin-bottom: 10px;
        }

        .cf-photo {
            display: flex;
            align-items: center;
            gap: 18px;
            flex-wrap: wrap;
        }

        .cf-ava {
            width: 96px;
            height: 96px;
            border-radius: 50%;
            border: 3px solid var(--primary);
            padding: 2px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            font-size: 38px;
            background: rgba(46, 158, 107, .1);
        }

        .cf-ava img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        .cf-field {
            margin-bottom: 16px;
        }

        .cf-label {
            display: block;
            font-size: 12.5px;
            font-weight: 600;
            color: var(--heading);
            margin-bottom: 6px;
        }

        .cf-input {
            width: 100%;
            padding: 10px 12px;
            font: inherit;
            font-size: 14px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: #fff;
            color: var(--heading);
        }

        .cf-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(46, 158, 107, .15);
        }

        .cf-hint {
            font-size: 12px;
            color: var(--muted);
            margin-top: 5px;
        }

        .cf-seg {
            display: flex;
            gap: 10px;
        }

        .cf-seg label {
            flex: 1;
            cursor: pointer;
        }

        .cf-seg input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .cf-seg span {
            display: block;
            text-align: center;
            padding: 10px;
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            color: var(--muted);
        }

        .cf-seg input:checked+span {
            border-color: var(--primary);
            background: rgba(46, 158, 107, .1);
            color: var(--primary);
        }

        .cf-seg input:focus-visible+span {
            box-shadow: 0 0 0 3px rgba(46, 158, 107, .25);
        }

        .cf-check {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            cursor: pointer;
        }

        .cf-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
            margin-top: 6px;
        }

        .cf-grow {
            flex: 1;
        }

        .cf-danger-zone {
            margin-top: 18px;
        }
    </style>
@endpush

@section('content')
    @php
        $currentBreed = trim((string) ($cat->breed ?? ''));
        $matched = collect($breeds)->first(fn($b) => mb_strtolower($b) === mb_strtolower($currentBreed));
        $initChoice = $currentBreed === '' ? '' : ($matched ?? \App\Http\Requests\CatRequest::BREED_OTHER);
        $breedChoice = old('breed_choice', $initChoice);
        $breedCustom = old('breed_custom', $currentBreed !== '' && !$matched ? $currentBreed : '');
        $gender = old('gender', $cat->gender ?? 'jantan');
        $neutered = (bool) old('is_neutered', ($cat->is_neutered ?? false) ? 1 : 0);
        $photoUrl = $cat->photo_url ?? null;
    @endphp

    <div class="cf-wrap">
        <a href="{{ route('owner.cats.index') }}" class="cf-back">← Kembali ke Kucing Saya</a>
        <h1 class="page-title">{{ $cat ? 'Edit Profil Kucing' : 'Tambah Kucing' }}</h1>
        <p class="page-sub">{{ $cat ? 'Perbarui data ' . $cat->name . '.' : 'Isi data kucing kesayanganmu.' }}</p>

        @if ($errors->any())
            <div class="alert alert-err" style="margin-bottom:16px;">
                @foreach ($errors->all() as $message)
                    <div>{{ $message }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" enctype="multipart/form-data" novalidate
            action="{{ $cat ? route('owner.cats.update', $cat->id) : route('owner.cats.store') }}">
            @csrf
            @if ($cat)
                @method('PUT')
            @endif

            <div class="card" style="padding:22px; margin-top:16px;">
                <div class="cf-field cf-photo">
                    <div class="cf-ava" id="photoBox">
                        @if ($photoUrl)
                            <img src="{{ $photoUrl }}" alt="Foto kucing" id="photoPreview">
                        @else
                            <span id="photoEmoji">🐾</span>
                        @endif
                    </div>
                    <div>
                        <label for="photo" class="btn btn-outline" style="cursor:pointer;">
                            {{ $photoUrl ? 'Ganti Foto' : 'Pilih Foto' }}
                        </label>
                        <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/webp"
                            style="display:none;">
                        <p class="cf-hint">JPG, PNG, atau WEBP. Maksimal 5 MB.</p>
                    </div>
                </div>

                <div class="cf-field">
                    <label class="cf-label" for="name">Nama Kucing *</label>
                    <input class="cf-input" type="text" id="name" name="name" maxlength="100" required
                        value="{{ old('name', $cat->name ?? '') }}" placeholder="Contoh: Mochi">
                </div>

                <div class="cf-field">
                    <label class="cf-label" for="breed_choice">Ras</label>
                    <select class="cf-input" id="breed_choice" name="breed_choice">
                        <option value="">Tidak diketahui</option>
                        @foreach ($breeds as $breed)
                            <option value="{{ $breed }}" @selected($breedChoice === $breed)>{{ $breed }}</option>
                        @endforeach
                        <option value="{{ \App\Http\Requests\CatRequest::BREED_OTHER }}" @selected($breedChoice === \App\Http\Requests\CatRequest::BREED_OTHER)>
                            Lainnya…</option>
                    </select>
                    <input class="cf-input" type="text" id="breed_custom" name="breed_custom" maxlength="100"
                        value="{{ $breedCustom }}" placeholder="Ketik nama ras"
                        style="margin-top:8px; display:none;">
                </div>

                <div class="cf-field">
                    <label class="cf-label" for="birth_date">Tanggal Lahir</label>
                    <input class="cf-input" type="date" id="birth_date" name="birth_date" min="2000-01-01"
                        max="{{ now('Asia/Jakarta')->toDateString() }}"
                        value="{{ old('birth_date', $cat->birth_date ?? '') }}">
                    <p class="cf-hint" id="ageText"></p>
                </div>

                <div class="cf-field">
                    <span class="cf-label">Jenis Kelamin *</span>
                    <div class="cf-seg">
                        <label><input type="radio" name="gender" value="jantan" @checked($gender === 'jantan')>
                            <span>Jantan</span></label>
                        <label><input type="radio" name="gender" value="betina" @checked($gender === 'betina')>
                            <span>Betina</span></label>
                    </div>
                </div>

                <div class="cf-field" style="margin-bottom:0;">
                    <input type="hidden" name="is_neutered" value="0">
                    <label class="cf-check">
                        <input type="checkbox" name="is_neutered" value="1" @checked($neutered)>
                        Sudah steril / dikebiri
                    </label>
                </div>
            </div>

            <div class="cf-actions">
                <button type="submit" class="btn btn-primary">{{ $cat ? 'Simpan Perubahan' : 'Simpan' }}</button>
                <a href="{{ route('owner.cats.index') }}" class="btn btn-outline">Batal</a>
                <span class="cf-grow"></span>
                @if ($cat)
                    <button type="button" class="btn btn-danger" onclick="confirmDeleteCat()">Hapus</button>
                @endif
            </div>
        </form>

        {{-- Form hapus di luar form utama (form tidak boleh bersarang) --}}
        @if ($cat)
            <form id="delete-cat-form" method="POST" action="{{ route('owner.cats.destroy', $cat->id) }}"
                style="display:none;">
                @csrf
                @method('DELETE')
            </form>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            // --- Ras: tampilkan input teks bila memilih "Lainnya" ---
            const select = document.getElementById('breed_choice');
            const custom = document.getElementById('breed_custom');
            function syncBreed() {
                custom.style.display = select.value === @js(\App\Http\Requests\CatRequest::BREED_OTHER) ? 'block' : 'none';
            }
            select.addEventListener('change', syncBreed);
            syncBreed();

            // --- Umur (padanan ageLabel di Flutter) ---
            const birth = document.getElementById('birth_date');
            const ageText = document.getElementById('ageText');
            function syncAge() {
                if (!birth.value) { ageText.textContent = ''; return; }
                const b = new Date(birth.value + 'T00:00:00');
                const now = new Date();
                let years = now.getFullYear() - b.getFullYear();
                let months = now.getMonth() - b.getMonth();
                if (now.getDate() < b.getDate()) months--;
                if (months < 0) { years--; months += 12; }
                if (years < 0) { ageText.textContent = ''; return; }
                ageText.textContent = 'Umur: ' + (years > 0
                    ? (months > 0 ? years + ' thn ' + months + ' bln' : years + ' tahun')
                    : (months > 0 ? months + ' bulan' : 'Baru lahir'));
            }
            birth.addEventListener('change', syncAge);
            syncAge();

            // --- Preview foto + cek ukuran/tipe di sisi klien ---
            const input = document.getElementById('photo');
            const box = document.getElementById('photoBox');
            input.addEventListener('change', function () {
                const f = input.files[0];
                if (!f) return;
                const okType = ['image/jpeg', 'image/png', 'image/webp'].includes(f.type);
                if (!okType || f.size > 5 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Foto tidak valid',
                        text: !okType ? 'Gunakan format JPG, PNG, atau WEBP.' : 'Ukuran foto maksimal 5 MB.',
                    });
                    input.value = '';
                    return;
                }
                const img = document.createElement('img');
                img.alt = 'Pratinjau foto';
                img.src = URL.createObjectURL(f);
                box.replaceChildren(img);
            });
        })();

        @if ($cat)
            function confirmDeleteCat() {
                Swal.fire({
                    title: 'Hapus kucing ini?',
                    text: 'Profil ' + @js($cat->name) + ' beserta data terkait (riwayat berat, vaksin, kehamilan, dan pengingat) akan ikut terhapus. Tindakan ini tidak bisa dibatalkan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                }).then((r) => { if (r.isConfirmed) document.getElementById('delete-cat-form').submit(); });
            }
        @endif
    </script>
@endpush