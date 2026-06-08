@extends('layouts.admin')

@section('title', 'Sertifikat')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h1 class="h4 mb-1">Upload Sertifikat</h1>
            <p class="text-muted mb-0">Kelola file sertifikat untuk hasil asesmen yang sudah selesai.</p>
        </div>
        <a href="{{ route('admin.pengajuan.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-file-earmark-text"></i> Data Pengajuan
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <section class="admin-stats">
        <div class="admin-card">
            <div class="info">
                <h6>Siap Upload</h6>
                <h3>{{ $totalSiapUpload }}</h3>
                <small class="text-muted">Asesmen selesai tanpa sertifikat</small>
            </div>
            <div class="icon"><i class="bi bi-upload"></i></div>
        </div>

        <div class="admin-card">
            <div class="info">
                <h6>Sertifikat Terbit</h6>
                <h3>{{ $totalTerbit }}</h3>
                <small class="text-muted">Total sertifikat tersimpan</small>
            </div>
            <div class="icon"><i class="bi bi-award"></i></div>
        </div>
    </section>

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-lg-6">
                    <label class="form-label">Pencarian</label>
                    <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Nama asesi, email, program, atau nomor sertifikat">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status Sertifikat</label>
                    <select name="status" class="form-select">
                        <option value="">Semua status</option>
                        <option value="belum" @selected(request('status') === 'belum')>Belum upload</option>
                        <option value="terbit" @selected(request('status') === 'terbit')>Sudah terbit</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Filter
                    </button>
                    <a href="{{ route('admin.sertifikat.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th>Asesi</th>
                    <th>Program</th>
                    <th>Jadwal Selesai</th>
                    <th>Asesor</th>
                    <th>Status Sertifikat</th>
                    <th style="width: 220px;">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse($pengajuanList as $pengajuan)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $pengajuan->user->nama ?? '-' }}</div>
                            <small class="text-muted">{{ $pengajuan->user->email ?? '-' }}</small>
                        </td>
                        <td>
                            <div>{{ $pengajuan->program->nama ?? '-' }}</div>
                            <small class="text-muted">{{ $pengajuan->program->kode_skema ?? '-' }}</small>
                        </td>
                        <td>{{ $pengajuan->jadwalAsesmen?->tanggal_mulai?->format('d/m/Y H:i') ?? '-' }}</td>
                        <td>{{ $pengajuan->jadwalAsesmen?->asesor?->nama ?? '-' }}</td>
                        <td>
                            @if($pengajuan->sertifikat)
                                <span class="badge bg-success">Sudah terbit</span>
                                <div class="small text-muted mt-1">{{ $pengajuan->sertifikat->nomor_sertifikat }}</div>
                            @else
                                <span class="badge bg-warning text-dark">Belum upload</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('admin.sertifikat.create', $pengajuan->id) }}"
                                   class="btn btn-sm {{ $pengajuan->sertifikat ? 'btn-outline-primary' : 'btn-warning' }}">
                                    <i class="bi bi-upload"></i>
                                    {{ $pengajuan->sertifikat ? 'Edit' : 'Upload' }}
                                </a>

                                <a href="{{ route('admin.pengajuan.show', $pengajuan->id) }}"
                                   class="btn btn-sm btn-outline-secondary">
                                    Detail
                                </a>

                                @if($pengajuan->sertifikat?->file_sertifikat)
                                    <a href="{{ asset('storage/' . $pengajuan->sertifikat->file_sertifikat) }}"
                                       target="_blank"
                                       class="btn btn-sm btn-success">
                                        Lihat File
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            Belum ada asesmen selesai yang cocok dengan filter.
                        </td>
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
@endsection
