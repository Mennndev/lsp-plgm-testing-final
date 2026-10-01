@extends('layouts.asesor')

@section('title', 'Detail Pengajuan')

@section('content')
<div class="asesor-breadcrumb">
    <a href="{{ route('asesor.dashboard') }}"><i class="bi bi-house-door"></i> Dashboard</a>
    <span class="separator">›</span>
    <span>Detail Pengajuan</span>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header-brand">
        <h5><i class="bi bi-file-earmark-text"></i> Detail Pengajuan Asesi</h5>
    </div>
    <div class="card-body p-4">
        <div class="row">
            <div class="col-md-6">
                <div class="info-row">
                    <div class="info-label">Nama Asesi:</div>
                    <div class="info-value">{{ $pengajuan->user->nama }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Email:</div>
                    <div class="info-value">{{ $pengajuan->user->email }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Skema:</div>
                    <div class="info-value"><strong>{{ $pengajuan->program->nama }}</strong></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-row">
                    <div class="info-label">Tanggal Pengajuan:</div>
                    <div class="info-value">{{ $pengajuan->created_at->format('d M Y, H:i') }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Status:</div>
                    <div class="info-value">
                        <span class="badge" style="background: #D69F3A; color: #111;">{{ ucfirst($pengajuan->status) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($pengajuan->apl02 && $pengajuan->apl02->count() > 0)
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header" style="background: #f4f6fc; border-bottom: 2px solid #233C7E;">
        <h6 class="mb-0" style="color: #233C7E;">
            <i class="bi bi-clipboard-check"></i> APL-02: Asesmen Mandiri Asesi
        </h6>
    </div>
    <div class="card-body">
        <div class="alert alert-info py-2">
            <small>
                Nilai K/BK berikut merupakan <strong>asesmen mandiri Asesi</strong>, bukan keputusan akhir Asesor.
            </small>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="6%">No</th>
                        <th width="22%">Kode Unit</th>
                        <th>Unit Kompetensi</th>
                        <th width="17%" class="text-center">Penilaian Mandiri</th>
                        <th width="22%">Bukti Kompetensi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pengajuan->apl02 as $index => $apl02)
                        @php
                            $status = is_array($apl02->self_assessment)
                                ? ($apl02->self_assessment['status'] ?? null)
                                : $apl02->self_assessment;
                            $buktiList = $buktiUnit->get($apl02->unit_kompetensi_id, collect());
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $apl02->unitKompetensi->kode_unit ?? '-' }}</td>
                            <td>{{ $apl02->unitKompetensi->judul_unit ?? '-' }}</td>
                            <td class="text-center">
                                @if($status === 'K')
                                    <span class="badge bg-success">K - Kompeten</span>
                                @elseif($status === 'BK')
                                    <span class="badge bg-warning text-dark">BK - Belum Kompeten</span>
                                @else
                                    <span class="badge bg-secondary">Belum Dinilai</span>
                                @endif
                            </td>
                            <td>
                                @forelse($buktiList as $bukti)
                                    <a href="{{ asset('storage/' . $bukti->path) }}"
                                       target="_blank"
                                       class="btn btn-sm btn-outline-primary mb-1">
                                        <i class="bi bi-file-earmark-arrow-down"></i>
                                        {{ \Illuminate\Support\Str::limit($bukti->nama_file, 30) }}
                                    </a>
                                @empty
                                    <span class="text-muted">Tidak ada bukti</span>
                                @endforelse
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@elseif($pengajuan->selfAssessments && $pengajuan->selfAssessments->count() > 0)
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header" style="background: #f4f6fc; border-bottom: 2px solid #233C7E;">
        <h6 class="mb-0" style="color: #233C7E;"><i class="bi bi-clipboard-check"></i> Self-Assessment Asesi (Data Lama)</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Unit Kompetensi</th>
                        <th>Elemen</th>
                        <th>KUK</th>
                        <th>Self-Assessment</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pengajuan->selfAssessments as $sa)
                    <tr>
                        <td>{{ $sa->kriteriaUnjukKerja->elemenKompetensi->unitKompetensi->judul_unit ?? '-' }}</td>
                        <td>{{ $sa->kriteriaUnjukKerja->elemenKompetensi->nama_elemen ?? '-' }}</td>
                        <td>{{ $sa->kriteriaUnjukKerja->deskripsi ?? '-' }}</td>
                        <td>
                            <span class="badge {{ strtoupper($sa->nilai) === 'K' ? 'bg-success' : 'bg-secondary' }}">
                                {{ strtoupper($sa->nilai) === 'K' ? 'Kompeten' : 'Belum Kompeten' }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<div class="d-flex gap-2">
    <a href="{{ route('asesor.pengajuan.penilaian', $pengajuan->id) }}" class="btn btn-asesor btn-primary-asesor">
        <i class="bi bi-clipboard-check"></i> Mulai Penilaian
    </a>
    <a href="{{ route('asesor.formulir.index', $pengajuan->id) }}" class="btn btn-asesor" style="background: #D69F3A; color: #fff; border: none;">
        <i class="bi bi-file-earmark-text"></i> Formulir Asesmen
    </a>
    <a href="{{ route('asesor.dashboard') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>
@endsection
