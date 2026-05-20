<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanSkema;
use App\Models\Sertifikat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SertifikatController extends Controller
{
    public function create($pengajuanId)
    {
        $pengajuan = PengajuanSkema::with(['user', 'program', 'sertifikat'])
            ->findOrFail($pengajuanId);

        return view('admin.sertifikat.create', compact('pengajuan'));
    }

    public function store(Request $request, $pengajuanId)
    {
        $pengajuan = PengajuanSkema::with(['user', 'program'])
            ->findOrFail($pengajuanId);

        $validated = $request->validate([
            'nomor_sertifikat' => ['required', 'string', 'max:255', 'unique:sertifikat,nomor_sertifikat'],
            'jenis_bukti' => ['nullable', 'string', 'max:255'],
            'tanggal_terbit' => ['required', 'date'],
            'tanggal_berlaku_sampai' => ['required', 'date', 'after_or_equal:tanggal_terbit'],
            'file_sertifikat' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ]);

        $filePath = null;

        if ($request->hasFile('file_sertifikat')) {
            $filePath = $request->file('file_sertifikat')->store('sertifikat', 'public');
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
            ->route('admin.pengajuan.show', $pengajuan->id)
            ->with('success', 'Sertifikat berhasil diterbitkan.');
    }
}
