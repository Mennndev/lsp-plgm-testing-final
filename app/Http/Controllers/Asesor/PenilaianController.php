<?php

namespace App\Http\Controllers\Asesor;

use App\Http\Controllers\Controller;
use App\Models\PengajuanAsesorAssessment;
use App\Models\PengajuanAsesorUnitAssessment;
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
                'apl02.unitKompetensi',
                'portfolio.unitKompetensi',
                'asesorAssessments' => function ($query) {
                    $query->where('asesor_id', Auth::id());
                },
            ])
            ->findOrFail($pengajuanId);

        $totalKuk = $pengajuan->program->units
            ->flatMap->elemenKompetensis
            ->flatMap->kriteriaUnjukKerja
            ->count();

        $useUnitAssessment = $totalKuk === 0;

        $penilaianTersimpan = $pengajuan->asesorAssessments
            ->keyBy('kriteria_unjuk_kerja_id');

        $penilaianUnitTersimpan = PengajuanAsesorUnitAssessment::where('pengajuan_skema_id', $pengajuan->id)
            ->where('asesor_id', Auth::id())
            ->get()
            ->keyBy('unit_kompetensi_id');

        $apl02AsesiPerUnit = $pengajuan->apl02->keyBy('unit_kompetensi_id');
        $buktiAsesiPerUnit = $pengajuan->portfolio
            ->where('deskripsi', 'Bukti Kompetensi APL-02')
            ->groupBy('unit_kompetensi_id');

        return view('asesor.penilaian.show', compact(
            'pengajuan',
            'penilaianTersimpan',
            'penilaianUnitTersimpan',
            'apl02AsesiPerUnit',
            'buktiAsesiPerUnit',
            'useUnitAssessment'
        ));
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

        if (count($allowedKukIds) === 0) {
            $allowedUnitIds = $pengajuan->program->units
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();

            $request->validate([
                'nilai_unit' => ['required', 'array', 'size:'.count($allowedUnitIds)],
                'nilai_unit.*' => ['required', Rule::in(['K', 'BK'])],
                'catatan_unit' => ['nullable', 'array'],
                'catatan_unit.*' => ['nullable', 'string', 'max:2000'],
            ]);

            $submittedUnitIds = collect(array_keys($request->input('nilai_unit', [])))
                ->map(fn ($id) => (int) $id)
                ->sort()
                ->values()
                ->all();

            $expectedUnitIds = collect($allowedUnitIds)->sort()->values()->all();

            if ($submittedUnitIds !== $expectedUnitIds) {
                abort(422, 'Seluruh Unit Kompetensi pada skema wajib dinilai dan tidak boleh berasal dari skema lain.');
            }

            foreach ($request->input('nilai_unit', []) as $unitId => $nilai) {
                PengajuanAsesorUnitAssessment::updateOrCreate(
                    [
                        'pengajuan_skema_id' => $pengajuan->id,
                        'unit_kompetensi_id' => $unitId,
                        'asesor_id' => Auth::id(),
                    ],
                    [
                        'nilai' => $nilai,
                        'catatan' => $request->input("catatan_unit.{$unitId}"),
                    ]
                );
            }

            return redirect()
                ->route('asesor.dashboard')
                ->with('success', 'Penilaian per Unit Kompetensi berhasil disimpan.');
        }

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
