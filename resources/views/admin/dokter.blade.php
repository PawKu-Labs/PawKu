@extends('admin.layout')
@section('title', 'Verifikasi Dokter')

@push('styles')
    <style>
        .dr-tabs { display: flex; gap: 8px; flex-wrap: wrap; margin: 4px 0 18px; }
        .dr-tab {
            display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 999px;
            border: 1px solid var(--border); background: #fff; color: var(--body);
            font-size: 13px; font-weight: 600; text-decoration: none; transition: .2s;
        }
        .dr-tab:hover { background: #f1f5f9; }
        .dr-tab.active { background: var(--heading); border-color: var(--heading); color: #fff; }
        .dr-tab .n { font-size: 11px; font-weight: 700; padding: 1px 8px; border-radius: 999px; background: rgba(0, 0, 0, .07); }
        .dr-tab.active .n { background: rgba(255, 255, 255, .22); color: #fff; }

        .dr-list { display: flex; flex-direction: column; gap: 14px; }
        .dr-card { background: #fff; border: 1px solid var(--border); border-radius: 16px; overflow: hidden; }
        .dr-head { display: flex; align-items: center; gap: 14px; padding: 16px 20px; border-bottom: 1px solid #f1f5f9; }
        .dr-avatar {
            width: 48px; height: 48px; border-radius: 14px; background: #e0f2fe; color: #0369a1;
            display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 18px;
            overflow: hidden; flex-shrink: 0;
        }
        .dr-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .dr-who { flex: 1; min-width: 0; }
        .dr-name { font-weight: 700; font-size: 16px; color: var(--heading); }
        .dr-sub { font-size: 12.5px; color: var(--muted); margin-top: 2px; word-break: break-word; }

        .dr-info { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 16px; padding: 16px 20px; }
        .dr-lbl { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .6px; color: var(--muted); margin-bottom: 4px; }
        .dr-val { font-size: 14px; font-weight: 600; color: var(--heading); word-break: break-word; }
        .dr-val small { display: block; font-size: 12px; font-weight: 400; color: var(--muted); margin-top: 2px; }
        .dr-missing { color: #be123c; font-weight: 600; }

        .dr-note { margin: 0 20px 16px; padding: 10px 14px; border-radius: 10px; font-size: 13px; line-height: 1.5; }
        .dr-note.warn { background: #fff7ed; color: #c2410c; }
        .dr-note.bad { background: #fff1f2; color: #be123c; }
        .dr-note.ok { background: #ecfdf5; color: #047857; }

        .dr-foot { display: flex; gap: 10px; flex-wrap: wrap; padding: 14px 20px; border-top: 1px solid #f1f5f9; background: #fafbfc; }
        .dr-foot form { margin: 0; }
        .dr-btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 10px 18px;
            border-radius: 10px; font: inherit; font-size: 13.5px; font-weight: 700; cursor: pointer;
            border: 1px solid transparent; transition: .2s;
        }
        .dr-btn:disabled { opacity: .45; cursor: not-allowed; }
        .dr-btn-ok { background: #16a34a; color: #fff; }
        .dr-btn-ok:hover:not(:disabled) { filter: brightness(.92); }
        .dr-btn-bad { background: #fff; border-color: #dc2626; color: #dc2626; }
        .dr-btn-bad:hover { background: #fef2f2; }
        .dr-btn-ghost { background: #fff; border-color: var(--border); color: var(--body); }
        .dr-btn-ghost:hover { background: #f1f5f9; }

        .dr-empty { text-align: center; padding: 48px 20px; color: var(--muted); }
        .dr-empty .ico { font-size: 40px; margin-bottom: 8px; }
        .dr-pager { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-top: 16px; font-size: 13px; color: var(--muted); }
        .dr-pager a { text-decoration: none; }
    </style>
@endpush

@section('content')
    @php
        $tabs = [
            'pending' => 'Menunggu',
            'verified' => 'Terverifikasi',
            'rejected' => 'Ditolak',
        ];
        $pill = [
            'pending' => ['Menunggu Verifikasi', 'p-pending'],
            'verified' => ['Terverifikasi', 'p-approved'],
            'rejected' => ['Ditolak', 'p-rejected'],
        ];
        $statusUrl = fn ($id) => route('admin.doctors.status', ['member' => $id]);
    @endphp

    <div>
        <h1 class="page-title">Verifikasi Dokter Hewan</h1>
        <p class="page-sub">Periksa nomor STR dan SIP dokter yang didaftarkan klinik. Hanya dokter terverifikasi yang bisa dipilih admin klinik untuk menangani booking.</p>
    </div>

    <div class="dr-tabs">
        @foreach ($tabs as $key => $label)
            <a href="{{ route('admin.doctors', ['status' => $key]) }}" class="dr-tab {{ $status === $key ? 'active' : '' }}">
                {{ $label }} <span class="n">{{ number_format((int) ($counts[$key] ?? 0)) }}</span>
            </a>
        @endforeach
    </div>

    @if ($doctors->isEmpty())
        <div class="card dr-empty">
            <div class="ico">🩺</div>
            <div style="font-weight:700;color:var(--heading);margin-bottom:4px;">
                @if ($status === 'pending')
                    Tidak ada dokter yang menunggu verifikasi
                @elseif ($status === 'verified')
                    Belum ada dokter terverifikasi
                @else
                    Belum ada dokter yang ditolak
                @endif
            </div>
            <div style="font-size:13px;">Dokter baru muncul di sini setelah klinik mendaftarkannya lewat menu Tim.</div>
        </div>
    @else
        <div class="dr-list">
            @foreach ($doctors as $d)
                @php
                    $docName = $d->full_name ?: ($d->username ?: 'Tanpa nama');
                    $hasStr = trim((string) $d->str_number) !== '';
                    $hasSip = trim((string) $d->sip_number) !== '';
                    $complete = $hasStr && $hasSip;
                    [$pillLabel, $pillClass] = $pill[$d->verification_status] ?? ['Menunggu Verifikasi', 'p-pending'];
                @endphp
                <article class="dr-card">
                    <div class="dr-head">
                        <div class="dr-avatar">
                            @if (!empty($d->avatar_url))
                                <img src="{{ $d->avatar_url }}" alt="">
                            @else
                                {{ strtoupper(mb_substr($docName, 0, 1)) }}
                            @endif
                        </div>
                        <div class="dr-who">
                            <div class="dr-name">{{ $docName }}</div>
                            <div class="dr-sub">
                                @if (!empty($d->email)){{ $d->email }}@endif
                                @if (!empty($d->phone_number))@if (!empty($d->email)) · @endif{{ $d->phone_number }}@endif
                            </div>
                        </div>
                        <span class="pill {{ $pillClass }}">{{ $pillLabel }}</span>
                    </div>

                    <div class="dr-info">
                        <div>
                            <div class="dr-lbl">Klinik</div>
                            <div class="dr-val">
                                {{ $d->mitra_name }}
                                @if ($d->mitra_status !== 'approved')
                                    <small>Status klinik: {{ $d->mitra_status }}</small>
                                @endif
                            </div>
                        </div>
                        <div>
                            <div class="dr-lbl">Nomor STR</div>
                            <div class="dr-val">
                                @if ($hasStr){{ $d->str_number }}@else<span class="dr-missing">Belum diisi</span>@endif
                            </div>
                        </div>
                        <div>
                            <div class="dr-lbl">Nomor SIP</div>
                            <div class="dr-val">
                                @if ($hasSip){{ $d->sip_number }}@else<span class="dr-missing">Belum diisi</span>@endif
                            </div>
                        </div>
                        <div>
                            <div class="dr-lbl">Spesialisasi</div>
                            <div class="dr-val">{{ $d->specialization ?: '-' }}</div>
                        </div>
                        <div>
                            <div class="dr-lbl">Didaftarkan</div>
                            <div class="dr-val">{{ \Carbon\Carbon::parse($d->created_at)->timezone('Asia/Jakarta')->format('d M Y H:i') }}</div>
                        </div>
                    </div>

                    @if ($d->verification_status === 'pending' && !$complete)
                        <div class="dr-note warn">
                            Nomor {{ !$hasStr && !$hasSip ? 'STR dan SIP' : (!$hasStr ? 'STR' : 'SIP') }} belum diisi, jadi dokter ini belum bisa diverifikasi.
                            Minta dokter melengkapi datanya terlebih dahulu.
                        </div>
                    @endif

                    @if ($d->verification_status === 'verified')
                        <div class="dr-note ok">
                            Diverifikasi
                            @if (!empty($d->verified_by_name)) oleh <strong>{{ $d->verified_by_name }}</strong>@endif
                            @if (!empty($d->verified_at)) pada {{ \Carbon\Carbon::parse($d->verified_at)->timezone('Asia/Jakarta')->format('d M Y H:i') }}@endif.
                            @unless ($d->is_active) Akun dokter ini sedang dinonaktifkan oleh klinik. @endunless
                        </div>
                    @endif

                    @if ($d->verification_status === 'rejected')
                        <div class="dr-note bad">
                            <strong>Alasan penolakan:</strong> {{ $d->rejection_reason ?: 'Admin tidak mencantumkan alasan.' }}
                        </div>
                    @endif

                    <div class="dr-foot">
                        @if ($d->verification_status === 'pending')
                            <form method="POST" action="{{ $statusUrl($d->id) }}">
                                @csrf
                                <input type="hidden" name="status" value="verified">
                                <button type="button" class="dr-btn dr-btn-ok" @disabled(!$complete)
                                    onclick="confirmAction(this.form, {{ Js::from('Verifikasi ' . $docName . ' sebagai dokter di ' . $d->mitra_name . '? Setelah itu admin klinik bisa memilihnya untuk menangani booking.') }}, false)">
                                    ✓ Verifikasi dokter
                                </button>
                            </form>
                            <form method="POST" action="{{ $statusUrl($d->id) }}">
                                @csrf
                                <input type="hidden" name="status" value="rejected">
                                <button type="button" class="dr-btn dr-btn-bad"
                                    onclick="promptReject(this.form, { title: {{ Js::from('Tolak verifikasi ' . $docName . '?') }}, placeholder: 'Tuliskan alasan penolakan untuk dokter dan klinik...', confirmText: 'Ya, tolak dokter' })">
                                    ✕ Tolak
                                </button>
                            </form>
                        @elseif ($d->verification_status === 'verified')
                            <form method="POST" action="{{ $statusUrl($d->id) }}">
                                @csrf
                                <input type="hidden" name="status" value="pending">
                                <button type="button" class="dr-btn dr-btn-bad"
                                    onclick="confirmAction(this.form, {{ Js::from('Cabut verifikasi ' . $docName . '? Dokter tidak akan bisa dipilih untuk booking baru sampai diverifikasi lagi.') }}, true)">
                                    Cabut verifikasi
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ $statusUrl($d->id) }}">
                                @csrf
                                <input type="hidden" name="status" value="pending">
                                <button type="button" class="dr-btn dr-btn-ghost"
                                    onclick="confirmAction(this.form, {{ Js::from('Kembalikan ' . $docName . ' ke status menunggu verifikasi?') }}, false)">
                                    Tinjau ulang
                                </button>
                            </form>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>

        <div class="dr-pager">
            <div>
                @if ($doctors->previousPageUrl())
                    <a class="btn btn-outline" href="{{ $doctors->previousPageUrl() }}">← Sebelumnya</a>
                @endif
            </div>
            <span>Halaman {{ $doctors->currentPage() }}</span>
            <div>
                @if ($doctors->hasMorePages())
                    <a class="btn btn-outline" href="{{ $doctors->nextPageUrl() }}">Berikutnya →</a>
                @endif
            </div>
        </div>
    @endif
@endsection