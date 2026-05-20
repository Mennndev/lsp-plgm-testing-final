@extends('layouts.admin')

@section('title', 'Terbitkan Sertifikat')

@section('content')
<div class="container-fluid">
    <h1 class="h4 mb-4">Terbitkan Sertifikat</h1>

    <div class="card mb-4">
        <div class="card-body">
            <p><strong>Nama Asesi:</strong> {{ $pengajuan->user->nama ?? '-' }}</p>
            <p><strong>Email:</strong> {{ $pengajuan->user->email ?? '-' }}</p>
            <p><strong>Skema:</strong> {{ $pengajuan->program->nama ?? '-' }}</p>
            <p><strong>Status Pengajuan:</strong> {{ $pengajuan->status_label ?? $pengajuan->status }}</p>
        </div>
    </div>

    <div class="card">
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
                    <input type="text" name="jenis_bukti" class="form-control"
                           value="{{ old('jenis_bukti', $pengajuan->sertifikat->jenis_bukti ?? 'Sertifikat Kompetensi') }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Tanggal Terbit</label>
                    <input type="date" name="tanggal_terbit" class="form-control @error('tanggal_terbit') is-invalid @enderror"
                           value="{{ old('tanggal_terbit', optional($pengajuan->sertifikat->tanggal_terbit ?? null)->format('Y-m-d')) }}" required>
                    @error('tanggal_terbit')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Berlaku Sampai</label>
                    <input type="date" name="tanggal_berlaku_sampai" class="form-control @error('tanggal_berlaku_sampai') is-invalid @enderror"
                           value="{{ old('tanggal_berlaku_sampai', optional($pengajuan->sertifikat->tanggal_berlaku_sampai ?? null)->format('Y-m-d')) }}" required>
                    @error('tanggal_berlaku_sampai')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Upload File Sertifikat</label>
                    <input type="file" name="file_sertifikat" class="form-control @error('file_sertifikat') is-invalid @enderror">
                    <small class="text-muted">Format: PDF, JPG, JPEG, PNG. Maksimal 2MB.</small>
                    @error('file_sertifikat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    @if(!empty($pengajuan->sertifikat->file_sertifikat))
                        <div class="mt-2">
                            <a href="{{ asset('storage/' . $pengajuan->sertifikat->file_sertifikat) }}" target="_blank">
                                Lihat file sertifikat saat ini
                            </a>
                        </div>
                    @endif
                </div>

                <button type="submit" class="btn btn-primary">Simpan Sertifikat</button>
                <a href="{{ route('admin.pengajuan.show', $pengajuan->id) }}" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>
@endsection
