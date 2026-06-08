<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanSkema;
use App\Models\Sertifikat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SertifikatController extends Controller
{
    public function index(Request $request)
    {
        $query = PengajuanSkema::with(['user', 'program', 'jadwalAsesmen.asesor', 'sertifikat'])
            ->whereHas('jadwalAsesmen', function ($builder) {
                $builder->where('status', 'completed');
            });

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($builder) use ($search) {
                $builder->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('nama', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('program', function ($programQuery) use ($search) {
                    $programQuery->where('nama', 'like', "%{$search}%")
                        ->orWhere('kode_skema', 'like', "%{$search}%");
                })->orWhereHas('sertifikat', function ($sertifikatQuery) use ($search) {
                    $sertifikatQuery->where('nomor_sertifikat', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'terbit') {
                $query->whereHas('sertifikat');
            }

            if ($request->status === 'belum') {
                $query->whereDoesntHave('sertifikat');
            }
        }

        $pengajuanList = $query->latest('updated_at')
            ->paginate(15)
            ->withQueryString();

        $totalSiapUpload = PengajuanSkema::whereHas('jadwalAsesmen', function ($builder) {
            $builder->where('status', 'completed');
        })->whereDoesntHave('sertifikat')->count();

        $totalTerbit = Sertifikat::count();

        return view('admin.sertifikat.index', compact(
            'pengajuanList',
            'totalSiapUpload',
            'totalTerbit'
        ));
    }

    public function create($pengajuanId)
    {
        $pengajuan = PengajuanSkema::with(['user', 'program', 'jadwalAsesmen', 'sertifikat'])
            ->findOrFail($pengajuanId);

        if ($pengajuan->jadwalAsesmen?->status !== 'completed') {
            return redirect()
                ->route('admin.pengajuan.show', $pengajuan->id)
                ->with('error', 'Sertifikat hanya bisa diunggah setelah jadwal asesmen berstatus selesai.');
        }

        return view('admin.sertifikat.create', compact('pengajuan'));
    }

    public function store(Request $request, $pengajuanId)
    {
        $pengajuan = PengajuanSkema::with(['user', 'program', 'jadwalAsesmen', 'sertifikat'])
            ->findOrFail($pengajuanId);

        if ($pengajuan->jadwalAsesmen?->status !== 'completed') {
            return redirect()
                ->route('admin.pengajuan.show', $pengajuan->id)
                ->with('error', 'Sertifikat hanya bisa diunggah setelah jadwal asesmen berstatus selesai.');
        }

        $existingCertificate = $pengajuan->sertifikat;
        $fileRules = [
            $existingCertificate?->file_sertifikat ? 'nullable' : 'required',
            'file',
            'mimes:pdf,jpg,jpeg,png',
            'max:2048',
        ];

        $validated = $request->validate([
            'nomor_sertifikat' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sertifikat', 'nomor_sertifikat')->ignore($existingCertificate?->id),
            ],
            'jenis_bukti' => ['nullable', 'string', 'max:255'],
            'tanggal_terbit' => ['required', 'date'],
            'tanggal_berlaku_sampai' => ['required', 'date', 'after_or_equal:tanggal_terbit'],
            'file_sertifikat' => $fileRules,
        ]);

        $filePath = $existingCertificate?->file_sertifikat;

        if ($request->hasFile('file_sertifikat')) {
            $oldFilePath = $filePath;
            $filePath = $request->file('file_sertifikat')->store('sertifikat', 'public');

            if ($oldFilePath && Storage::disk('public')->exists($oldFilePath)) {
                Storage::disk('public')->delete($oldFilePath);
            }
        }

        Sertifikat::updateOrCreate(
            [
                'pengajuan_skema_id' => $pengajuan->id,
            ],
            [
                'user_id' => $pengajuan->user_id,
                'nomor_sertifikat' => $validated['nomor_sertifikat'],
                'jenis_bukti' => $validated['jenis_bukti'] ?? 'Sertifikat Kompetensi',
                'tanggal_terbit' => $validated['tanggal_terbit'],
                'tanggal_berlaku_sampai' => $validated['tanggal_berlaku_sampai'],
                'file_sertifikat' => $filePath,
                'status' => 'aktif',
            ]
        );

        return redirect()
            ->route('admin.sertifikat.index')
            ->with('success', 'Sertifikat berhasil disimpan.');
    }
}
