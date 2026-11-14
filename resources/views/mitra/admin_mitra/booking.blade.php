@extends('mitra.layout')
@section('title', 'Booking')

@section('content')
    @php
        $timeOf = fn ($t) => $t ? substr((string) $t, 0, 5) : null;
        $dateOf = function ($b) {
            $d = $b->slot_date ?: \Carbon\Carbon::parse($b->created_at)->timezone('Asia/Jakarta')->toDateString();

            return \Carbon\Carbon::parse($d)->format('d/m/Y');
        };
        // Urutan langkah booking aktif
        $steps = ['menunggu' => 'Menunggu', 'dikonfirmasi' => 'Diterima', 'dilayani' => 'Dilayani', 'selesai' => 'Selesai'];
        $stepIndex = array_flip(array_keys($steps));
    @endphp

    <div>
        <h1 class="page-title">Booking</h1>
        <p class="page-sub">Terima booking, pilih dokter yang menangani, lalu mulai layani dan tandai selesai.</p>
    </div>

    <div class="tabs">
        @foreach ($tabs as $t)
            <a class="tab {{ $filter === $t['key'] ? 'active' : '' }}"
                href="{{ route('admin_mitra.booking', ['mitra' => $clinic->id, 'filter' => $t['key']]) }}">
                {{ $t['label'] }} <span class="count">{{ number_format($t['count']) }}</span>
            </a>
        @endforeach
    </div>

    @if ($doctors->isEmpty())
        <div class="hint">
            Belum ada dokter yang terverifikasi di klinik ini, jadi booking belum bisa dilayani.
            @if ($pendingDoctors > 0)
                Ada {{ $pendingDoctors }} dokter yang masih menunggu verifikasi admin PawKu.
            @else
                Pemilik klinik dapat menambahkan akun dokter di menu Tim, lalu admin PawKu memverifikasinya.
            @endif
        </div>
    @endif

    @if ($bookings->isEmpty())
        <div class="card"><div class="empty">Tidak ada booking pada tampilan ini.</div></div>
    @else
        <div class="bk-list">
            @foreach ($bookings as $b)
                @php
                    $s = $b->status;
                    $hasDoctor = ! empty($b->assigned_doctor_id);
                    $isActive = isset($stepIndex[$s]);
                    $statusUrl = route('admin_mitra.booking.status', ['mitra' => $clinic->id, 'booking' => $b->id]);
                    $cat = $b->cat_name ?? 'kucing';
                @endphp
                <article class="bk">
                    {{-- Kepala: nomor antri, kucing, pemilik, status --}}
                    <div class="bk-head">
                        <span class="queue" title="Nomor antri">{{ $b->queue_number ?? '-' }}</span>
                        <div class="bk-title">
                            <b>{{ $b->cat_name ?? '-' }}@if ($b->cat_breed) <span style="font-weight:400">· {{ $b->cat_breed }}</span>@endif</b>
                            <span>Pemilik: {{ $b->owner_name ?? $b->owner_username ?? '-' }}@if ($b->owner_phone) · {{ $b->owner_phone }}@endif</span>
                        </div>
                        <span class="st st-{{ $s }}">{{ $statusLabels[$s] ?? $s }}</span>
                    </div>

                    {{-- Info layanan & jadwal --}}
                    <div class="bk-info">
                        <div>
                            <div class="lbl">Layanan</div>
                            <div class="val">{{ $b->service_name ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="lbl">Jadwal</div>
                            <div class="val">
                                {{ $dateOf($b) }}
                                @if ($timeOf($b->slot_start_time))
                                    <div class="sub">{{ $timeOf($b->slot_start_time) }}@if ($timeOf($b->slot_end_time)) – {{ $timeOf($b->slot_end_time) }}@endif</div>
                                @endif
                            </div>
                        </div>
                        @if ($b->notes)
                            <div>
                                <div class="lbl">Catatan pemilik</div>
                                <div class="val" style="font-weight:400">{{ \Illuminate\Support\Str::limit($b->notes, 120) }}</div>
                            </div>
                        @endif
                    </div>

                    {{-- Langkah progres (hanya booking yang berjalan / selesai) --}}
                    @if ($isActive)
                        <div class="steps">
                            @foreach ($steps as $key => $label)
                                @php $cls = $stepIndex[$key] < $stepIndex[$s] || $s === 'selesai' ? 'done' : ($key === $s ? 'now' : ''); @endphp
                                <span class="step {{ $cls }}"><i>{{ $cls === 'done' ? '✓' : $loop->iteration }}</i>{{ $label }}</span>
                                @if (! $loop->last)<span class="step-sep"></span>@endif
                            @endforeach
                        </div>
                    @endif

                    {{-- Dua panel: dokter & tindakan --}}
                    <div class="bk-panels">
                        <div class="bk-panel">
                            <div class="panel-title">1 · Dokter yang menangani</div>

                            @if ($hasDoctor)
                                <span class="doc-now">🩺 {{ $b->doctor_name ?? 'Dokter terpilih' }}</span>
                            @else
                                <span class="doc-none">⚠️ Belum ada dokter</span>
                            @endif

                            @if (in_array($s, $assignable, true))
                                <form method="POST"
                                    action="{{ route('admin_mitra.booking.dokter', ['mitra' => $clinic->id, 'booking' => $b->id]) }}"
                                    class="doc-form"
                                    data-confirm="doctor" data-cat="{{ $cat }}">
                                    @csrf
                                    <select name="doctor_id" class="select" @disabled($doctors->isEmpty())>
                                        <option value="">— Pilih dokter —</option>
                                        @foreach ($doctors as $d)
                                            <option value="{{ $d->user_id }}" @selected($b->assigned_doctor_id === $d->user_id)>
                                                {{ $d->full_name ?? $d->username }}@if ($d->specialization) ({{ $d->specialization }})@endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-outline" @disabled($doctors->isEmpty())>
                                        {{ $hasDoctor ? 'Ganti dokter' : 'Teruskan ke dokter' }}
                                    </button>
                                </form>
                            @endif
                        </div>

                        <div class="bk-panel">
                            <div class="panel-title">2 · Tindakan</div>

                            @if ($s === 'menunggu')
                                <div class="act-row">
                                    <form method="POST" action="{{ $statusUrl }}"
                                        data-confirm="status" data-icon="question" data-color="#16a34a"
                                        data-title="Terima booking {{ $cat }}?"
                                        data-text="Booking diterima dan nomor antri {{ $b->queue_number ?? '-' }} tetap dipakai."
                                        data-ok="Ya, terima">
                                        @csrf
                                        <input type="hidden" name="status" value="dikonfirmasi">
                                        <button type="submit" class="btn btn-success">✓ Terima booking</button>
                                    </form>
                                    <form method="POST" action="{{ $statusUrl }}" class="act-small"
                                        data-confirm="status" data-icon="warning" data-color="#dc2626"
                                        data-title="Tolak booking {{ $cat }}?"
                                        data-text="Pemilik kucing akan melihat booking ini ditolak."
                                        data-ok="Ya, tolak">
                                        @csrf
                                        <input type="hidden" name="status" value="ditolak">
                                        <button type="submit" class="btn btn-outline-danger">✕ Tolak</button>
                                    </form>
                                </div>

                            @elseif ($s === 'dikonfirmasi')
                                <div class="act-row">
                                    @if ($hasDoctor)
                                        <form method="POST" action="{{ $statusUrl }}"
                                            data-confirm="status" data-icon="question" data-color="#7c3aed"
                                            data-title="Mulai layani {{ $cat }}?"
                                            data-text="Status booking menjadi sedang dilayani oleh {{ $b->doctor_name ?? 'dokter terpilih' }}."
                                            data-ok="Ya, mulai layani">
                                            @csrf
                                            <input type="hidden" name="status" value="dilayani">
                                            <button type="submit" class="btn btn-primary">▶ Mulai layani</button>
                                        </form>
                                    @else
                                        <form>
                                            <button type="button" class="btn btn-primary" disabled title="Pilih dokter dulu">▶ Mulai layani</button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ $statusUrl }}" class="act-small"
                                        data-confirm="status" data-icon="warning" data-color="#dc2626"
                                        data-title="Tolak booking {{ $cat }}?"
                                        data-text="Pemilik kucing akan melihat booking ini ditolak."
                                        data-ok="Ya, tolak">
                                        @csrf
                                        <input type="hidden" name="status" value="ditolak">
                                        <button type="submit" class="btn btn-outline-danger">✕ Tolak</button>
                                    </form>
                                </div>
                                @unless ($hasDoctor)
                                    <div class="act-note">Pilih dokter di kolom sebelah dulu sebelum mulai layani.</div>
                                @endunless

                            @elseif ($s === 'dilayani')
                                <div class="act-row">
                                    @if ($hasDoctor)
                                        <form method="POST" action="{{ $statusUrl }}"
                                            data-confirm="status" data-icon="question" data-color="#16a34a"
                                            data-title="Tandai {{ $cat }} selesai?"
                                            data-text="Booking akan ditandai selesai dilayani."
                                            data-ok="Ya, selesai">
                                            @csrf
                                            <input type="hidden" name="status" value="selesai">
                                            <button type="submit" class="btn btn-success">✓ Tandai selesai</button>
                                        </form>
                                    @else
                                        <form>
                                            <button type="button" class="btn btn-success" disabled title="Pilih dokter dulu">✓ Tandai selesai</button>
                                        </form>
                                    @endif
                                </div>
                                @unless ($hasDoctor)
                                    <div class="act-note">Pilih dokter di kolom sebelah dulu sebelum menandai selesai.</div>
                                @endunless

                            @else
                                <span class="act-done">Tidak ada tindakan lagi untuk booking ini.</span>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="pager">
            <div>
                @if ($bookings->previousPageUrl())
                    <a class="btn btn-ghost btn-sm" href="{{ $bookings->previousPageUrl() }}">← Sebelumnya</a>
                @endif
            </div>
            <span class="muted">Halaman {{ $bookings->currentPage() }}</span>
            <div>
                @if ($bookings->hasMorePages())
                    <a class="btn btn-ghost btn-sm" href="{{ $bookings->nextPageUrl() }}">Berikutnya →</a>
                @endif
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        // Setiap aksi (terima, tolak, mulai layani, selesai, teruskan ke dokter) minta konfirmasi dulu.
        document.addEventListener('submit', function (e) {
            const form = e.target;
            if (!form.matches || !form.matches('form[data-confirm]')) return;
            if (form.dataset.confirmed === '1') return;
            e.preventDefault();

            let cfg;
            if (form.dataset.confirm === 'doctor') {
                const select = form.querySelector('select[name="doctor_id"]');
                const chosen = select && select.value ? select.options[select.selectedIndex].text.trim() : '';
                cfg = chosen
                    ? { title: 'Teruskan ke dokter ini?', text: 'Booking ' + form.dataset.cat + ' akan ditangani oleh ' + chosen + '.', ok: 'Ya, teruskan', icon: 'question', color: '#e11d48' }
                    : { title: 'Kosongkan pilihan dokter?', text: 'Booking ' + form.dataset.cat + ' tidak akan punya dokter yang menangani.', ok: 'Ya, kosongkan', icon: 'warning', color: '#dc2626' };
            } else {
                cfg = { title: form.dataset.title, text: form.dataset.text, ok: form.dataset.ok, icon: form.dataset.icon || 'question', color: form.dataset.color || '#e11d48' };
            }

            Swal.fire({
                title: cfg.title,
                text: cfg.text,
                icon: cfg.icon,
                showCancelButton: true,
                confirmButtonColor: cfg.color,
                cancelButtonColor: '#64748b',
                confirmButtonText: cfg.ok,
                cancelButtonText: 'Batal',
                reverseButtons: true,
            }).then(function (result) {
                if (result.isConfirmed) {
                    form.dataset.confirmed = '1';
                    form.submit();
                }
            });
        });
    </script>
@endpush