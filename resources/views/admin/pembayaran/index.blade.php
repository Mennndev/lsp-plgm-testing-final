@extends('layouts.admin')

@section('title', 'Pembayaran - Admin LSP PLGM')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Manajemen Pembayaran</h1>
            <p class="text-muted mb-0">Status pembayaran diperbarui otomatis melalui Midtrans.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.pembayaran.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Status Pembayaran</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        @foreach([
                            'pending' => 'Menunggu Pembayaran',
                            'processing' => 'Sedang Diproses',
                            'success' => 'Berhasil',
                            'failed' => 'Gagal',
                            'expired' => 'Kadaluarsa',
                            'refunded' => 'Dikembalikan',
                        ] as $value => $label)
                            <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Filter</button>
                    <a href="{{ route('admin.pembayaran.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Order ID</th>
                            <th>Peserta</th>
                            <th>Skema</th>
                            <th>Nominal</th>
                            <th>Metode</th>
                            <th>Status</th>
                            <th>Terakhir Diperbarui</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pembayaranList as $pembayaran)
                            <tr>
                                <td><code>{{ $pembayaran->order_id }}</code></td>
                                <td>{{ $pembayaran->user->nama ?? '-' }}</td>
                                <td>{{ $pembayaran->pengajuan->program->nama ?? '-' }}</td>
                                <td class="fw-semibold">{{ $pembayaran->formatted_nominal }}</td>
                                <td>{{ $pembayaran->payment_type ? strtoupper(str_replace('_', ' ', $pembayaran->payment_type)) : '-' }}</td>
                                <td><span class="badge bg-{{ $pembayaran->status_badge_color }}">{{ $pembayaran->status_label }}</span></td>
                                <td>{{ $pembayaran->updated_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('admin.pembayaran.show', $pembayaran->id) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">Belum ada data pembayaran.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($pembayaranList->hasPages())
                <div class="mt-3">{{ $pembayaranList->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
