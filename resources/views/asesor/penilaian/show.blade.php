@extends('layouts.asesor')

@section('title', 'Penilaian Asesi')

@section('content')
<div class="asesor-breadcrumb">
    <a href="{{ route('asesor.dashboard') }}"><i class="bi bi-house-door"></i> Dashboard</a>
    <span class="separator">›</span>
    <a href="{{ route('asesor.pengajuan.show', $pengajuan->id) }}">Detail Pengajuan</a>
    <span class="separator">›</span>
    <span>Penilaian</span>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header-brand">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-1"><i class="bi bi-clipboard-check"></i> Penilaian Skema: {{ $pengajuan->program->nama }}</h5>
                <p class="mb-0" style="opacity: 0.9; font-size: 13px;">
                    <i class="bi bi-person"></i> Asesi: {{ $pengajuan->user->nama }}
                </p>
            </div>
        </div>
    </div>
</div>

@php
    if ($useUnitAssessment) {
        $totalItem = $pengajuan->program->units->count();
        $dinilaiItem = $penilaianUnitTersimpan->count();
        $labelItem = 'Unit';
    } else {
        $totalItem = 0;
        foreach ($pengajuan->program->units as $unit) {
            foreach ($unit->elemenKompetensis as $elemen) {
                $totalItem += $elemen->kriteriaUnjukKerja->count();
            }
        }
        $dinilaiItem = $penilaianTersimpan->count();
        $labelItem = 'KUK';
    }
    $persentase = $totalItem > 0 ? round(($dinilaiItem / $totalItem) * 100) : 0;
@endphp

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span style="font-weight: 600; color: #233C7E;">Progress Penilaian</span>
            <span style="font-weight: 600; color: #233C7E;">{{ $dinilaiItem }} / {{ $totalItem }} {{ $labelItem }}</span>
        </div>
        <div class="progress-overall">
            <div class="progress-bar" role="progressbar" style="width: {{ $persentase }}%" aria-valuenow="{{ $persentase }}" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('asesor.pengajuan.store', $pengajuan->id) }}" id="penilaianForm">
    @csrf

    @if($useUnitAssessment)
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i>
            Skema ini dinilai per <strong>Unit Kompetensi</strong>. Nilai K/BK dari Asesi ditampilkan sebagai referensi penilaian mandiri, bukan keputusan akhir Asesor.
        </div>

        @forelse($pengajuan->program->units as $index => $unit)
            @php
                $saved = $penilaianUnitTersimpan->get($unit->id);
                $asesiRow = $apl02AsesiPerUnit->get($unit->id);
                $asesiStatus = $asesiRow?->self_assessment['status'] ?? null;
                $evidence = $buktiAsesiPerUnit->get($unit->id, collect());
            @endphp

            <div class="unit-card">
                <div class="unit-card-header">
                    <h5><i class="bi bi-bookmark-check"></i> {{ $index + 1 }}. {{ $unit->judul_unit }}</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <div class="small text-muted mb-1">Kode Unit</div>
                                <strong>{{ $unit->kode_unit }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <div class="small text-muted mb-1">Penilaian Mandiri Asesi</div>
                                @if($asesiStatus === 'K')
                                    <span class="badge bg-success">K - Kompeten</span>
                                @elseif($asesiStatus === 'BK')
                                    <span class="badge bg-danger">BK - Belum Kompeten</span>
                                @else
                                    <span class="text-muted">Belum ada</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Bukti Kompetensi Asesi</label>
                        @if($evidence->isNotEmpty())
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($evidence as $bukti)
                                    <a href="{{ asset('storage/'.$bukti->path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-file-earmark"></i> {{ \Illuminate\Support\Str::limit($bukti->nama_file, 40) }}
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <span class="text-muted fst-italic">Tidak ada bukti kompetensi yang diunggah.</span>
                        @endif
                    </div>

                    <div class="row g-3">
                        <div class="col-lg-4 col-md-5">
                            <label for="nilai_unit_{{ $unit->id }}" class="form-label fw-semibold">Hasil Penilaian Asesor</label>
                            <select id="nilai_unit_{{ $unit->id }}" name="nilai_unit[{{ $unit->id }}]" class="form-select" required>
                                <option value="">-- Pilih Penilaian --</option>
                                <option value="K" {{ old('nilai_unit.'.$unit->id, $saved?->nilai) === 'K' ? 'selected' : '' }}>✓ Kompeten</option>
                                <option value="BK" {{ old('nilai_unit.'.$unit->id, $saved?->nilai) === 'BK' ? 'selected' : '' }}>✗ Belum Kompeten</option>
                            </select>
                        </div>
                        <div class="col-lg-8 col-md-7">
                            <label class="form-label fw-semibold">Catatan Asesor</label>
                            <textarea name="catatan_unit[{{ $unit->id }}]" class="form-control" rows="2" placeholder="Catatan penilaian untuk unit ini (opsional)">{{ old('catatan_unit.'.$unit->id, $saved?->catatan) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-secondary mb-0">
                <i class="bi bi-info-circle"></i> Belum ada Unit Kompetensi pada skema ini.
            </div>
        @endforelse
    @else
        @forelse($pengajuan->program->units as $unit)
            <div class="unit-card">
                <div class="unit-card-header">
                    <h5><i class="bi bi-bookmark-check"></i> {{ $unit->judul_unit }}</h5>
                </div>
                <div class="card-body p-4">
                    @forelse($unit->elemenKompetensis as $elemen)
                        <div class="elemen-section">
                            <h6>{{ $elemen->nama_elemen }}</h6>
                            @foreach($elemen->kriteriaUnjukKerja as $kuk)
                                @php
                                    $nilaiTersimpan = $penilaianTersimpan[$kuk->id]->nilai ?? null;
                                    $catatanTersimpan = $penilaianTersimpan[$kuk->id]->catatan ?? null;
                                @endphp
                                <div class="kuk-item">
                                    <label for="nilai_{{ $kuk->id }}">
                                        <i class="bi bi-check2-square text-primary"></i> {{ $kuk->deskripsi }}
                                    </label>
                                    <div class="row g-3 mt-1">
                                        <div class="col-lg-4 col-md-5">
                                            <select id="nilai_{{ $kuk->id }}" name="nilai[{{ $kuk->id }}]" class="form-select" required>
                                                <option value="">-- Pilih Penilaian --</option>
                                                <option value="K" {{ old('nilai.'.$kuk->id, $nilaiTersimpan) === 'K' ? 'selected' : '' }}>✓ Kompeten</option>
                                                <option value="BK" {{ old('nilai.'.$kuk->id, $nilaiTersimpan) === 'BK' ? 'selected' : '' }}>✗ Belum Kompeten</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-8 col-md-7">
                                            <textarea name="catatan[{{ $kuk->id }}]" class="form-control" rows="2" placeholder="Catatan untuk kriteria ini (opsional)">{{ old('catatan.'.$kuk->id, $catatanTersimpan) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @empty
                        <p class="text-muted mb-0">Belum ada elemen kompetensi untuk unit ini.</p>
                    @endforelse
                </div>
            </div>
        @empty
            <div class="alert alert-secondary mb-0">
                <i class="bi bi-info-circle"></i> Belum ada Unit Kompetensi pada program ini.
            </div>
        @endforelse
    @endif

    <div class="sticky-submit">
        <button type="button" class="btn btn-success btn-lg px-5" onclick="confirmSubmit()">
            <i class="bi bi-save"></i> Simpan Penilaian
        </button>
        <a href="{{ route('asesor.pengajuan.show', $pengajuan->id) }}" class="btn btn-outline-secondary btn-lg px-4 ms-2">
            <i class="bi bi-x-circle"></i> Batal
        </a>
    </div>
</form>
@endsection

@push('scripts')
<script>
function confirmSubmit() {
    if (confirm('Apakah Anda yakin ingin menyimpan penilaian ini?')) {
        document.getElementById('penilaianForm').submit();
    }
}
</script>
@endpush
