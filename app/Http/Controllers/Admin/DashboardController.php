<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalAsesmen;
use App\Models\PengajuanSkema;
use App\Models\ProfesiTerkait;
use App\Models\ProgramPelatihan;
use App\Models\Sertifikat;
use App\Models\UnitKompetensi;

class DashboardController extends Controller
{
    public function index()
    {
        // STAT KOTAK ATAS
        $totalProgram = ProgramPelatihan::where('is_published', 1)->count();
        $totalUnit = UnitKompetensi::count();
        $totalProfesi = ProfesiTerkait::count();

        // jumlah asesi = user yang pernah mengajukan skema (distinct user_id)
        $totalAsesi = PengajuanSkema::distinct('user_id')->count('user_id');
        $totalPengajuanMenunggu = PengajuanSkema::where('status', 'pending')->count();
        $totalJadwalSelesai = JadwalAsesmen::where('status', 'completed')->count();
        $totalSertifikatTerbit = Sertifikat::count();
        $totalSertifikatPerluUpload = PengajuanSkema::whereHas('jadwalAsesmen', function ($query) {
            $query->where('status', 'completed');
        })->whereDoesntHave('sertifikat')->count();

        // PROGRAM TERBARU
        $programTerbaru = ProgramPelatihan::orderByDesc('created_at')
            ->take(5)
            ->get();

        // PENGAJUAN SKEMA TERBARU (ganti dari pendaftaranBaru)
        $pengajuanTerbaru = PengajuanSkema::with(['program', 'user'])
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        $sertifikatPerluUpload = PengajuanSkema::with(['program', 'user', 'jadwalAsesmen'])
            ->whereHas('jadwalAsesmen', function ($query) {
                $query->where('status', 'completed');
            })
            ->whereDoesntHave('sertifikat')
            ->latest('updated_at')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalProgram',
            'totalUnit',
            'totalProfesi',
            'totalAsesi',
            'totalPengajuanMenunggu',
            'totalJadwalSelesai',
            'totalSertifikatTerbit',
            'totalSertifikatPerluUpload',
            'programTerbaru',
            'pengajuanTerbaru',
            'sertifikatPerluUpload'
        ));
    }
}
