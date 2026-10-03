@extends('superadmin.layout')
@section('title', 'Persetujuan Petshop')

@section('content')
    <div>
        <h1 class="page-title">Persetujuan Petshop</h1>
        <p class="page-sub">Tinjau pendaftaran petshop mitra, lalu setujui atau tolak.</p>
    </div>

    <div class="tabs">
        @foreach(['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'] as $key => $label)
            <a href="{{ route('superadmin.petshops', ['status' => $key]) }}" class="tab {{ $status === $key ? 'active' : '' }}">
                {{ $label }}<span class="n">({{ $counts[$key] ?? 0 }})</span>
            </a>
        @endforeach
    </div>

    <div class="card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Petshop</th>
                        <th>Pemilik</th>
                        <th>Didaftarkan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($petshops as $p)
                        <tr>
                            <td><strong>{{ $p->name ?? '-' }}</strong> <span class="pill"
                                    style="background:#e6f4ff;color:#1677ff">{{ str_replace('PAW_', 'Paw ', $p->mitra_type) }}</span>
                                <div class="sub">{{ $p->address ?? '' }}</div>
                            </td>
                            <td>{{ $p->owner_name ?? '-' }}
                                <div class="sub">{{ $p->owner_email ?? '' }}</div>
                            </td>
                            <td>{{ !empty($p->created_at) ? \Carbon\Carbon::parse($p->created_at)->setTimezone('Asia/Jakarta')->format('d M Y, H:i') : '-' }}
                            </td>
                            <td><span
                                    class="pill p-{{ $p->status }}">{{ ['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'][$p->status] ?? $p->status }}</span>
                            </td>
                            <td>
                                <div class="actions">
                                    @if($p->status !== 'approved')
                                        <form method="POST" action="{{ route('superadmin.petshops.status', $p->id) }}"
                                            onsubmit="event.preventDefault(); confirmAction(this, 'Setujui petshop ini?', false)">
                                            @csrf <input type="hidden" name="status" value="approved">
                                            <button class="btn btn-green">Setujui</button>
                                        </form>
                                    @endif
                                    @if($p->status !== 'rejected')
                                        <form method="POST" action="{{ route('superadmin.petshops.status', $p->id) }}"
                                            onsubmit="event.preventDefault(); confirmAction(this, 'Tolak petshop ini?', true)">
                                            @csrf <input type="hidden" name="status" value="rejected">
                                            <button class="btn btn-danger">Tolak</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty">Tidak ada petshop dengan status ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($petshops->hasPages())
        <div class="pagination">{{ $petshops->links('pagination::simple-default') }}</div>@endif
    </div>
@endsection