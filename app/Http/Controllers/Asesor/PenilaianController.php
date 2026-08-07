<?php

namespace App\Http\Controllers\Asesor;

use App\Http\Controllers\Controller;
use App\Models\PengajuanAsesorAssessment;
use App\Models\PengajuanSkema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PenilaianController extends Controller
{
    public function show($pengajuanId)
    {
        $pengajuan = PengajuanSkema::whereHas('asesors', function ($query) {
                $query->where('users.id', Auth::id());
            })
            ->with([
                'user',
                'program.units.elemenKompetensis.kriteriaUnjukKerja',
                'asesorAssessments' => function ($query) {
                    $query->where('asesor_id', Auth::id());
                },
            ])
            ->findOrFail($pengajuanId);

        $penilaianTersimpan = $pengajuan->asesorAssessments
            ->keyBy('kriteria_unjuk_kerja_id');

        return view('asesor.penilaian.show', compact('pengajuan', 'penilaianTersimpan'));
    }

    public function store(Request $request, $pengajuanId)
    {
        $pengajuan = PengajuanSkema::whereHas('asesors', function ($query) {
                $query->where('users.id', Auth::id());
            })
            ->with('program.units.elemenKompetensis.kriteriaUnjukKerja')
            ->findOrFail($pengajuanId);

        $allowedKukIds = $pengajuan->program->units
            ->flatMap->elemenKompetensis
            ->flatMap->kriteriaUnjukKerja
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $request->validate([
            'nilai' => ['required', 'array'],
            'nilai.*' => ['required', Rule::in(['K', 'BK'])],
            'catatan' => ['nullable', 'array'],
            'catatan.*' => ['nullable', 'string', 'max:2000'],
        ]);

        foreach ($request->nilai as $kukId => $nilai) {
            if (! in_array((int) $kukId, $allowedKukIds, true)) {
                abort(422, 'Kriteria unjuk kerja tidak termasuk dalam skema yang dinilai.');
            }

            PengajuanAsesorAssessment::updateOrCreate(
                [
                    'pengajuan_skema_id' => $pengajuan->id,
                    'kriteria_unjuk_kerja_id' => $kukId,
                ],
                [
                    'asesor_id' => Auth::id(),
                    'nilai' => $nilai,
                    'catatan' => $request->catatan[$kukId] ?? null,
                ]
            );
        }

        return redirect()
            ->route('asesor.dashboard')
            ->with('success', 'Penilaian berhasil disimpan');
    }
}
