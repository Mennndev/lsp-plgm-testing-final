@extends('layouts.admin')

@section('title', 'Detail Pengajuan')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Pengajuan Skema</h1>
        <a href="{{ route('admin.pengajuan.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
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

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold">Status Pengajuan</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Nama User:</strong> {{ $pengajuan->user->nama }}</p>
                    <p><strong>Email:</strong> {{ $pengajuan->user->email }}</p>
                    <p><strong>Program:</strong> {{ $pengajuan->program->nama }}</p>
                    <p><strong>Tanggal Pengajuan:</strong> {{ $pengajuan->tanggal_pengajuan->format('d/m/Y H:i') }}</p>
                </div>
                <div class="col-md-6">
                    <p>
                        <strong>Status:</strong>
                        <span class="badge bg-{{ $pengajuan->status_badge_color }} fs-6">
                            {{ $pengajuan->status_label }}
                        </span>
                    </p>
                    @if($pengajuan->tanggal_disetujui)
                        <p><strong>Tanggal Diproses:</strong> {{ $pengajuan->tanggal_disetujui->format('d/m/Y H:i') }}</p>
                    @endif
                    @if($pengajuan->approver)
                        <p><strong>Diproses oleh:</strong> {{ $pengajuan->approver->nama }}</p>
                    @endif
                </div>
            </div>

            @if($pengajuan->catatan_admin)
                <hr>
                <div class="alert alert-{{ $pengajuan->status === 'rejected' ? 'danger' : 'info' }} mb-0">
                    <strong>Catatan Admin:</strong><br>
                    {{ $pengajuan->catatan_admin }}
                </div>
            @endif

            @if($pengajuan->status === 'pending')
                <hr>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                    <i class="bi bi-check-circle"></i> Setujui Pengajuan
                </button>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                    <i class="bi bi-x-circle"></i> Tolak Pengajuan
                </button>
            @endif
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Asesor & Jadwal Asesmen</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.pengajuan.assign-asesor', $pengajuan->id) }}" class="mb-3">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Pilih Asesor</label>
                    <select name="asesor_id" class="form-select" required>
                        <option value="">-- Pilih Asesor --</option>
                        @foreach($listAsesor as $asesor)
                            <option value="{{ $asesor->id }}" {{ ($assignedAsesorId ?? null) == $asesor->id ? 'selected' : '' }}>
                                {{ $asesor->nama ?? $asesor->name }} ({{ $asesor->email }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <button class="btn btn-primary">
                    <i class="bi bi-person-check"></i> Assign Asesor
                </button>
            </form>

            <a href="{{ route('admin.jadwal-asesmen.form', $pengajuan->id) }}" class="btn btn-outline-primary">
                <i class="bi bi-calendar2-event"></i> Kelola Jadwal Asesmen
            </a>

            @if($pengajuan->jadwalAsesmen)
                <div class="alert alert-info mt-3 mb-0">
                    Jadwal saat ini:
                    <strong>{{ $pengajuan->jadwalAsesmen->tanggal_mulai->format('d/m/Y H:i') }}</strong>
                    ({{ strtoupper($pengajuan->jadwalAsesmen->mode_asesmen) }})
                    @if($pengajuan->jadwalAsesmen->asesor)
                        - Asesor: {{ $pengajuan->jadwalAsesmen->asesor->nama ?? $pengajuan->jadwalAsesmen->asesor->name }}
                    @endif
                    <br>
                    Status asesmen:
                    <span class="badge bg-{{ $pengajuan->jadwalAsesmen->status_badge ?? 'secondary' }}">
                        {{ $pengajuan->jadwalAsesmen->status_label ?? ucfirst($pengajuan->jadwalAsesmen->status) }}
                    </span>
                </div>

                @if($pengajuan->jadwalAsesmen->status === 'completed')
                    @if($pengajuan->sertifikat)
                        <div class="alert alert-success mt-3 mb-0">
                            <i class="bi bi-award"></i>
                            Sertifikat sudah diterbitkan dengan nomor
                            <strong>{{ $pengajuan->sertifikat->nomor_sertifikat }}</strong>.
                            @if($pengajuan->sertifikat->file_sertifikat)
                                <br>
                                <a href="{{ asset('storage/'.$pengajuan->sertifikat->file_sertifikat) }}" target="_blank" class="btn btn-sm btn-success mt-2">
                                    <i class="bi bi-file-earmark-pdf"></i> Lihat Sertifikat
                                </a>
                            @endif
                            <a href="{{ route('admin.sertifikat.create', $pengajuan->id) }}" class="btn btn-sm btn-outline-primary mt-2">
                                <i class="bi bi-pencil"></i> Edit Sertifikat
                            </a>
                        </div>
                    @else
                        <a href="{{ route('admin.sertifikat.create', $pengajuan->id) }}" class="btn btn-warning mt-3">
                            <i class="bi bi-award"></i> Terbitkan Sertifikat
                        </a>
                    @endif
                @else
                    <div class="alert alert-warning mt-3 mb-0">
                        <i class="bi bi-info-circle"></i>
                        Sertifikat belum bisa diterbitkan karena asesmen belum selesai.
                    </div>
                @endif
            @else
                <div class="alert alert-warning mt-3 mb-0">
                    <i class="bi bi-info-circle"></i>
                    Jadwal asesmen belum dibuat. Sertifikat belum bisa diterbitkan.
                </div>
            @endif
        </div>
    </div>

    @if($pengajuan->apl01)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">APL-01: Data Pribadi</h6>
            </div>
            <div class="card-body">
                <h6 class="font-weight-bold mb-3">A. Data Pribadi</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <tr><th width="25%">Nama Lengkap</th><td>{{ $pengajuan->apl01->nama_lengkap }}</td></tr>
                        <tr><th>NIK</th><td>{{ $pengajuan->apl01->nik }}</td></tr>
                        <tr><th>Tempat/Tanggal Lahir</th><td>{{ $pengajuan->apl01->tempat_lahir }}, {{ $pengajuan->apl01->tanggal_lahir?->format('d/m/Y') }}</td></tr>
                        <tr><th>Jenis Kelamin</th><td>{{ $pengajuan->apl01->jenis_kelamin }}</td></tr>
                        <tr><th>Kebangsaan</th><td>{{ $pengajuan->apl01->kebangsaan }}</td></tr>
                        <tr><th>Alamat</th><td>{{ $pengajuan->apl01->alamat_rumah }}</td></tr>
                        <tr><th>Email</th><td>{{ $pengajuan->apl01->email }}</td></tr>
                        <tr><th>No. HP</th><td>{{ $pengajuan->apl01->hp }}</td></tr>
                    </table>
                </div>

                @if($pengajuan->apl01->kualifikasi_pendidikan || $pengajuan->apl01->pekerjaan)
                    <h6 class="font-weight-bold mb-3 mt-4">B. Pendidikan & Pekerjaan</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <tr><th width="25%">Pendidikan</th><td>{{ $pengajuan->apl01->kualifikasi_pendidikan ?: '-' }}</td></tr>
                            <tr><th>Pekerjaan</th><td>{{ $pengajuan->apl01->pekerjaan ?: '-' }}</td></tr>
                            @if($pengajuan->apl01->nama_institusi)
                                <tr><th>Institusi</th><td>{{ $pengajuan->apl01->nama_institusi }}</td></tr>
                            @endif
                            @if($pengajuan->apl01->jabatan)
                                <tr><th>Jabatan</th><td>{{ $pengajuan->apl01->jabatan }}</td></tr>
                            @endif
                            @if($pengajuan->apl01->alamat_kantor)
                                <tr><th>Alamat Kantor</th><td>{{ $pengajuan->apl01->alamat_kantor }}</td></tr>
                            @endif
                        </table>
                    </div>
                @endif

                @if($pengajuan->apl01->tujuan_asesmen)
                    <h6 class="font-weight-bold mb-2 mt-4">Tujuan Asesmen</h6>
                    <ul class="mb-0">
                        @foreach($pengajuan->apl01->tujuan_asesmen as $tujuan)
                            <li>{{ $tujuan }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">APL-02: Penilaian Mandiri & Bukti Kompetensi</h6>
        </div>
        <div class="card-body">
            @if($apl02Unit->isNotEmpty())
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    Nilai K/BK di bawah merupakan <strong>penilaian mandiri Asesi</strong>, bukan hasil akhir penilaian Asesor.
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="6%" class="text-center">No</th>
                                <th width="20%">Kode Unit</th>
                                <th>Unit Kompetensi</th>
                                <th width="18%" class="text-center">Penilaian Mandiri</th>
                                <th width="25%">Bukti Kompetensi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($apl02Unit as $index => $row)
                                @php
                                    $unit = $row->unitKompetensi;
                                    $status = $row->self_assessment['status'] ?? null;
                                    $evidence = $buktiUnit->get($row->unit_kompetensi_id, collect());
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $unit?->kode_unit ?? '-' }}</td>
                                    <td>{{ $unit?->judul_unit ?? '-' }}</td>
                                    <td class="text-center">
                                        @if($status === 'K')
                                            <span class="badge bg-success px-3 py-2">
                                                <i class="bi bi-check-circle"></i> K - Kompeten
                                            </span>
                                        @elseif($status === 'BK')
                                            <span class="badge bg-danger px-3 py-2">
                                                <i class="bi bi-x-circle"></i> BK - Belum Kompeten
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($evidence->isNotEmpty())
                                            <div class="d-flex flex-column gap-1">
                                                @foreach($evidence as $bukti)
                                                    <a href="{{ asset('storage/'.$bukti->path) }}" target="_blank" class="btn btn-sm btn-outline-primary text-start">
                                                        <i class="bi bi-file-earmark"></i>
                                                        {{ \Illuminate\Support\Str::limit($bukti->nama_file, 40) }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted fst-italic">Tidak ada bukti</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @elseif($selfAssessments->isNotEmpty())
                <div class="alert alert-secondary">
                    Pengajuan ini menggunakan format APL-02 lama berbasis KUK.
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Kode Unit</th>
                                <th>Unit Kompetensi</th>
                                <th>Kriteria Unjuk Kerja</th>
                                <th width="18%" class="text-center">Self Assessment</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($selfAssessments as $row)
                                <tr>
                                    <td>{{ $row->kuk?->elemen?->unit?->kode_unit ?? '-' }}</td>
                                    <td>{{ $row->kuk?->elemen?->unit?->judul_unit ?? '-' }}</td>
                                    <td>{{ $row->kuk?->deskripsi ?? '-' }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $row->nilai === 'K' ? 'success' : 'danger' }}">
                                            {{ $row->nilai === 'K' ? 'K - Kompeten' : 'BK - Belum Kompeten' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-warning mb-0">
                    Belum ada data penilaian mandiri APL-02 untuk pengajuan ini.
                </div>
            @endif
        </div>
    </div>

    @if($pengajuan->pengajuanPersyaratanDasar->count())
        <div class="card mb-4 shadow">
            <div class="card-header bg-secondary text-white">
                <i class="bi bi-journal-check"></i> Persyaratan Dasar
            </div>
            <div class="card-body">
                <ul class="list-group">
                    @foreach($pengajuan->pengajuanPersyaratanDasar as $bukti)
                        <li class="list-group-item">
                            <a href="{{ asset('storage/'.$bukti->path) }}" target="_blank">
                                <i class="bi bi-paperclip"></i> {{ $bukti->nama_file }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    @if($pengajuan->pengajuanBuktiAdministratif->count())
        <div class="card mb-4 shadow">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-folder-check"></i> Bukti Administratif
            </div>
            <div class="card-body">
                <ul class="list-group">
                    @foreach($pengajuan->pengajuanBuktiAdministratif as $bukti)
                        <li class="list-group-item">
                            <a href="{{ asset('storage/'.$bukti->path) }}" target="_blank">
                                <i class="bi bi-paperclip"></i> {{ $bukti->nama_file }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    @if($pengajuan->pengajuanBuktiPortofolio->count())
        <div class="card mb-4 shadow">
            <div class="card-header bg-info text-white">
                <i class="bi bi-briefcase"></i> Bukti Portofolio
            </div>
            <div class="card-body">
                <ul class="list-group">
                    @foreach($pengajuan->pengajuanBuktiPortofolio as $bukti)
                        <li class="list-group-item">
                            <a href="{{ asset('storage/'.$bukti->path) }}" target="_blank">
                                <i class="bi bi-paperclip"></i> {{ $bukti->nama_file }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
</div>

<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.pengajuan.approve', $pengajuan->id) }}">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="approveModalLabel">Setujui Pengajuan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menyetujui pengajuan ini?</p>
                    <div class="mb-3">
                        <label class="form-label">Catatan (Opsional)</label>
                        <textarea class="form-control" name="catatan_admin" rows="3" placeholder="Tambahkan catatan jika diperlukan"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Ya, Setujui</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.pengajuan.reject', $pengajuan->id) }}">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="rejectModalLabel">Tolak Pengajuan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menolak pengajuan ini?</p>
                    <div class="mb-3">
                        <label class="form-label">Catatan <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="catatan_admin" rows="3" required placeholder="Berikan alasan penolakan"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Ya, Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
