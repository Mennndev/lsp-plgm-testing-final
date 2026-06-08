@extends('layouts.admin')

@section('title', 'Laporan Admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h1 class="h4 mb-1">Laporan Admin</h1>
            <p class="text-muted mb-0">Ringkasan pengajuan, pembayaran, asesmen, dan sertifikat.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Tanggal Dari</label>
                    <input type="date" name="tanggal_dari" class="form-control" value="{{ request('tanggal_dari') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Sampai</label>
                    <input type="date" name="tanggal_sampai" class="form-control" value="{{ request('tanggal_sampai') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Program</label>
                    <select name="program" class="form-select">
                        <option value="">Semua program</option>
                        @foreach($programs as $program)
                            <option value="{{ $program->id }}" @selected((string) request('program') === (string) $program->id)>
                                {{ $program->kode_skema }} - {{ $program->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                    <a href="{{ route('admin.laporan.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <section class="admin-stats">
        <div class="admin-card">
            <div class="info">
                <h6>Total Pengajuan</h6>
                <h3>{{ $statistik['total_pengajuan'] }}</h3>
                <small class="text-muted">Berdasarkan filter laporan</small>
            </div>
            <div class="icon"><i class="bi bi-file-earmark-text"></i></div>
        </div>

        <div class="admin-card">
            <div class="info">
                <h6>Disetujui/Dibayar</h6>
                <h3>{{ $statistik['pengajuan_disetujui'] }}</h3>
                <small class="text-muted">Pengajuan siap proses lanjut</small>
            </div>
            <div class="icon"><i class="bi bi-check2-circle"></i></div>
        </div>

        <div class="admin-card">
            <div class="info">
                <h6>Asesmen Selesai</h6>
                <h3>{{ $statistik['asesmen_selesai'] }}</h3>
                <small class="text-muted">Jadwal berstatus selesai</small>
            </div>
            <div class="icon"><i class="bi bi-calendar-check"></i></div>
        </div>

        <div class="admin-card">
            <div class="info">
                <h6>Sertifikat Terbit</h6>
                <h3>{{ $statistik['sertifikat_terbit'] }}</h3>
                <small class="text-muted">Sertifikat tersimpan</small>
            </div>
            <div class="icon"><i class="bi bi-award"></i></div>
        </div>
    </section>

    <div class="row">
        <div class="col-xl-8 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light fw-semibold">Daftar Pengajuan</div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                        <tr>
                            <th>Asesi</th>
                            <th>Program</th>
                            <th>Status</th>
                            <th>Pembayaran</th>
                            <th>Jadwal</th>
                            <th>Sertifikat</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($pengajuanList as $pengajuan)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $pengajuan->user->nama ?? '-' }}</div>
                                    <small class="text-muted">{{ $pengajuan->user->email ?? '-' }}</small>
                                </td>
                                <td>{{ $pengajuan->program->nama ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ $pengajuan->status_badge_color }} {{ $pengajuan->status_badge_color === 'warning' ? 'text-dark' : '' }}">
                                        {{ $pengajuan->status_label }}
                                    </span>
                                </td>
                                <td>
                                    @if($pengajuan->pembayaran)
                                        <span class="badge bg-{{ $pengajuan->pembayaran->status_badge_color }} {{ $pengajuan->pembayaran->status_badge_color === 'warning' ? 'text-dark' : '' }}">
                                            {{ $pengajuan->pembayaran->status_label }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $pengajuan->jadwalAsesmen?->status_label ?? '-' }}</td>
                                <td>
                                    @if($pengajuan->sertifikat)
                                        <span class="badge bg-success">Terbit</span>
                                    @else
                                        <span class="badge bg-secondary">Belum</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Tidak ada data laporan.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    {{ $pengajuanList->links() }}
                </div>
            </div>
        </div>

        <div class="col-xl-4 mb-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light fw-semibold">Ringkasan Pembayaran</div>
                <div class="card-body">
                    <p class="mb-1 text-muted">Pembayaran berhasil</p>
                    <h4 class="mb-3">{{ $statistik['pembayaran_berhasil'] }}</h4>
                    <p class="mb-1 text-muted">Total pendapatan</p>
                    <h4 class="mb-0">Rp {{ number_format($statistik['total_pendapatan'], 0, ',', '.') }}</h4>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light fw-semibold">Status Pengajuan</div>
                <div class="card-body">
                    @forelse($statusPengajuan as $status => $total)
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <span>{{ ucfirst($status) }}</span>
                            <strong>{{ $total }}</strong>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Belum ada data status.</p>
                    @endforelse
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-light fw-semibold">Top Program</div>
                <div class="card-body">
                    @forelse($pengajuanPerProgram as $row)
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <span>{{ \Illuminate\Support\Str::limit($row->program->nama ?? '-', 28) }}</span>
                            <strong>{{ $row->total }}</strong>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Belum ada data program.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
