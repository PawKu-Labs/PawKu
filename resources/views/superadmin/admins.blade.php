@extends('superadmin.layout')
@section('title', 'Kelola Admin')

@section('content')
    <div class="toolbar">
        <div>
            <h1 class="page-title">Kelola Admin</h1>
            <p class="page-sub">Lihat, nonaktifkan, blokir, turunkan jabatan, hapus, atau tambah akun admin.</p>
        </div>
        <button class="btn btn-primary" type="button" onclick="document.getElementById('addAdmin').showModal()">+ Tambah
            Admin</button>
    </div>

    <form method="GET" class="toolbar">
        <input class="input" style="max-width:340px" type="text" name="q" value="{{ $q }}"
            placeholder="Cari nama, username, atau email...">
    </form>

    <div class="card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Admin</th>
                        <th>Username</th>
                        <th>Status</th>
                        <th>Dibuat</th>
                        <th>Aksi</th>
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
                            <td><strong>{{ $a->full_name ?? $a->username ?? '-' }}</strong>
                                <div class="sub">{{ $a->email ?? '' }}</div>
                            </td>
                            <td>{{ $a->username ?? '-' }}</td>
                            <td><span class="pill p-{{ $st }}">{{ $stLabel }}</span></td>
                            <td>{{ !empty($a->created_at) ? \Carbon\Carbon::parse($a->created_at)->setTimezone('Asia/Jakarta')->format('d M Y') : '-' }}
                            </td>
                            <td>
                                @unless($self)
                                    <div class="actions">
                                        @if($st === 'active')
                                            @include('superadmin._act', ['a' => $a, 'action' => 'deactivate', 'label' => 'Nonaktifkan', 'cls' => 'btn-warn', 'confirm' => 'Nonaktifkan admin ini? Ia tidak bisa login.'])
                                            @include('superadmin._act', ['a' => $a, 'action' => 'block', 'label' => 'Blokir', 'cls' => 'btn-danger', 'confirm' => 'Blokir admin ini?'])
                                        @elseif($st === 'inactive')
                                            @include('superadmin._act', ['a' => $a, 'action' => 'activate', 'label' => 'Aktifkan', 'cls' => 'btn-green', 'confirm' => 'Aktifkan kembali admin ini?'])
                                            @include('superadmin._act', ['a' => $a, 'action' => 'block', 'label' => 'Blokir', 'cls' => 'btn-danger', 'confirm' => 'Blokir admin ini?'])
                                        @else
                                            @include('superadmin._act', ['a' => $a, 'action' => 'unblock', 'label' => 'Buka Blokir', 'cls' => 'btn-green', 'confirm' => 'Buka blokir admin ini?'])
                                        @endif
                                        @include('superadmin._act', ['a' => $a, 'action' => 'demote', 'label' => 'Turunkan', 'cls' => '', 'confirm' => 'Turunkan admin ini menjadi pemilik kucing biasa?'])
                                        @include('superadmin._act', ['a' => $a, 'action' => 'delete', 'label' => 'Hapus', 'cls' => 'btn-danger', 'confirm' => 'HAPUS PERMANEN akun admin ini? Tindakan tidak dapat dibatalkan.'])
                                    </div>
                                @else <span class="sub">Akun Anda</span> @endunless
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
        <div class="pagination">{{ $admins->links('pagination::simple-default') }}</div>@endif
    </div>

    <dialog id="addAdmin">
        <h2 style="font-family:'Montserrat',sans-serif;font-size:18px;color:var(--heading);margin-bottom:16px">Tambah Admin
            Baru</h2>
        <form method="POST" action="{{ route('superadmin.admins.store') }}">
            @csrf
            <div class="field">
                <label for="username">Username</label>
                <input class="input" id="username" name="username" value="{{ old('username') }}" required minlength="3"
                    maxlength="30" autocomplete="off">
                @error('username')
                <div class="err">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label for="email">Email</label>
                <input class="input" id="email" type="email" name="email" value="{{ old('email') }}" required
                    autocomplete="off">
                @error('email')
                <div class="err">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label for="password">Password Sementara</label>
                <input class="input" id="password" type="text" name="password" required minlength="8" autocomplete="off">
                <div class="sub" style="margin-top:4px">Minimal 8 karakter. Admin disarankan menggantinya setelah login
                    pertama.</div>
                @error('password')
                <div class="err">{{ $message }}</div>@enderror
            </div>
            <div class="actions" style="justify-content:flex-end;margin-top:8px">
                <button type="button" class="btn" onclick="document.getElementById('addAdmin').close()">Batal</button>
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