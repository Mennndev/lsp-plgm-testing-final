@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h1 class="h4 mb-1">Dashboard Admin</h1>
            <p class="text-muted mb-0">Ringkasan operasional sertifikasi LSP PLGM.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.sertifikat.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-award"></i> Upload Sertifikat
            </a>
            <a href="{{ route('admin.laporan.index') }}" class="btn btn-primary">
                <i class="bi bi-bar-chart"></i> Lihat Laporan
            </a>
        </div>
    </div>

    <section class="admin-stats">
        <div class="admin-card">
            <div class="info">
                <h6>Program Aktif</h6>
                <h3>{{ $totalProgram ?? 0 }}</h3>
                <small class="text-muted">Program yang sudah dipublish</small>
            </div>
            <div class="icon"><i class="bi bi-journal-text"></i></div>
        </div>

        <div class="admin-card">
            <div class="info">
                <h6>Pengajuan Menunggu</h6>
                <h3>{{ $totalPengajuanMenunggu ?? 0 }}</h3>
                <small class="text-muted">Perlu ditinjau admin</small>
            </div>
            <div class="icon"><i class="bi bi-hourglass-split"></i></div>
        </div>

        <div class="admin-card">
            <div class="info">
                <h6>Asesmen Selesai</h6>
                <h3>{{ $totalJadwalSelesai ?? 0 }}</h3>
                <small class="text-muted">Siap proses hasil sertifikasi</small>
            </div>
            <div class="icon"><i class="bi bi-calendar-check"></i></div>
        </div>

        <div class="admin-card">
            <div class="info">
                <h6>Sertifikat Terbit</h6>
                <h3>{{ $totalSertifikatTerbit ?? 0 }}</h3>
                <small class="text-muted">{{ $totalSertifikatPerluUpload ?? 0 }} masih perlu upload</small>
            </div>
            <div class="icon"><i class="bi bi-award"></i></div>
        </div>
    </section>

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="admin-table">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">Program Pelatihan Terbaru</h5>
                    <a href="{{ route('admin.program-pelatihan.index') }}" class="btn btn-sm btn-outline-primary">
                        Semua Program
                    </a>
                </div>

                <div class="table-responsive">
                    <table>
                        <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama Program</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th style="width: 100px;">Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($programTerbaru as $program)
                            <tr>
                                <td>{{ $program->kode_skema }}</td>
                                <td>{{ $program->nama }}</td>
                                <td>{{ $program->kategori }}</td>
                                <td>
                                    @if($program->is_published)
                                        <span class="badge bg-success">Publish</span>
                                    @else
                                        <span class="badge bg-secondary">Draft</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.program-pelatihan.edit', $program->id) }}"
                                       class="btn-admin btn-primary-admin btn-sm"
                                       title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="{{ url('skema/'.$program->slug) }}"
                                       class="btn-admin btn-sm bg-light"
                                       title="Lihat"
                                       target="_blank">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Belum ada data program pelatihan.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="admin-table">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">Sertifikat Perlu Upload</h5>
                    <a href="{{ route('admin.sertifikat.index', ['status' => 'belum']) }}" class="btn btn-sm btn-outline-primary">
                        Kelola Sertifikat
                    </a>
                </div>

                <div class="table-responsive">
                    <table>
                        <thead>
                        <tr>
                            <th>Asesi</th>
                            <th>Program</th>
                            <th>Asesmen Selesai</th>
                            <th>Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($sertifikatPerluUpload as $pengajuan)
                            <tr>
                                <td>{{ $pengajuan->user->nama ?? '-' }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($pengajuan->program->nama ?? '-', 35) }}</td>
                                <td>{{ $pengajuan->jadwalAsesmen?->tanggal_mulai?->format('d/m/Y H:i') ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('admin.sertifikat.create', $pengajuan->id) }}" class="btn btn-sm btn-warning">
                                        Upload
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Tidak ada sertifikat yang menunggu upload.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="admin-table mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">Pengajuan Terbaru</h5>
                    <a href="{{ route('admin.pengajuan.index') }}" class="btn btn-sm btn-outline-primary">Semua</a>
                </div>

                <div class="table-responsive">
                    <table>
                        <thead>
                        <tr>
                            <th>Asesi</th>
                            <th>Program</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($pengajuanTerbaru as $pengajuan)
                            <tr>
                                <td>{{ $pengajuan->user->nama ?? $pengajuan->user->name ?? '-' }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($pengajuan->program->nama ?? '-', 20) }}</td>
                                <td>
                                    <span class="badge bg-{{ $pengajuan->status_badge_color }} {{ $pengajuan->status_badge_color === 'warning' ? 'text-dark' : '' }}">
                                        {{ $pengajuan->status_label }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">Belum ada pengajuan skema.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="admin-table">
                <h5>Aksi Cepat</h5>
                <div class="d-grid gap-2">
                    <a class="btn btn-primary" href="{{ route('admin.program-pelatihan.create') }}">
                        <i class="bi bi-plus-circle"></i> Tambah Program Baru
                    </a>
                    <a class="btn btn-outline-primary" href="{{ route('admin.jadwal-asesmen.index') }}">
                        <i class="bi bi-calendar2-week"></i> Kelola Jadwal
                    </a>
                    <a class="btn btn-outline-primary" href="{{ route('admin.laporan.index') }}">
                        <i class="bi bi-bar-chart"></i> Laporan Admin
                    </a>
                    <a class="btn btn-outline-secondary" href="{{ url('skema-sertifikasi') }}" target="_blank">
                        <i class="bi bi-eye"></i> Halaman Skema Publik
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
