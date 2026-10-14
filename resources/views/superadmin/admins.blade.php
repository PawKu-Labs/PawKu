@extends('superadmin.layout')
@section('title', 'Kelola Admin')

@section('content')
    <div class="hero">
        <div>
            <h1 class="page-title">Kelola Admin</h1>
            <p class="page-sub">Lihat, nonaktifkan, blokir, turunkan jabatan, hapus, atau tambah akun admin.</p>
        </div>
        <div>
            <button class="btn btn-primary" type="button" onclick="document.getElementById('addAdmin').showModal()">
                <i class="fa-solid fa-user-plus"></i> Tambah Admin
            </button>
        </div>
    </div>

    <form method="GET" class="filters">
        <div class="field grow" style="max-width:340px">
            <input class="input" type="text" name="q" value="{{ $q }}"
                placeholder="Cari nama, username, atau email...">
        </div>
    </form>

    <div class="card">
        <div class="table-wrap">
            <table class="responsive">
                <thead>
                    <tr>
                        <th class="cell-first">Admin</th>
                        <th>Username</th>
                        <th>Status</th>
                        <th>Dibuat</th>
                        <th class="cell-last">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($admins as $a)
                        @php
                            $st = $a->is_blocked ? 'blocked' : (($a->is_active ?? true) ? 'active' : 'inactive');
                            $stLabel = ['active' => 'Aktif', 'inactive' => 'Nonaktif', 'blocked' => 'Diblokir'][$st] ?? $st;
                            $self = $a->id === session('supabase.user.id');
                        @endphp
                        <tr>
                            <td class="cell-first" data-label="Admin">
                                <strong>{{ $a->full_name ?? $a->username ?? '-' }}</strong>
                                <div class="sub">{{ $a->email ?? '' }}</div>
                            </td>
                            <td data-label="Username">{{ $a->username ?? '-' }}</td>
                            <td data-label="Status"><span class="pill p-{{ $st }}">{{ $stLabel }}</span></td>
                            <td data-label="Dibuat">{{ !empty($a->created_at) ? \Carbon\Carbon::parse($a->created_at)->setTimezone('Asia/Jakarta')->format('d M Y') : '-' }}</td>
                            <td class="cell-last" data-label="Aksi">
                                @unless($self)
                                    <div class="actions">
                                        @if($st === 'active')
                                            @include('superadmin._act', ['a' => $a, 'action' => 'deactivate', 'label' => 'Nonaktifkan', 'cls' => 'btn-outline', 'confirm' => 'Nonaktifkan admin ini? Ia tidak bisa login.'])
                                            @include('superadmin._act', ['a' => $a, 'action' => 'block', 'label' => 'Blokir', 'cls' => 'btn-danger', 'confirm' => 'Blokir admin ini?'])
                                        @elseif($st === 'inactive')
                                            @include('superadmin._act', ['a' => $a, 'action' => 'activate', 'label' => 'Aktifkan', 'cls' => 'btn-green', 'confirm' => 'Aktifkan kembali admin ini?'])
                                            @include('superadmin._act', ['a' => $a, 'action' => 'block', 'label' => 'Blokir', 'cls' => 'btn-danger', 'confirm' => 'Blokir admin ini?'])
                                        @else
                                            @include('superadmin._act', ['a' => $a, 'action' => 'unblock', 'label' => 'Buka Blokir', 'cls' => 'btn-green', 'confirm' => 'Buka blokir admin ini?'])
                                        @endif
                                        @include('superadmin._act', ['a' => $a, 'action' => 'demote', 'label' => 'Turunkan', 'cls' => 'btn-outline', 'confirm' => 'Turunkan admin ini menjadi pemilik kucing biasa?'])
                                        @include('superadmin._act', ['a' => $a, 'action' => 'delete', 'label' => 'Hapus', 'cls' => 'btn-danger', 'confirm' => 'HAPUS PERMANEN akun admin ini? Tindakan tidak dapat dibatalkan.'])
                                    </div>
                                @else 
                                    <span class="sub">Akun Anda</span> 
                                @endunless
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty">Belum ada admin.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($admins->hasPages())
            <div class="pager">{{ $admins->links('pagination::simple-default') }}</div>
        @endif
    </div>

    <dialog id="addAdmin">
        <div class="modal-header">
            <h2 class="modal-title">Tambah Admin Baru</h2>
            <button type="button" class="modal-close" onclick="document.getElementById('addAdmin').close()">&times;</button>
        </div>
        <form method="POST" action="{{ route('superadmin.admins.store') }}">
            @csrf
            <div class="field" style="margin-bottom: 12px;">
                <label for="username">Username</label>
                <input class="input" id="username" name="username" value="{{ old('username') }}" required minlength="3"
                    maxlength="30" autocomplete="off">
                @error('username')
                    <div class="sub" style="color:var(--danger)">{{ $message }}</div>
                @enderror
            </div>
            <div class="field" style="margin-bottom: 12px;">
                <label for="email">Email</label>
                <input class="input" id="email" type="email" name="email" value="{{ old('email') }}" required
                    autocomplete="off">
                @error('email')
                    <div class="sub" style="color:var(--danger)">{{ $message }}</div>
                @enderror
            </div>
            <div class="field" style="margin-bottom: 16px;">
                <label for="password">Password Sementara</label>
                <input class="input" id="password" type="text" name="password" required minlength="8" autocomplete="off">
                <div class="sub">Minimal 8 karakter. Admin disarankan menggantinya setelah login pertama.</div>
                @error('password')
                    <div class="sub" style="color:var(--danger)">{{ $message }}</div>
                @enderror
            </div>
            <div class="actions" style="justify-content:flex-end;">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('addAdmin').close()">Batal</button>
                <button class="btn btn-primary" type="submit">Simpan</button>
            </div>
        </form>
    </dialog>
@endsection

@push('scripts')
    @if($errors->any() || session('open_modal'))
        <script>document.getElementById('addAdmin').showModal();</script>
    @endif
@endpush