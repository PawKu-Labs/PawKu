@extends('admin.layout')
@section('title', 'Persetujuan Petshop')

@section('content')
    @php
        $statusLabels = ['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'];
    @endphp

    <div class="hero">
        <div>
            <h1 class="page-title">Persetujuan Petshop</h1>
            <p class="page-sub">Tinjau pendaftaran petshop mitra PawKu, lalu berikan keputusan setujui atau tolak.</p>
        </div>
    </div>

    <div class="tabs">
        @foreach($statusLabels as $key => $label)
            <a href="{{ route('admin.petshops', ['status' => $key]) }}" class="tab {{ $status === $key ? 'active' : '' }}">
                {{ $label }}
                <span class="n">({{ $counts[$key] ?? 0 }})</span>
            </a>
        @endforeach
    </div>

    <div class="card" style="padding:0;overflow:hidden;">
        <div class="table-wrap">
            <table class="responsive">
                <thead>
                    <tr>
                        <th class="cell-first">Petshop</th>
                        <th>Pemilik & Kontak</th>
                        <th>Didaftarkan</th>
                        <th>Status</th>
                        <th class="cell-last" style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($petshops as $p)
                        <tr>
                            <td class="cell-first stack" data-label="">
                                <div style="display:flex;align-items:center;gap:12px;min-width:0;">
                                    @if(!empty($p->photo_url) || !empty($p->logo_url))
                                        <img src="{{ $p->photo_url ?: $p->logo_url }}" alt="{{ $p->name }}"
                                            style="width:44px;height:44px;border-radius:10px;object-fit:cover;border:1px solid #e2e8f0;flex-shrink:0;">
                                    @else
                                        <div class="stat-icon i-blue" style="width:44px;height:44px;">🏪</div>
                                    @endif
                                    <div style="min-width:0;">
                                        <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                                            <strong>{{ $p->name ?? '-' }}</strong>
                                            <span
                                                class="pill c-user">{{ str_replace('_', ' ', $p->mitra_type ?? 'Petshop') }}</span>
                                        </div>
                                        <div class="sub"
                                            style="max-width:320px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                            {{ $p->address ?? 'Alamat belum diatur' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td data-label="Pemilik">
                                <div>
                                    <div style="font-weight:600;">{{ $p->owner_name ?? '-' }}</div>
                                    <div class="sub" style="overflow-wrap:anywhere;">{{ $p->owner_email ?? '-' }}</div>
                                    @if(!empty($p->phone_number))
                                        <div class="sub" style="color:#0958d9;">📞 {{ $p->phone_number }}</div>
                                    @endif
                                </div>
                            </td>
                            <td data-label="Didaftarkan">
                                <div>
                                    {{ !empty($p->created_at) ? \Carbon\Carbon::parse($p->created_at)->setTimezone('Asia/Jakarta')->format('d M Y, H:i') : '-' }}
                                    <span class="sub">WIB</span>
                                </div>
                            </td>
                            <td data-label="Status">
                                <span class="pill p-{{ $p->status }}">{{ $statusLabels[$p->status] ?? $p->status }}</span>
                            </td>
                            <td class="cell-last" data-label="">
                                <div class="actions" style="justify-content:flex-end;">
                                    <button type="button" class="btn btn-outline" data-item="{{ json_encode($p) }}"
                                        onclick="showDetailModal(JSON.parse(this.dataset.item))" title="Lihat rincian petshop">
                                        Detail
                                    </button>

                                    @if($p->status !== 'approved')
                                        <form method="POST" action="{{ route('admin.petshops.status', $p->id) }}"
                                            data-message="Setujui pendaftaran petshop {{ $p->name }}?"
                                            onsubmit="event.preventDefault(); confirmAction(this, this.dataset.message, false)">
                                            @csrf
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="btn btn-green">Setujui</button>
                                        </form>
                                    @endif

                                    @if($p->status !== 'rejected')
                                        <form method="POST" action="{{ route('admin.petshops.status', $p->id) }}"
                                            onsubmit="event.preventDefault(); promptReject(this)">
                                            @csrf
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="btn btn-danger">Tolak</button>
                                        </form>
                                    @endif

                                    @if($p->status === 'rejected')
                                        <form method="POST" action="{{ route('admin.petshops.status', $p->id) }}"
                                            data-message="Kembalikan petshop ini ke status Menunggu?"
                                            onsubmit="event.preventDefault(); confirmAction(this, this.dataset.message, false)">
                                            @csrf
                                            <input type="hidden" name="status" value="pending">
                                            <button type="submit" class="btn btn-outline" title="Tinjau ulang">Review ulang</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty">
                                <div style="font-size:32px;margin-bottom:8px;">📋</div>
                                <div>Tidak ada pendaftaran petshop dengan status
                                    <strong>{{ $statusLabels[$status] ?? $status }}</strong>.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($petshops->hasPages())
            <div class="pager">{{ $petshops->links('pagination::simple-default') }}</div>
        @endif
    </div>

    <dialog id="detailModal">
        <div class="modal-header">
            <div class="modal-title" id="mName">Rincian Petshop</div>
            <button class="modal-close" type="button" aria-label="Tutup"
                onclick="document.getElementById('detailModal').close()">&times;</button>
        </div>
        <div style="max-height:70vh;overflow-y:auto;padding-right:4px;">
            <div id="mPhotoWrap" style="margin-bottom:16px;text-align:center;display:none;">
                <img id="mPhoto" src="" alt="Foto Petshop"
                    style="width:100%;max-height:200px;object-fit:cover;border-radius:12px;border:1px solid #e2e8f0;">
            </div>

            <div class="detail-row"><span class="lbl">Tipe Mitra</span><span class="val" id="mType">-</span></div>
            <div class="detail-row"><span class="lbl">Pemilik</span><span class="val" id="mOwner">-</span></div>
            <div class="detail-row"><span class="lbl">Nomor Telepon</span><span class="val" id="mPhone">-</span></div>
            <div class="detail-row"><span class="lbl">Alamat Lengkap</span><span class="val" id="mAddress">-</span></div>
            <div class="detail-row"><span class="lbl">Deskripsi</span><span class="val" id="mDesc"
                    style="white-space:pre-line;color:#475569;">-</span></div>

            <div class="detail-row" id="mRulesWrap" style="display:none;">
                <span class="lbl">Aturan & Kebijakan Petshop</span>
                <span class="val" id="mRules" style="white-space:pre-line;color:#475569;">-</span>
            </div>

            <div class="detail-row" id="mOwnerNoteWrap" style="display:none;">
                <span class="lbl">Catatan Pemilik</span>
                <span class="val" id="mOwnerNote" style="white-space:pre-line;color:#475569;">-</span>
            </div>

            <div class="detail-row" id="mRejectionWrap" style="display:none;">
                <span class="lbl" style="color:#ef4444;">Alasan Penolakan</span>
                <span class="val" id="mRejection"
                    style="color:#cf1322;background:#fff2f0;padding:8px 12px;border-radius:8px;border:1px solid #ffccc7;">-</span>
            </div>
        </div>
        <div style="margin-top:20px;display:flex;justify-content:flex-end;">
            <button class="btn btn-outline" type="button"
                onclick="document.getElementById('detailModal').close()">Tutup</button>
        </div>
    </dialog>

    @push('scripts')
        <script>
            function showDetailModal(item) {
                const $ = (id) => document.getElementById(id);
                $('mName').textContent = item.name || 'Rincian Petshop';
                $('mType').textContent = (item.mitra_type || 'PETSHOP').replace(/_/g, ' ');
                $('mOwner').textContent = (item.owner_name || '-') + (item.owner_email ? ' (' + item.owner_email + ')' : '');
                $('mPhone').textContent = item.phone_number || '-';
                $('mAddress').textContent = item.address || '-';
                $('mDesc').textContent = item.description || 'Tidak ada deskripsi.';

                const pic = item.photo_url || item.logo_url;
                if (pic) { $('mPhoto').src = pic; $('mPhotoWrap').style.display = 'block'; }
                else { $('mPhotoWrap').style.display = 'none'; }

                [['mRulesWrap', 'mRules', item.rules_text],
                ['mOwnerNoteWrap', 'mOwnerNote', item.owner_note],
                ['mRejectionWrap', 'mRejection', item.rejection_reason]].forEach(([wrap, el, val]) => {
                    if (val) { $(el).textContent = val; $(wrap).style.display = 'flex'; }
                    else { $(wrap).style.display = 'none'; }
                });

                $('detailModal').showModal();
            }
        </script>
    @endpush
@endsection