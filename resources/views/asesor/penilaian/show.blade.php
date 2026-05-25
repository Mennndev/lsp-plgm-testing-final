@extends('layouts.asesor')

@section('title', 'Penilaian Asesi')

@section('content')
<!-- ✅ BREADCRUMB -->
<div class="asesor-breadcrumb">
    <a href="{{ route('asesor.dashboard') }}"><i class="bi bi-house-door"></i> Dashboard</a>
    <span class="separator">›</span>
    <a href="{{ route('asesor.pengajuan.show', $pengajuan->id) }}">Detail Pengajuan</a>
    <span class="separator">›</span>
    <span>Penilaian</span>
</div>

<!-- ✅ CARD HEADER INFO -->
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

<!-- ✅ PROGRESS BAR OVERALL -->
@php
    $totalKUK = 0;
    $dinilaiKUK = 0;

    // Calculate total KUK
    foreach($pengajuan->program->units as $unit) {
        foreach($unit->elemenKompetensis as $elemen) {
            $totalKUK += $elemen->kriteriaUnjukKerja->count();
        }
    }

    // Count already assessed KUK from PengajuanAsesorAssessment table
    if ($totalKUK > 0) {
        $dinilaiKUK = \App\Models\PengajuanAsesorAssessment::where('pengajuan_skema_id', $pengajuan->id)
            ->where('asesor_id', Auth::id())
            ->count();
    }

    // Calculate percentage
    $persentase = $totalKUK > 0 ? round(($dinilaiKUK / $totalKUK) * 100) : 0;
@endphp
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span style="font-weight: 600; color: #233C7E;">Progress Penilaian</span>
            <span style="font-weight: 600; color: #233C7E;">{{ $dinilaiKUK }} / {{ $totalKUK }} KUK</span>
        </div>
        <div class="progress-overall">
            <div class="progress-bar" role="progressbar" style="width: {{ $persentase }}%" aria-valuenow="{{ $persentase }}" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('asesor.pengajuan.store', $pengajuan->id) }}" id="penilaianForm">
    @csrf

    @forelse($pengajuan->program->units as $unit)
        <!-- ✅ UNIT KOMPETENSI CARD -->
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
                                        <option value="K" {{ old('nilai.' . $kuk->id, $nilaiTersimpan) === 'K' ? 'selected' : '' }}>
                                            ✓ Kompeten
                                        </option>
                                        <option value="BK" {{ old('nilai.' . $kuk->id, $nilaiTersimpan) === 'BK' ? 'selected' : '' }}>
                                            ✗ Belum Kompeten
                                        </option>
                                    </select>
                                </div>

                                <div class="col-lg-8 col-md-7">
                                    <textarea name="catatan[{{ $kuk->id }}]" class="form-control" rows="2" placeholder="Catatan untuk kriteria ini (opsional)">{{ old('catatan.' . $kuk->id, $catatanTersimpan) }}</textarea>
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
            <i class="bi bi-info-circle"></i> Belum ada unit kompetensi pada program ini.
        </div>
    @endforelse

    <!-- ✅ STICKY BUTTON -->
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
