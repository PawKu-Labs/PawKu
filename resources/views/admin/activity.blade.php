@extends('admin.layout')
@section('title', 'Log Aktivitas')

@section('content')
    @php
        $tabs = [
            'all' => 'Semua',
            'user' => 'Pengguna',
            'data' => 'Perubahan Data',
            'transaction' => 'Transaksi',
            'admin' => 'Aksi Admin',
            'auth' => 'Login',
        ];
        $catLabel = [
            'user' => 'Pengguna',
            'data' => 'Data',
            'transaction' => 'Transaksi',
            'admin' => 'Admin',
            'auth' => 'Login',
        ];
        $actionLabel = [
            'created' => 'Ditambah',
            'updated' => 'Diubah',
            'deleted' => 'Dihapus',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'pending' => 'Ditinjau ulang',
            'login' => 'Masuk',
            'logout' => 'Keluar',
        ];
        $allTotal = $totals->sum();
    @endphp

    <div class="hero">
        <div>
            <h1 class="page-title">Log Aktivitas Sistem</h1>
            <p class="page-sub">Pantau aktivitas pengguna, perubahan data, dan transaksi yang tercatat di PawKu, lengkap
                dengan waktu dan pelakunya.</p>
        </div>
    </div>

    @unless($ready)
        <div class="alert-bar">
            <div style="display:flex;align-items:center;gap:12px;">
                <div class="alert-icon">!</div>
                <div>
                    <div style="font-weight:700;color:#874d00;font-size:14px;">Tabel log belum tersedia</div>
                    <div style="font-size:13px;color:#ad6800;margin-top:2px;">
                        Jalankan <code>php artisan migrate</code> untuk membuat tabel <code>activity_logs</code>.
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="grid g4">
            <div class="card stat a-blue">
                <div class="stat-top"><span class="stat-label">Aktivitas hari ini</span>
                    <div class="stat-icon i-blue">⚡</div>
                </div>
                <div class="stat-value">{{ number_format($today->sum()) }}</div>
            </div>
            <div class="card stat a-purple">
                <div class="stat-top"><span class="stat-label">Perubahan data</span>
                    <div class="stat-icon i-purple">✎</div>
                </div>
                <div class="stat-value">{{ number_format(($today['data'] ?? 0) + ($today['admin'] ?? 0)) }}</div>
            </div>
            <div class="card stat a-green">
                <div class="stat-top"><span class="stat-label">Transaksi</span>
                    <div class="stat-icon i-green">💳</div>
                </div>
                <div class="stat-value">{{ number_format($today['transaction'] ?? 0) }}</div>
            </div>
            <div class="card stat a-gold">
                <div class="stat-top"><span class="stat-label">Aktivitas pengguna</span>
                    <div class="stat-icon i-gold">👤</div>
                </div>
                <div class="stat-value">{{ number_format(($today['user'] ?? 0) + ($today['auth'] ?? 0)) }}</div>
            </div>
        </div>

        <div class="tabs">
            @foreach($tabs as $key => $label)
                <a href="{{ route('admin.activity', array_filter(['category' => $key, 'q' => $search, 'from' => $from, 'to' => $to])) }}"
                    class="tab {{ $category === $key ? 'active' : '' }}">
                    {{ $label }}
                    <span class="n">({{ $key === 'all' ? $allTotal : ($totals[$key] ?? 0) }})</span>
                </a>
            @endforeach
        </div>

        <form method="GET" action="{{ route('admin.activity') }}" class="card filters">
            <input type="hidden" name="category" value="{{ $category }}">
            <div class="field grow">
                <label for="q">Cari</label>
                <input class="input" id="q" type="search" name="q" value="{{ $search }}"
                    placeholder="Nama pelaku, deskripsi, tabel, atau ID">
            </div>
            <div class="field">
                <label for="from">Dari tanggal</label>
                <input class="input" id="from" type="date" name="from" value="{{ $from }}">
            </div>
            <div class="field">
                <label for="to">Sampai tanggal</label>
                <input class="input" id="to" type="date" name="to" value="{{ $to }}">
            </div>
            <div style="display:flex;gap:8px;">
                <button class="btn btn-primary" type="submit" style="min-height:40px;">Terapkan</button>
                @if($search !== '' || $from || $to)
                    <a class="btn btn-outline" style="min-height:40px;"
                        href="{{ route('admin.activity', ['category' => $category]) }}">Reset</a>
                @endif
            </div>
        </form>

        <div class="card" style="padding:0;overflow:hidden;">
            <div class="table-wrap">
                <table class="responsive">
                    <thead>
                        <tr>
                            <th class="cell-first">Waktu</th>
                            <th>Pelaku</th>
                            <th>Jenis</th>
                            <th>Aktivitas</th>
                            <th class="cell-last" style="text-align:right;">Rincian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td class="cell-first" data-label="Waktu" style="white-space:nowrap;">
                                    <div>
                                        {{ \Carbon\Carbon::parse($log->created_at)->setTimezone('Asia/Jakarta')->format('d M Y, H:i:s') }}
                                        <span class="sub">WIB</span>
                                    </div>
                                </td>
                                <td data-label="Pelaku">
                                    <div>
                                        <div style="font-weight:600;">{{ $log->actor_name ?: 'Sistem' }}</div>
                                        @if($log->actor_role)
                                        <div class="sub">{{ str_replace('_', ' ', $log->actor_role) }}</div>@endif
                                    </div>
                                </td>
                                <td data-label="Jenis">
                                    <div style="display:flex;gap:6px;flex-wrap:wrap;justify-content:flex-end;">
                                        <span
                                            class="pill c-{{ $log->category }}">{{ $catLabel[$log->category] ?? $log->category }}</span>
                                        <span class="pill c-auth">{{ $actionLabel[$log->action] ?? $log->action }}</span>
                                    </div>
                                </td>
                                <td data-label="Aktivitas" class="stack" style="min-width:240px;">
                                    <div style="overflow-wrap:anywhere;">
                                        {{ $log->description }}
                                        @if($log->subject_type)
                                            <div class="sub">{{ $log->subject_type }}@if($log->subject_id) &middot;
                                            {{ \Illuminate\Support\Str::limit($log->subject_id, 18) }}@endif</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="cell-last" data-label="" style="text-align:right;">
                                    @if(!empty($log->changes))
                                        <button type="button" class="btn btn-outline" data-title="{{ $log->description }}"
                                            data-changes="{{ json_encode($log->changes) }}" onclick="showChanges(this)">
                                            Lihat perubahan
                                        </button>
                                    @else
                                        <span class="sub">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="empty">
                                    <div style="font-size:32px;margin-bottom:8px;">🕘</div>
                                    <div>Belum ada aktivitas yang cocok dengan filter ini.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
                <div class="pager">{{ $logs->links('vendor.pagination.simple-default') }}</div>
            @endif
        </div>
    @endunless

    <dialog id="changesModal">
        <div class="modal-header">
            <div class="modal-title" id="cTitle">Rincian perubahan</div>
            <button class="modal-close" type="button" aria-label="Tutup"
                onclick="document.getElementById('changesModal').close()">&times;</button>
        </div>
        <div style="max-height:65vh;overflow:auto;">
            <table class="diff">
                <thead>
                    <tr>
                        <th>Kolom</th>
                        <th>Sebelum</th>
                        <th>Sesudah</th>
                    </tr>
                </thead>
                <tbody id="cBody"></tbody>
            </table>
        </div>
        <div style="margin-top:18px;display:flex;justify-content:flex-end;">
            <button class="btn btn-outline" type="button"
                onclick="document.getElementById('changesModal').close()">Tutup</button>
        </div>
    </dialog>

    @push('scripts')
        <script>
            function showChanges(btn) {
                const rows = JSON.parse(btn.dataset.changes || '[]');
                document.getElementById('cTitle').textContent = btn.dataset.title || 'Rincian perubahan';
                const body = document.getElementById('cBody');
                body.replaceChildren();

                rows.forEach((r) => {
                    const tr = document.createElement('tr');
                    [[r.field, ''], [r.old, 'old'], [r.new, 'new']].forEach(([val, cls]) => {
                        const td = document.createElement('td');
                        td.textContent = val === null ? '—' : val;
                        if (cls && val !== null) td.className = cls;
                        tr.appendChild(td);
                    });
                    body.appendChild(tr);
                });

                document.getElementById('changesModal').showModal();
            }
        </script>
    @endpush
@endsection