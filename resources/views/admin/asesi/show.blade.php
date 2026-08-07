@extends('layouts.admin')

@section('title', 'Detail Asesi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Detail Asesi</h4>
        <p class="text-muted mb-0">Data pendaftaran peserta sertifikasi.</p>
    </div>
    <a href="{{ route('admin.asesi.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="text-muted small">Nama</div>
                <div class="fw-semibold">{{ $asesi->user->nama ?? '-' }}</div>
            </div>
            <div class="col-md-6">
                <div class="text-muted small">Email</div>
                <div class="fw-semibold">{{ $asesi->email ?? $asesi->user->email ?? '-' }}</div>
            </div>
            <div class="col-md-6">
                <div class="text-muted small">Jenis Kelamin</div>
                <div>{{ $asesi->jenis_kelamin ?? '-' }}</div>
            </div>
            <div class="col-md-6">
                <div class="text-muted small">Tempat / Tanggal Lahir</div>
                <div>{{ $asesi->tempat_lahir ?? '-' }} / {{ $asesi->tanggal_lahir?->format('d/m/Y') ?? '-' }}</div>
            </div>
            <div class="col-md-6">
                <div class="text-muted small">NIK</div>
                <div>{{ $asesi->no_ktp ?? '-' }}</div>
            </div>
            <div class="col-md-6">
                <div class="text-muted small">Skema</div>
                <div>{{ $asesi->program->nama ?? $asesi->skema ?? '-' }}</div>
            </div>
            <div class="col-md-6">
                <div class="text-muted small">Pekerjaan</div>
                <div>{{ $asesi->pekerjaan ?? '-' }}</div>
            </div>
            <div class="col-md-6">
                <div class="text-muted small">Instansi</div>
                <div>{{ $asesi->instansi ?? '-' }}</div>
            </div>
            <div class="col-12">
                <div class="text-muted small">Alamat</div>
                <div>{{ $asesi->alamat ?? '-' }}{{ $asesi->kota ? ', '.$asesi->kota : '' }}{{ $asesi->provinsi ? ', '.$asesi->provinsi : '' }}</div>
            </div>
            <div class="col-md-6">
                <div class="text-muted small">Status Persetujuan</div>
                <span class="badge bg-{{ $asesi->setuju ? 'success' : 'warning' }}">
                    {{ $asesi->setuju ? 'Disetujui' : 'Pending' }}
                </span>
            </div>
            <div class="col-md-6">
                <div class="text-muted small">Tanggal Daftar</div>
                <div>{{ $asesi->created_at?->format('d/m/Y H:i') ?? '-' }}</div>
            </div>
        </div>

        @if($asesi->ktp_path || $asesi->ttd_path)
            <hr>
            <div class="d-flex flex-wrap gap-2">
                @if($asesi->ktp_path)
                    <a href="{{ asset('storage/'.$asesi->ktp_path) }}" target="_blank" class="btn btn-outline-primary">
                        <i class="bi bi-card-image me-1"></i> Lihat KTP
                    </a>
                @endif
                @if($asesi->ttd_path)
                    <a href="{{ asset('storage/'.$asesi->ttd_path) }}" target="_blank" class="btn btn-outline-success">
                        <i class="bi bi-pen me-1"></i> Lihat Tanda Tangan
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
