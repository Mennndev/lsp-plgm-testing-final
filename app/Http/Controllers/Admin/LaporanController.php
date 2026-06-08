<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalAsesmen;
use App\Models\Pembayaran;
use App\Models\PengajuanSkema;
use App\Models\ProgramPelatihan;
use App\Models\Sertifikat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'tanggal_dari' => ['nullable', 'date'],
            'tanggal_sampai' => ['nullable', 'date', 'after_or_equal:tanggal_dari'],
            'program' => ['nullable', 'integer', 'exists:program_pelatihans,id'],
        ]);

        $programs = ProgramPelatihan::orderBy('nama')->get(['id', 'kode_skema', 'nama']);
        $pengajuanQuery = $this->filteredPengajuanQuery($filters);

        $statusPengajuan = (clone $pengajuanQuery)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $pembayaranBerhasil = Pembayaran::query()
            ->where('status', 'success')
            ->whereHas('pengajuan', function (Builder $query) use ($filters) {
                $this->applyPengajuanFilters($query, $filters);
            });

        $statistik = [
            'total_pengajuan' => (clone $pengajuanQuery)->count(),
            'pengajuan_disetujui' => (clone $pengajuanQuery)->whereIn('status', ['approved', 'paid'])->count(),
            'asesmen_selesai' => JadwalAsesmen::query()
                ->where('status', 'completed')
                ->whereHas('pengajuan', function (Builder $query) use ($filters) {
                    $this->applyPengajuanFilters($query, $filters);
                })
                ->count(),
            'sertifikat_terbit' => Sertifikat::query()
                ->whereHas('pengajuan', function (Builder $query) use ($filters) {
                    $this->applyPengajuanFilters($query, $filters);
                })
                ->count(),
            'pembayaran_berhasil' => (clone $pembayaranBerhasil)->count(),
            'total_pendapatan' => (clone $pembayaranBerhasil)->sum('nominal'),
        ];

        $pengajuanPerProgram = $this->filteredPengajuanQuery($filters)
            ->with('program')
            ->select('program_pelatihan_id', DB::raw('COUNT(*) as total'))
            ->groupBy('program_pelatihan_id')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        $pengajuanList = $this->filteredPengajuanQuery($filters)
            ->with(['user', 'program', 'pembayaran', 'jadwalAsesmen', 'sertifikat'])
            ->latest('tanggal_pengajuan')
            ->paginate(15)
            ->withQueryString();

        return view('admin.laporan.index', compact(
            'filters',
            'pengajuanList',
            'pengajuanPerProgram',
            'programs',
            'statistik',
            'statusPengajuan'
        ));
    }

    private function filteredPengajuanQuery(array $filters): Builder
    {
        return $this->applyPengajuanFilters(PengajuanSkema::query(), $filters);
    }

    private function applyPengajuanFilters(Builder $query, array $filters): Builder
    {
        if (! empty($filters['tanggal_dari'])) {
            $query->whereDate('tanggal_pengajuan', '>=', $filters['tanggal_dari']);
        }

        if (! empty($filters['tanggal_sampai'])) {
            $query->whereDate('tanggal_pengajuan', '<=', $filters['tanggal_sampai']);
        }

        if (! empty($filters['program'])) {
            $query->where('program_pelatihan_id', $filters['program']);
        }

        return $query;
    }
}
