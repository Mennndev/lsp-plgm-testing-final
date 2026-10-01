@extends('layouts.asesor')

@section('title', 'Dashboard Asesor')

@section('content')
<h4 class="mb-4" style="color: #233C7E; font-weight: 600;">Dashboard Asesor</h4>

<section class="asesor-stats">
    <div class="asesor-card">
        <div class="info">
            <h6>Total Penugasan</h6>
            <h3>{{ $summary['total_penugasan'] }}</h3>
            <small class="text-muted">Semua penugasan</small>
        </div>
        <div class="icon">
            <i class="bi bi-clipboard-data"></i>
        </div>
    </div>

    <div class="asesor-card">
        <div class="info">
            <h6>Belum Dinilai</h6>
            <h3>{{ $summary['belum_dimulai'] }}</h3>
            <small class="text-muted">Menunggu penilaian</small>
        </div>
        <div class="icon" style="background: #fff3cd; color: #856404;">
            <i class="bi bi-hourglass-split"></i>
        </div>
    </div>

    <div class="asesor-card">
        <div class="info">
            <h6>Sedang Dinilai</h6>
            <h3>{{ $summary['proses'] }}</h3>
            <small class="text-muted">Dalam proses</small>
        </div>
        <div class="icon" style="background: #fff3e0; color: #e65100;">
            <i class="bi bi-pencil-square"></i>
        </div>
    </div>

    <div class="asesor-card">
        <div class="info">
            <h6>Selesai</h6>
            <h3>{{ $summary['selesai'] }}</h3>
            <small class="text-muted">Penilaian selesai</small>
        </div>
        <div class="icon" style="background: #d4edda; color: #155724;">
            <i class="bi bi-check-circle"></i>
        </div>
    </div>
</section>

<div class="filter-card">
    <form method="GET" action="{{ route('asesor.dashboard') }}" class="row g-3 align-items-end">
        <div class="col-md-5">
            <label class="form-label fw-semibold" style="color: #233C7E;">Cari Asesi / Skema / Status</label>
            <input type="text" name="q" value="{{ $search }}" class="form-control" placeholder="Contoh: Lukman / Operator / approved">
        </div>
        <div class="col-md-4">
            <label class="form-label fw-semibold" style="color: #233C7E;">Status Penilaian</label>
            <select name="status_penilaian" class="form-select">
                <option value="all" {{ $penilaianStatus === 'all' ? 'selected' : '' }}>Semua</option>
                <option value="belum_dimulai" {{ $penilaianStatus === 'belum_dimulai' ? 'selected' : '' }}>Belum Dinilai</option>
                <option value="proses" {{ $penilaianStatus === 'proses' ? 'selected' : '' }}>Sedang Dinilai</option>
                <option value="selesai" {{ $penilaianStatus === 'selesai' ? 'selected' : '' }}>Selesai</option>
            </select>
        </div>
        <div class="col-md-3">
            <button class="btn btn-primary w-100" type="submit" style="background: #233C7E; border: none;">
                <i class="bi bi-search"></i> Terapkan Filter
            </button>
        </div>
    </form>
</div>

<div class="asesor-table">
    <h5>Daftar Penugasan</h5>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Asesi</th>
                    <th>Skema</th>
                    <th>Status Pengajuan</th>
                    <th style="min-width: 240px;">Progress Penilaian</th>
                    <th>Tanggal Pengajuan</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengajuanList as $row)
                    @php
                        $statusClass = [
                            'belum_dimulai' => 'bg-secondary',
                            'proses' => 'bg-warning text-dark',
                            'selesai' => 'bg-success',
                        ][$row['status_penilaian']] ?? 'bg-secondary';

                        $statusText = [
                            'belum_dimulai' => 'Belum Dinilai',
                            'proses' => 'Sedang Dinilai',
                            'selesai' => 'Selesai',
                        ][$row['status_penilaian']] ?? 'Tidak Diketahui';
                    @endphp
                    <tr>
                        <td><strong>{{ $row['nama_asesi'] }}</strong></td>
                        <td>{{ $row['nama_skema'] }}</td>
                        <td>
                            <span class="badge" style="background: #D69F3A; color: #111;">{{ ucfirst($row['status_pengajuan']) }}</span>
                        </td>
                        <td>
                            <div class="d-flex justify-content-between small mb-1">
                                <span><strong>{{ $row['dinilai'] }}</strong> / {{ $row['total_penilaian'] }} {{ $row['progress_label'] }}</span>
                                <span class="fw-semibold" style="color: #233C7E;">{{ $row['persentase'] }}%</span>
                            </div>
                            <div class="progress" style="height: 6px;" role="progressbar" aria-label="Persentase kemajuan penilaian" aria-valuenow="{{ $row['persentase'] }}" aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar" style="width: {{ $row['persentase'] }}%; background: #233C7E;"></div>
                            </div>
                            <span class="badge mt-2 {{ $statusClass }}">{{ $statusText }}</span>
                        </td>
                        <td>{{ $row['tanggal_pengajuan'] ?? '-' }}</td>
                        <td class="text-end">
                            <a href="{{ route('asesor.pengajuan.show', $row['pengajuan_id']) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                            @if($row['status_penilaian'] === 'selesai')
                                <a href="{{ route('asesor.pengajuan.penilaian', $row['pengajuan_id']) }}" class="btn btn-sm btn-success">
                                    <i class="bi bi-clipboard-check"></i> Lihat Hasil
                                </a>
                            @elseif($row['status_penilaian'] === 'proses')
                                <a href="{{ route('asesor.pengajuan.penilaian', $row['pengajuan_id']) }}" class="btn btn-sm" style="background: #D69F3A; color: #111; border: none;">
                                    <i class="bi bi-arrow-right-circle"></i> Lanjutkan
                                </a>
                            @else
                                <a href="{{ route('asesor.pengajuan.penilaian', $row['pengajuan_id']) }}" class="btn btn-sm" style="background: #233C7E; color: #fff; border: none;">
                                    <i class="bi bi-play-circle"></i> Mulai Penilaian
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="bi bi-inbox" style="font-size: 32px;"></i>
                            <div class="mt-2">Tidak ada data penugasan untuk filter saat ini.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pengajuanList->hasPages())
        <div class="mt-3">
            {{ $pengajuanList->appends(['q' => $search, 'status_penilaian' => $penilaianStatus])->links() }}
        </div>
    @endif
</div>
@endsection
