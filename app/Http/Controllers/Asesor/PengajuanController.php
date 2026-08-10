<?php

namespace App\Http\Controllers\Asesor;

use App\Http\Controllers\Controller;
use App\Models\PengajuanSkema;
use Illuminate\Support\Facades\Auth;

class PengajuanController extends Controller
{
    public function show($id)
    {
        $pengajuan = PengajuanSkema::whereHas('asesors', function ($q) {
                $q->where('users.id', Auth::id());
            })
            ->with([
                'user',
                'program',
                'apl02.unitKompetensi',
                'portfolio.unitKompetensi',
                'selfAssessments',
                'buktiKompetensi',
            ])
            ->findOrFail($id);

        $buktiUnit = $pengajuan->portfolio
            ->where('deskripsi', 'Bukti Kompetensi APL-02')
            ->groupBy('unit_kompetensi_id');

        return view('asesor.pengajuan.show', compact('pengajuan', 'buktiUnit'));
    }
}
