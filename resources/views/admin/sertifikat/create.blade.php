@extends('layouts.admin')

@section('title', $pengajuan->sertifikat ? 'Edit Sertifikat' : 'Upload Sertifikat')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h1 class="h4 mb-1">{{ $pengajuan->sertifikat ? 'Edit Sertifikat' : 'Upload Sertifikat' }}</h1>
            <p class="text-muted mb-0">Simpan file sertifikat hasil sertifikasi asesi.</p>
        </div>
        <a href="{{ route('admin.sertifikat.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light fw-semibold">Data Asesi</div>
                <div class="card-body">
                    <p class="mb-2"><strong>Nama:</strong><br>{{ $pengajuan->user->nama ?? '-' }}</p>
                    <p class="mb-2"><strong>Email:</strong><br>{{ $pengajuan->user->email ?? '-' }}</p>
                    <p class="mb-2"><strong>Skema:</strong><br>{{ $pengajuan->program->nama ?? '-' }}</p>
                    <p class="mb-0">
                        <strong>Status Asesmen:</strong><br>
                        <span class="badge bg-{{ $pengajuan->jadwalAsesmen?->status_badge ?? 'secondary' }}">
                            {{ $pengajuan->jadwalAsesmen?->status_label ?? '-' }}
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('admin.sertifikat.store', $pengajuan->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Nomor Sertifikat</label>
                            <input type="text" name="nomor_sertifikat" class="form-control @error('nomor_sertifikat') is-invalid @enderror"
                                   value="{{ old('nomor_sertifikat', $pengajuan->sertifikat->nomor_sertifikat ?? '') }}" required>
                            @error('nomor_sertifikat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jenis Bukti</label>
                            <input type="text" name="jenis_bukti" class="form-control @error('jenis_bukti') is-invalid @enderror"
                                   value="{{ old('jenis_bukti', $pengajuan->sertifikat->jenis_bukti ?? 'Sertifikat Kompetensi') }}">
                            @error('jenis_bukti')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Terbit</label>
                                <input type="date" name="tanggal_terbit" class="form-control @error('tanggal_terbit') is-invalid @enderror"
                                       value="{{ old('tanggal_terbit', optional($pengajuan->sertifikat->tanggal_terbit ?? now())->format('Y-m-d')) }}" required>
                                @error('tanggal_terbit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Berlaku Sampai</label>
                                <input type="date" name="tanggal_berlaku_sampai" class="form-control @error('tanggal_berlaku_sampai') is-invalid @enderror"
                                       value="{{ old('tanggal_berlaku_sampai', optional($pengajuan->sertifikat->tanggal_berlaku_sampai ?? now()->addYears(3))->format('Y-m-d')) }}" required>
                                @error('tanggal_berlaku_sampai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">File Sertifikat</label>
                            <input type="file" name="file_sertifikat" class="form-control @error('file_sertifikat') is-invalid @enderror" {{ $pengajuan->sertifikat?->file_sertifikat ? '' : 'required' }}>
                            <small class="text-muted">Format PDF, JPG, JPEG, atau PNG. Maksimal 2MB.</small>
                            @error('file_sertifikat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            @if($pengajuan->sertifikat?->file_sertifikat)
                                <div class="mt-2">
                                    <a href="{{ asset('storage/' . $pengajuan->sertifikat->file_sertifikat) }}" target="_blank" class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-file-earmark-check"></i> Lihat file saat ini
                                    </a>
                                </div>
                            @endif
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan Sertifikat
                            </button>
                            <a href="{{ route('admin.pengajuan.show', $pengajuan->id) }}" class="btn btn-outline-secondary">
                                Detail Pengajuan
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
