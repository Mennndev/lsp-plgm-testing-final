@extends('layouts.admin')

@section('title', 'Detail Pembayaran - Admin LSP PLGM')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Detail Pembayaran</h1>
            <p class="text-muted mb-0"><code>{{ $pembayaran->order_id }}</code></p>
        </div>
        <a href="{{ route('admin.pembayaran.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-primary text-white"><strong>Informasi Pembayaran</strong></div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Status</dt>
                        <dd class="col-sm-7"><span class="badge bg-{{ $pembayaran->status_badge_color }}">{{ $pembayaran->status_label }}</span></dd>

                        <dt class="col-sm-5">Nominal</dt>
                        <dd class="col-sm-7 fw-bold">{{ $pembayaran->formatted_nominal }}</dd>

                        <dt class="col-sm-5">Metode</dt>
                        <dd class="col-sm-7">{{ $pembayaran->payment_type ? strtoupper(str_replace('_', ' ', $pembayaran->payment_type)) : '-' }}</dd>

                        <dt class="col-sm-5">Transaction ID</dt>
                        <dd class="col-sm-7"><code>{{ $pembayaran->transaction_id ?? '-' }}</code></dd>

                        <dt class="col-sm-5">Status Midtrans</dt>
                        <dd class="col-sm-7">{{ $pembayaran->transaction_status ?? '-' }}</dd>

                        <dt class="col-sm-5">Waktu Transaksi</dt>
                        <dd class="col-sm-7">{{ $pembayaran->transaction_time?->format('d/m/Y H:i:s') ?? '-' }}</dd>

                        <dt class="col-sm-5">Dibayar</dt>
                        <dd class="col-sm-7">{{ $pembayaran->paid_at?->format('d/m/Y H:i:s') ?? '-' }}</dd>

                        <dt class="col-sm-5">Kadaluarsa</dt>
                        <dd class="col-sm-7">{{ $pembayaran->expired_at?->format('d/m/Y H:i:s') ?? '-' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-info text-white"><strong>Peserta & Pengajuan</strong></div>
                <div class="card-body">
                    <dl class="row mb-3">
                        <dt class="col-sm-4">Nama</dt>
                        <dd class="col-sm-8">{{ $pembayaran->user->nama ?? '-' }}</dd>

                        <dt class="col-sm-4">Email</dt>
                        <dd class="col-sm-8">{{ $pembayaran->user->email ?? '-' }}</dd>

                        <dt class="col-sm-4">Skema</dt>
                        <dd class="col-sm-8">{{ $pembayaran->pengajuan->program->nama ?? '-' }}</dd>

                        <dt class="col-sm-4">Status Pengajuan</dt>
                        <dd class="col-sm-8"><span class="badge bg-{{ $pembayaran->pengajuan->status_badge_color }}">{{ $pembayaran->pengajuan->status_label }}</span></dd>
                    </dl>

                    <a href="{{ route('admin.pengajuan.show', $pembayaran->pengajuan_skema_id) }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-file-earmark-text"></i> Lihat Pengajuan
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if($pembayaran->payment_details)
        <div class="card mt-4">
            <div class="card-header"><strong>Detail Respons Midtrans</strong></div>
            <div class="card-body">
                <pre class="mb-0 small" style="white-space: pre-wrap;">{{ json_encode($pembayaran->payment_details, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
            </div>
        </div>
    @endif
</div>
@endsection
