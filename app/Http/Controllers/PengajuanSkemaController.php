<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePengajuanRequest;
use App\Models\PengajuanSkema;
use App\Models\ProgramPelatihan;
use App\Models\PengajuanApl01;
use App\Models\PengajuanApl02;
use App\Models\PengajuanDokumen;
use App\Models\User;
use App\Models\PengajuanPortofolio;
use App\Models\PengajuanPersyaratanDasar;
use App\Models\PengajuanBuktiAdministratif;
use App\Models\PengajuanBuktiPortofolio;
use App\Models\PengajuanBuktiKompetensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class PengajuanSkemaController extends Controller
{

    public function pilihSkema()
    {
        // Ambil semua skema yang published
        $programs = ProgramPelatihan:: where('is_published', 1)
            ->orderBy('nama', 'asc')
            ->get();

        // Ambil skema yang sudah pernah diajukan user ini
        $pengajuanUser = PengajuanSkema::where('user_id', Auth::id())
            ->pluck('program_pelatihan_id')
            ->toArray();

        return view('pengajuan.pilih-skema', compact('programs', 'pengajuanUser'));
    }

     public function create($programId)
    {
        // Cari program dengan semua relationships untuk 6-tab system
        $program = ProgramPelatihan::with([
            'units.elemenKompetensis.kriteriaUnjukKerja',
            'persyaratanDasar',
            'buktiAdministratif',
            'buktiPortofolioTemplate'
        ])->findOrFail($programId);

        // Cek apakah user sudah pernah mengajukan skema ini (status pending/approved)
        $existingPengajuan = PengajuanSkema::where('user_id', Auth::id())
            ->where('program_pelatihan_id', $programId)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existingPengajuan) {
            return redirect()->route('dashboard.user')
                ->with('error', 'Anda sudah mengajukan skema ini dan sedang dalam proses review.');
        }

        // Use new 6-tab view
        return view('pengajuan.create-6tab', compact('program'));
    }

    public function store(StorePengajuanRequest $request)
    {

        DB::beginTransaction();

        try {
            // Create pengajuan_skema
            $pengajuan = PengajuanSkema::create([
                'user_id' => auth()->id(),
                'program_pelatihan_id' => $request->program_pelatihan_id,
                'status' => 'pending',
                'tanggal_pengajuan' => now('Asia/Jakarta'),
            ]);

            // Create pengajuan_apl01
            PengajuanApl01::create([
                'pengajuan_skema_id' => $pengajuan->id,
                'nama_lengkap' => $request->nama_lengkap,
                'nik' => $request->nik,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'jenis_kelamin' => $request->jenis_kelamin,
                'kebangsaan' => $request->kebangsaan ?? 'Indonesia',
                'alamat_rumah' => $request->alamat_rumah,
                'kode_pos' => $request->kode_pos,
                'telepon_rumah' => $request->telepon_rumah,
                'telepon_kantor' => $request->telepon_kantor,
                'email' => $request->email,
                'kualifikasi_pendidikan' => $request->kualifikasi_pendidikan,
                'pekerjaan' => $request->pekerjaan,
                'nama_institusi' => $request->nama_institusi,
                'jabatan' => $request->jabatan,
                'alamat_kantor' => $request->alamat_kantor,
                'telepon_kantor' => $request->telepon_kantor_pekerjaan,
                'fax' => $request->fax,
                'email_kantor' => $request->email_kantor,
                'nama_sertifikat' => $request->nama_sertifikat,
                'nomor_sertifikat' => $request->nomor_sertifikat,
                'tujuan_asesmen' => $request->tujuan_asesmen,
                'bukti_penyertaan_dasar' => $request->bukti_penyertaan_dasar,
                'bukti_administrasif' => $request->bukti_administrasif,
                'catatan' => $request->catatan,
            ]);

           // ✅ SIMPAN SELF ASSESSMENT PER KUK (ARSITEKTUR BARU & BENAR)
if ($request->has('self_assessment')) {
    foreach ($request->self_assessment as $kukId => $status) {
        \App\Models\PengajuanSelfAssessment::create([
            'pengajuan_skema_id' => $pengajuan->id,
            'kriteria_unjuk_kerja_id' => $kukId,
            'nilai' => $status, // "K" atau "BK"
        ]);
    }
}


            if ($request->hasFile('portfolio')) {
    foreach ($request->file('portfolio') as $unitId => $files) {
        foreach ($files as $index => $file) {
            if ($file && $file->isValid()) {
                $path = $file->store('pengajuan_portfolio', 'public');

                // Get deskripsi jika ada
                $deskripsi = null;
                if (isset($request->portfolio_deskripsi[$unitId][$index])) {
                    $deskripsi = $request->portfolio_deskripsi[$unitId][$index];
                }

                PengajuanPortofolio:: create([
                    'pengajuan_skema_id' => $pengajuan->id,
                    'unit_kompetensi_id' => $unitId,
                    'nama_file' => $file->getClientOriginalName(),
                    'path' => $path,
                    'ukuran' => $file->getSize(),
                    'tipe_file' => $file->getClientOriginalExtension(),
                    'deskripsi' => $deskripsi,
                ]);
            }
        }
    }
}

            // Handle file uploads
            if ($request->hasFile('dokumen')) {
                foreach ($request->file('dokumen') as $index => $file) {
                    $jenisDokumen = $request->jenis_dokumen[$index] ?? 'lainnya';
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('pengajuan_dokumen', $fileName, 'public');

                    PengajuanDokumen::create([
                        'pengajuan_skema_id' => $pengajuan->id,
                        'jenis_dokumen' => $jenisDokumen,
                        'nama_file' => $file->getClientOriginalName(),
                        'path' => $path,
                        'ukuran' => $file->getSize(),
                    ]);
                }
            }

            // Handle persyaratan dasar file uploads (6-tab system)
            if ($request->hasFile('persyaratan_dasar')) {
                foreach ($request->file('persyaratan_dasar') as $persyaratanId => $file) {
                    if ($file && $file->isValid()) {
                        // Validate file size (max 2MB)
                        if ($file->getSize() > 2097152) {
                            throw new \Exception('File persyaratan dasar terlalu besar. Maksimal 2MB.');
                        }

                        $path = $file->store('pengajuan_persyaratan_dasar', 'public');

                        PengajuanPersyaratanDasar::create([
                            'pengajuan_skema_id' => $pengajuan->id,
                            'persyaratan_dasar_id' => $persyaratanId,
                            'nama_file' => $file->getClientOriginalName(),
                            'path' => $path,
                            'ukuran' => $file->getSize(),
                        ]);
                    }
                }
            }

            // Handle bukti administratif file uploads (6-tab system)
            if ($request->hasFile('bukti_administratif')) {
                foreach ($request->file('bukti_administratif') as $buktiId => $file) {
                    if ($file && $file->isValid()) {
                        // Validate file size (max 2MB)
                        if ($file->getSize() > 2097152) {
                            throw new \Exception('File bukti administratif terlalu besar. Maksimal 2MB.');
                        }

                        $path = $file->store('pengajuan_bukti_administratif', 'public');

                        PengajuanBuktiAdministratif::create([
                            'pengajuan_skema_id' => $pengajuan->id,
                            'bukti_administratif_id' => $buktiId,
                            'nama_file' => $file->getClientOriginalName(),
                            'path' => $path,
                            'ukuran' => $file->getSize(),
                        ]);
                    }
                }
            }

            // Handle bukti portofolio file uploads (6-tab system)
            if ($request->hasFile('bukti_portofolio')) {
                foreach ($request->file('bukti_portofolio') as $portofolioId => $file) {
                    if ($file && $file->isValid()) {
                        // Validate file size (max 2MB)
                        if ($file->getSize() > 2097152) {
                            throw new \Exception('File bukti portofolio terlalu besar. Maksimal 2MB.');
                        }

                        $path = $file->store('pengajuan_bukti_portofolio', 'public');

                        PengajuanBuktiPortofolio::create([
                            'pengajuan_skema_id' => $pengajuan->id,
                            'bukti_portofolio_template_id' => $portofolioId,
                            'nama_file' => $file->getClientOriginalName(),
                            'path' => $path,
                            'ukuran' => $file->getSize(),
                        ]);
                    }
                }
            }

            // Handle bukti kompetensi file uploads (6-tab system)
            if ($request->hasFile('bukti_kompetensi')) {
                foreach ($request->file('bukti_kompetensi') as $kukId => $file) {
                    if ($file && $file->isValid()) {
                        // Validate file size (max 2MB)
                        if ($file->getSize() > 2097152) {
                            throw new \Exception('File bukti kompetensi terlalu besar. Maksimal 2MB.');
                        }

                        $path = $file->store('pengajuan_bukti_kompetensi', 'public');

                        PengajuanBuktiKompetensi::create([
                            'pengajuan_skema_id' => $pengajuan->id,
                            'kriteria_unjuk_kerja_id' => $kukId,
                            'nama_file' => $file->getClientOriginalName(),
                            'path' => $path,
                            'ukuran' => $file->getSize(),
                        ]);
                    }
                }
            }

            // Handle TTD Digital
            if ($request->has('ttd_digital') && $request->ttd_digital) {
                // Save digital signature to APL01
                $apl01 = PengajuanApl01::where('pengajuan_skema_id', $pengajuan->id)->first();
                if ($apl01) {
                    $apl01->update(['ttd' => $request->ttd_digital]);
                }
            }

            DB::commit();

            return redirect()->route('dashboard.user')
                ->with('success', 'Pengajuan skema berhasil dikirim. Mohon menunggu konfirmasi admin.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan pengajuan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $pengajuan = PengajuanSkema::with(['program', 'apl01', 'apl02.unitKompetensi', 'dokumen', 'approver'])
            ->findOrFail($id);

        // Check authorization (user owns it or user is admin)
        if (auth()->user()->role !== 'admin' && $pengajuan->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke pengajuan ini.');
        }

        return view('pengajuan.show', compact('pengajuan'));
    }

    public function draft(Request $request)
    {
        // Save draft data to session
        $request->session()->put('pengajuan_draft', $request->all());

        return response()->json([
            'success' => true,
            'message' => 'Draft berhasil disimpan.'
        ]);
    }

    public function destroy($id)
    {
        $pengajuan = PengajuanSkema::findOrFail($id);

        // Check authorization
        if ($pengajuan->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus pengajuan ini.');
        }

        // Only allow delete if status is 'draft'
        if ($pengajuan->status !== 'draft') {
            return back()->with('error', 'Hanya pengajuan dengan status draft yang dapat dihapus.');
        }

        // Delete related documents from storage
        foreach ($pengajuan->dokumen as $dokumen) {
            Storage::disk('public')->delete($dokumen->path);
        }

        $pengajuan->delete();

        return redirect()->route('dashboard.user')
            ->with('success', 'Pengajuan berhasil dihapus.');
    }
}
