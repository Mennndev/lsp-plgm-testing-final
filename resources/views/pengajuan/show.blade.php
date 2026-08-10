<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pengajuan - {{ $pengajuan->program->nama }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --lsp-blue: #1f6feb;
            --lsp-navy: #203f7d;
            --page-bg: #f4f7fb;
            --border: #e5eaf1;
            --muted: #667085;
        }

        body {
            background: var(--page-bg);
            color: #172033;
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .page-shell {
            max-width: 1180px;
            margin: 0 auto;
        }

        .page-heading {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 28px;
            box-shadow: 0 8px 28px rgba(31, 53, 86, .06);
        }

        .page-heading h1 {
            font-size: clamp(1.55rem, 2.5vw, 2rem);
            font-weight: 750;
            margin-bottom: 6px;
        }

        .status-badge {
            font-size: .88rem;
            padding: 9px 14px;
            border-radius: 999px;
        }

        .section-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 8px 28px rgba(31, 53, 86, .05);
            margin-bottom: 22px;
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 17px 22px;
            background: var(--lsp-blue);
            color: #fff;
        }

        .section-header h2 {
            font-size: 1.05rem;
            font-weight: 700;
            margin: 0;
        }

        .section-body {
            padding: 22px;
        }

        .subsection-title {
            font-size: .98rem;
            font-weight: 750;
            color: var(--lsp-navy);
            margin-bottom: 14px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px 28px;
        }

        .info-item {
            padding: 10px 0;
            border-bottom: 1px solid #eef1f5;
        }

        .info-label {
            display: block;
            color: var(--muted);
            font-size: .78rem;
            margin-bottom: 3px;
        }

        .info-value {
            font-weight: 600;
            word-break: break-word;
        }

        .unit-card {
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow: hidden;
            margin-bottom: 14px;
        }

        .unit-head {
            padding: 15px 17px;
            background: #f8faff;
            border-bottom: 1px solid var(--border);
        }

        .unit-code {
            color: var(--muted);
            font-size: .8rem;
            margin-top: 4px;
        }

        .unit-content {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(260px, .9fr);
            gap: 18px;
            padding: 16px 17px;
            align-items: start;
        }

        .assessment-box,
        .evidence-box {
            min-height: 86px;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 14px;
        }

        .box-label {
            font-size: .77rem;
            color: var(--muted);
            margin-bottom: 8px;
        }

        .file-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 10px 12px;
            margin-bottom: 8px;
            background: #fff;
        }

        .file-row:last-child {
            margin-bottom: 0;
        }

        .file-name {
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: .88rem;
        }

        .document-table th {
            color: #475467;
            font-size: .82rem;
            background: #f8fafc;
            white-space: nowrap;
        }

        .document-table td {
            vertical-align: middle;
        }

        .signature-frame {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: min(100%, 420px);
            min-height: 145px;
            padding: 14px;
            border: 1px dashed #b9c4d4;
            border-radius: 12px;
            background: #fff;
        }

        .signature-frame img {
            max-width: 100%;
            max-height: 130px;
            object-fit: contain;
        }

        .empty-state {
            padding: 18px;
            border: 1px dashed #cfd7e3;
            border-radius: 12px;
            color: var(--muted);
            text-align: center;
            background: #fafbfc;
        }

        .meta-card {
            height: 100%;
            padding: 17px;
            border: 1px solid var(--border);
            border-radius: 13px;
            background: #fff;
        }

        .meta-card .meta-title {
            color: var(--muted);
            font-size: .77rem;
            margin-bottom: 4px;
        }

        .meta-card .meta-value {
            font-size: .95rem;
            font-weight: 650;
        }

        @media (max-width: 767.98px) {
            .info-grid,
            .unit-content {
                grid-template-columns: 1fr;
            }

            .page-heading,
            .section-body {
                padding: 18px;
            }
        }
    </style>
</head>
<body>
@php
    $apl01 = $pengajuan->apl01;
    $unitEvidence = $pengajuan->portfolio
        ->filter(fn ($item) => $item->deskripsi === 'Bukti Kompetensi APL-02')
        ->groupBy('unit_kompetensi_id');

    $otherUnitPortfolio = $pengajuan->portfolio
        ->reject(fn ($item) => $item->deskripsi === 'Bukti Kompetensi APL-02');

    $persyaratanDasar = $pengajuan->pengajuanPersyaratanDasar;
    $buktiAdministratif = $pengajuan->pengajuanBuktiAdministratif;
    $buktiPortofolio = $pengajuan->pengajuanBuktiPortofolio;

    $jenisKelamin = match($apl01?->jenis_kelamin) {
        'L', 'Laki-laki' => 'Laki-laki',
        'P', 'Perempuan' => 'Perempuan',
        default => $apl01?->jenis_kelamin ?: '-',
    };

    $formatBytes = function ($bytes) {
        $bytes = (int) $bytes;
        if ($bytes <= 0) return '-';
        if ($bytes >= 1048576) return number_format($bytes / 1048576, 2, ',', '.') . ' MB';
        if ($bytes >= 1024) return number_format($bytes / 1024, 1, ',', '.') . ' KB';
        return $bytes . ' B';
    };
@endphp

<div class="container py-4 py-md-5">
    <div class="page-shell">
        <div class="page-heading mb-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3">
                <div>
                    <div class="text-primary fw-semibold small mb-2">
                        <i class="bi bi-file-earmark-check me-1"></i> Pengajuan Sertifikasi
                    </div>
                    <h1>Detail Pengajuan Skema</h1>
                    <p class="text-muted mb-0">{{ $pengajuan->program->nama }}</p>
                </div>
                <span class="badge bg-{{ $pengajuan->status_badge_color }} status-badge">
                    {{ $pengajuan->status_label }}
                </span>
            </div>

            <div class="row g-3 mt-3">
                <div class="col-md-3 col-6">
                    <div class="meta-card">
                        <div class="meta-title">Tanggal Pengajuan</div>
                        <div class="meta-value">{{ $pengajuan->tanggal_pengajuan?->format('d/m/Y H:i') ?? '-' }}</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="meta-card">
                        <div class="meta-title">Status</div>
                        <div class="meta-value">{{ $pengajuan->status_label }}</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="meta-card">
                        <div class="meta-title">Tanggal Diproses</div>
                        <div class="meta-value">{{ $pengajuan->tanggal_disetujui?->format('d/m/Y H:i') ?? '-' }}</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="meta-card">
                        <div class="meta-title">Diproses Oleh</div>
                        <div class="meta-value">{{ $pengajuan->approver?->nama ?? '-' }}</div>
                    </div>
                </div>
            </div>

            @if($pengajuan->catatan_admin)
                <div class="alert alert-{{ $pengajuan->status === 'rejected' ? 'danger' : 'info' }} mt-3 mb-0">
                    <strong>Catatan Admin:</strong> {{ $pengajuan->catatan_admin }}
                </div>
            @endif
        </div>

        @if($pengajuan->pembayaran)
            <div class="section-card">
                <div class="section-header">
                    <i class="bi bi-credit-card-2-front"></i>
                    <h2>Informasi Pembayaran</h2>
                </div>
                <div class="section-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Nominal Pembayaran</span>
                            <span class="info-value">{{ $pengajuan->pembayaran->formatted_nominal }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Status Pembayaran</span>
                            <span class="badge bg-{{ $pengajuan->pembayaran->status_badge_color }}">
                                {{ $pengajuan->pembayaran->status_label }}
                            </span>
                        </div>
                        @if($pengajuan->pembayaran->order_id)
                            <div class="info-item">
                                <span class="info-label">Order ID</span>
                                <span class="info-value">{{ $pengajuan->pembayaran->order_id }}</span>
                            </div>
                        @endif
                        @if($pengajuan->pembayaran->payment_type)
                            <div class="info-item">
                                <span class="info-label">Metode Pembayaran</span>
                                <span class="info-value">{{ strtoupper(str_replace('_', ' ', $pengajuan->pembayaran->payment_type)) }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('pembayaran.show', $pengajuan->id) }}" class="btn btn-outline-primary">
                            <i class="bi bi-receipt me-1"></i> Lihat Detail Pembayaran
                        </a>
                    </div>
                </div>
            </div>
        @endif

        @if($apl01)
            <div class="section-card">
                <div class="section-header">
                    <i class="bi bi-person-vcard"></i>
                    <h2>APL-01: Data Pemohon Sertifikasi</h2>
                </div>
                <div class="section-body">
                    <div class="subsection-title">A. Data Pribadi</div>
                    <div class="info-grid">
                        <div class="info-item"><span class="info-label">Nama Lengkap</span><span class="info-value">{{ $apl01->nama_lengkap ?: '-' }}</span></div>
                        <div class="info-item"><span class="info-label">NIK</span><span class="info-value">{{ $apl01->nik ?: '-' }}</span></div>
                        <div class="info-item"><span class="info-label">Tempat Lahir</span><span class="info-value">{{ $apl01->tempat_lahir ?: '-' }}</span></div>
                        <div class="info-item"><span class="info-label">Tanggal Lahir</span><span class="info-value">{{ $apl01->tanggal_lahir?->format('d/m/Y') ?? '-' }}</span></div>
                        <div class="info-item"><span class="info-label">Jenis Kelamin</span><span class="info-value">{{ $jenisKelamin }}</span></div>
                        <div class="info-item"><span class="info-label">Kebangsaan</span><span class="info-value">{{ $apl01->kebangsaan ?: '-' }}</span></div>
                        <div class="info-item"><span class="info-label">No. HP</span><span class="info-value">{{ $apl01->hp ?: '-' }}</span></div>
                        <div class="info-item"><span class="info-label">Telepon Rumah</span><span class="info-value">{{ $apl01->telepon_rumah ?: '-' }}</span></div>
                        <div class="info-item"><span class="info-label">Email</span><span class="info-value">{{ $apl01->email ?: '-' }}</span></div>
                        <div class="info-item"><span class="info-label">Kode Pos</span><span class="info-value">{{ $apl01->kode_pos ?: '-' }}</span></div>
                        <div class="info-item" style="grid-column: 1 / -1;"><span class="info-label">Alamat Rumah</span><span class="info-value">{{ $apl01->alamat_rumah ?: '-' }}</span></div>
                    </div>

                    <hr class="my-4">
                    <div class="subsection-title">B. Pendidikan & Pekerjaan</div>
                    <div class="info-grid">
                        <div class="info-item"><span class="info-label">Pendidikan Terakhir</span><span class="info-value">{{ $apl01->kualifikasi_pendidikan ?: '-' }}</span></div>
                        <div class="info-item"><span class="info-label">Pekerjaan</span><span class="info-value">{{ $apl01->pekerjaan ?: '-' }}</span></div>
                        <div class="info-item"><span class="info-label">Institusi / Perusahaan</span><span class="info-value">{{ $apl01->nama_institusi ?: '-' }}</span></div>
                        <div class="info-item"><span class="info-label">Jabatan</span><span class="info-value">{{ $apl01->jabatan ?: '-' }}</span></div>
                        <div class="info-item"><span class="info-label">Telepon Institusi / Perusahaan</span><span class="info-value">{{ $apl01->telepon_kantor ?: '-' }}</span></div>
                        <div class="info-item"><span class="info-label">Email Institusi / Perusahaan</span><span class="info-value">{{ $apl01->email_kantor ?: '-' }}</span></div>
                        <div class="info-item" style="grid-column: 1 / -1;"><span class="info-label">Alamat Institusi / Perusahaan</span><span class="info-value">{{ $apl01->alamat_kantor ?: '-' }}</span></div>
                    </div>

                    <hr class="my-4">
                    <div class="subsection-title">C. Tujuan Asesmen</div>
                    @if(!empty($apl01->tujuan_asesmen))
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($apl01->tujuan_asesmen as $tujuan)
                                <span class="badge text-bg-light border px-3 py-2">{{ $tujuan }}</span>
                            @endforeach
                        </div>
                    @else
                        <div class="text-muted">-</div>
                    @endif

                    @if($apl01->catatan)
                        <hr class="my-4">
                        <div class="subsection-title">D. Catatan Asesi</div>
                        <div class="p-3 bg-light rounded-3">{{ $apl01->catatan }}</div>
                    @endif

                    <hr class="my-4">
                    <div class="subsection-title">Tanda Tangan Digital Asesi</div>
                    @if($apl01->ttd)
                        <div class="signature-frame">
                            @if(str_starts_with($apl01->ttd, 'data:image/'))
                                <img src="{{ $apl01->ttd }}" alt="Tanda tangan digital Asesi">
                            @else
                                <img src="{{ asset('storage/'.$apl01->ttd) }}" alt="Tanda tangan digital Asesi">
                            @endif
                        </div>
                        <div class="small text-muted mt-2">Tanda tangan ini diberikan Asesi pada saat mengirim pengajuan sertifikasi.</div>
                    @else
                        <div class="empty-state">Tanda tangan digital tidak tersedia pada pengajuan ini.</div>
                    @endif
                </div>
            </div>
        @endif

        <div class="section-card">
            <div class="section-header">
                <i class="bi bi-clipboard-check"></i>
                <h2>APL-02: Asesmen Mandiri & Bukti Kompetensi</h2>
            </div>
            <div class="section-body">
                <div class="alert alert-info py-2">
                    <i class="bi bi-info-circle me-1"></i>
                    K/BK di bawah adalah <strong>penilaian mandiri Asesi</strong>, bukan hasil akhir penilaian Asesor.
                </div>

                @forelse($pengajuan->apl02 as $index => $apl02)
                    @php
                        $unit = $apl02->unitKompetensi;
                        $status = is_array($apl02->self_assessment)
                            ? ($apl02->self_assessment['status'] ?? null)
                            : null;
                        $evidences = $unitEvidence->get($apl02->unit_kompetensi_id, collect());
                    @endphp

                    <div class="unit-card">
                        <div class="unit-head">
                            <div class="fw-bold">{{ $index + 1 }}. {{ $unit?->judul_unit ?? 'Unit Kompetensi' }}</div>
                            <div class="unit-code">Kode Unit: {{ $unit?->kode_unit ?? '-' }}</div>
                        </div>
                        <div class="unit-content">
                            <div class="assessment-box">
                                <div class="box-label">Penilaian Mandiri</div>
                                @if($status === 'K')
                                    <span class="badge bg-success px-3 py-2"><i class="bi bi-check-circle me-1"></i> K - Kompeten</span>
                                @elseif($status === 'BK')
                                    <span class="badge bg-danger px-3 py-2"><i class="bi bi-x-circle me-1"></i> BK - Belum Kompeten</span>
                                @else
                                    <span class="badge bg-secondary">Belum Dinilai</span>
                                @endif
                            </div>

                            <div class="evidence-box">
                                <div class="box-label">Bukti Kompetensi</div>
                                @forelse($evidences as $evidence)
                                    <div class="file-row">
                                        <div class="file-name" title="{{ $evidence->nama_file }}">
                                            <i class="bi bi-file-earmark-text text-primary me-1"></i>
                                            {{ $evidence->nama_file }}
                                        </div>
                                        <a href="{{ asset('storage/'.$evidence->path) }}" target="_blank" class="btn btn-sm btn-outline-primary flex-shrink-0">
                                            <i class="bi bi-eye"></i> Lihat
                                        </a>
                                    </div>
                                @empty
                                    <span class="text-muted small">Tidak ada bukti yang dilampirkan untuk unit ini.</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">Belum ada data asesmen mandiri APL-02.</div>
                @endforelse
            </div>
        </div>

        <div class="section-card">
            <div class="section-header">
                <i class="bi bi-folder2-open"></i>
                <h2>Dokumen Pengajuan</h2>
            </div>
            <div class="section-body">
                <div class="subsection-title">Persyaratan Dasar</div>
                @if($persyaratanDasar->isNotEmpty())
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered document-table align-middle">
                            <thead><tr><th>No</th><th>Dokumen</th><th>Nama File</th><th>Ukuran</th><th>Aksi</th></tr></thead>
                            <tbody>
                            @foreach($persyaratanDasar as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->persyaratanDasar?->nama_dokumen ?? 'Persyaratan Dasar' }}</td>
                                    <td>{{ $item->nama_file }}</td>
                                    <td>{{ $formatBytes($item->ukuran) }}</td>
                                    <td><a href="{{ asset('storage/'.$item->path) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> Lihat</a></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state mb-4">Tidak ada file Persyaratan Dasar yang dilampirkan.</div>
                @endif

                <div class="subsection-title">Bukti Administratif</div>
                @if($buktiAdministratif->isNotEmpty())
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered document-table align-middle">
                            <thead><tr><th>No</th><th>Dokumen</th><th>Nama File</th><th>Ukuran</th><th>Aksi</th></tr></thead>
                            <tbody>
                            @foreach($buktiAdministratif as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->buktiAdministratif?->nama_dokumen ?? 'Bukti Administratif' }}</td>
                                    <td>{{ $item->nama_file }}</td>
                                    <td>{{ $formatBytes($item->ukuran) }}</td>
                                    <td><a href="{{ asset('storage/'.$item->path) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> Lihat</a></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state mb-4">Tidak ada file Bukti Administratif yang dilampirkan.</div>
                @endif

                <div class="subsection-title">Bukti Portofolio</div>
                @if($buktiPortofolio->isNotEmpty())
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered document-table align-middle">
                            <thead><tr><th>No</th><th>Dokumen</th><th>Nama File</th><th>Ukuran</th><th>Aksi</th></tr></thead>
                            <tbody>
                            @foreach($buktiPortofolio as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->buktiPortofolioTemplate?->nama_dokumen ?? 'Bukti Portofolio' }}</td>
                                    <td>{{ $item->nama_file }}</td>
                                    <td>{{ $formatBytes($item->ukuran) }}</td>
                                    <td><a href="{{ asset('storage/'.$item->path) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> Lihat</a></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state mb-4">Tidak ada file Bukti Portofolio yang dilampirkan.</div>
                @endif

                @if($otherUnitPortfolio->isNotEmpty())
                    <div class="subsection-title">Portofolio Tambahan per Unit Kompetensi</div>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered document-table align-middle">
                            <thead><tr><th>No</th><th>Unit Kompetensi</th><th>Nama File</th><th>Deskripsi</th><th>Aksi</th></tr></thead>
                            <tbody>
                            @foreach($otherUnitPortfolio as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->unitKompetensi?->judul_unit ?? '-' }}</td>
                                    <td>{{ $item->nama_file }}</td>
                                    <td>{{ $item->deskripsi ?: '-' }}</td>
                                    <td><a href="{{ asset('storage/'.$item->path) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> Lihat</a></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                @if($pengajuan->dokumen->isNotEmpty())
                    <div class="subsection-title">Dokumen Pendukung Lainnya</div>
                    <div class="table-responsive">
                        <table class="table table-bordered document-table align-middle mb-0">
                            <thead><tr><th>No</th><th>Jenis</th><th>Nama File</th><th>Ukuran</th><th>Aksi</th></tr></thead>
                            <tbody>
                            @foreach($pengajuan->dokumen as $index => $dok)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><span class="badge text-bg-light border">{{ strtoupper($dok->jenis_dokumen) }}</span></td>
                                    <td>{{ $dok->nama_file }}</td>
                                    <td>{{ isset($dok->ukuran) ? $formatBytes($dok->ukuran) : ($dok->formatted_size ?? '-') }}</td>
                                    <td><a href="{{ asset('storage/'.$dok->path) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> Lihat</a></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        @if($pengajuan->jadwalAsesmen)
            <div class="section-card">
                <div class="section-header">
                    <i class="bi bi-calendar2-check"></i>
                    <h2>Jadwal Asesmen</h2>
                </div>
                <div class="section-body">
                    <div class="info-grid">
                        <div class="info-item"><span class="info-label">Tanggal Mulai</span><span class="info-value">{{ $pengajuan->jadwalAsesmen->tanggal_mulai?->format('d/m/Y H:i') ?? '-' }}</span></div>
                        <div class="info-item"><span class="info-label">Tanggal Selesai</span><span class="info-value">{{ $pengajuan->jadwalAsesmen->tanggal_selesai?->format('d/m/Y H:i') ?? '-' }}</span></div>
                        <div class="info-item"><span class="info-label">Mode</span><span class="info-value">{{ ucfirst($pengajuan->jadwalAsesmen->mode_asesmen ?? '-') }}</span></div>
                        <div class="info-item"><span class="info-label">Asesor</span><span class="info-value">{{ $pengajuan->jadwalAsesmen->asesor?->nama ?? '-' }}</span></div>
                        <div class="info-item"><span class="info-label">Lokasi</span><span class="info-value">{{ $pengajuan->jadwalAsesmen->lokasi ?: '-' }}</span></div>
                        <div class="info-item"><span class="info-label">Status Jadwal</span><span class="info-value">{{ $pengajuan->jadwalAsesmen->status_label ?? ucfirst($pengajuan->jadwalAsesmen->status ?? '-') }}</span></div>
                    </div>
                </div>
            </div>
        @endif

        <div class="d-flex flex-wrap justify-content-between gap-2 pb-4">
            <a href="{{ route('dashboard.user') }}" class="btn btn-secondary px-4">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
            </a>

            @if($pengajuan->status === 'draft')
                <div class="d-flex gap-2">
                    <a href="{{ route('pengajuan.create', $pengajuan->program_pelatihan_id) }}" class="btn btn-warning">
                        <i class="bi bi-pencil me-1"></i> Edit Draft
                    </a>
                    <form action="{{ route('pengajuan.destroy', $pengajuan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus draft ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger"><i class="bi bi-trash me-1"></i> Hapus</button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>
