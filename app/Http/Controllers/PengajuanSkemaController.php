<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePengajuanRequest;
use App\Models\PengajuanApl01;
use App\Models\PengajuanBuktiAdministratif;
use App\Models\PengajuanBuktiKompetensi;
use App\Models\PengajuanBuktiPortofolio;
use App\Models\PengajuanDokumen;
use App\Models\PengajuanPersyaratanDasar;
use App\Models\PengajuanPortofolio;
use App\Models\PengajuanSelfAssessment;
use App\Models\PengajuanSkema;
use App\Models\ProgramPelatihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class PengajuanSkemaController extends Controller
{
    public function pilihSkema()
    {
        $programs = ProgramPelatihan::where('is_published', 1)
            ->orderBy('nama')
            ->get();

        $pengajuanUser = PengajuanSkema::where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'approved', 'paid'])
            ->pluck('program_pelatihan_id')
            ->toArray();

        return view('pengajuan.pilih-skema', compact('programs', 'pengajuanUser'));
    }

    public function create($programId)
    {
        $program = ProgramPelatihan::with([
            'units.elemenKompetensis.kriteriaUnjukKerja',
            'persyaratanDasar',
            'buktiAdministratif',
            'buktiPortofolioTemplate',
        ])->findOrFail($programId);

        $existingPengajuan = PengajuanSkema::where('user_id', Auth::id())
            ->where('program_pelatihan_id', $programId)
            ->whereIn('status', ['pending', 'approved', 'paid'])
            ->exists();

        if ($existingPengajuan) {
            return redirect()->route('dashboard.user')
                ->with('error', 'Anda sudah mengajukan skema ini dan pengajuan masih aktif.');
        }

        return view('pengajuan.create-6tab', compact('program'));
    }

    public function store(StorePengajuanRequest $request)
    {
        $storedPaths = [];
        DB::beginTransaction();

        try {
            $alreadyExists = PengajuanSkema::where('user_id', Auth::id())
                ->where('program_pelatihan_id', $request->integer('program_pelatihan_id'))
                ->whereIn('status', ['pending', 'approved', 'paid'])
                ->lockForUpdate()
                ->exists();

            if ($alreadyExists) {
                throw ValidationException::withMessages([
                    'program_pelatihan_id' => 'Anda sudah memiliki pengajuan aktif untuk skema ini.',
                ]);
            }

            $pengajuan = PengajuanSkema::create([
                'user_id' => Auth::id(),
                'program_pelatihan_id' => $request->integer('program_pelatihan_id'),
                'status' => 'pending',
                'tanggal_pengajuan' => now('Asia/Jakarta'),
            ]);

            $apl01 = PengajuanApl01::create([
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
                'hp' => $request->hp,
                'email' => $request->email,
                'kualifikasi_pendidikan' => $request->kualifikasi_pendidikan,
                'pekerjaan' => $request->pekerjaan,
                'nama_institusi' => $request->nama_institusi,
                'jabatan' => $request->jabatan,
                'alamat_kantor' => $request->alamat_kantor,
                'telepon_kantor' => $request->telepon_kantor,
                'fax' => $request->fax,
                'email_kantor' => $request->email_kantor,
                'nama_sertifikat' => $request->nama_sertifikat,
                'nomor_sertifikat' => $request->nomor_sertifikat,
                'tujuan_asesmen' => $request->tujuan_asesmen,
                'bukti_penyertaan_dasar' => $request->bukti_penyertaan_dasar,
                'bukti_administrasif' => $request->bukti_administrasif,
                'catatan' => $request->catatan,
                'ttd' => $request->ttd_digital,
            ]);

            foreach ($request->input('self_assessment', []) as $kukId => $status) {
                PengajuanSelfAssessment::create([
                    'pengajuan_skema_id' => $pengajuan->id,
                    'kriteria_unjuk_kerja_id' => $kukId,
                    'nilai' => $status,
                ]);
            }

            if ($request->hasFile('portfolio')) {
                foreach ($request->file('portfolio') as $unitId => $files) {
                    foreach ($files as $index => $file) {
                        if (! $file || ! $file->isValid()) {
                            continue;
                        }

                        $path = $file->store('pengajuan_portfolio', 'public');
                        $storedPaths[] = $path;

                        PengajuanPortofolio::create([
                            'pengajuan_skema_id' => $pengajuan->id,
                            'unit_kompetensi_id' => $unitId,
                            'nama_file' => $file->getClientOriginalName(),
                            'path' => $path,
                            'ukuran' => $file->getSize(),
                            'tipe_file' => $file->getClientOriginalExtension(),
                            'deskripsi' => $request->input("portfolio_deskripsi.{$unitId}.{$index}"),
                        ]);
                    }
                }
            }

            if ($request->hasFile('dokumen')) {
                foreach ($request->file('dokumen') as $index => $file) {
                    if (! $file || ! $file->isValid()) {
                        continue;
                    }

                    $path = $file->store('pengajuan_dokumen', 'public');
                    $storedPaths[] = $path;

                    PengajuanDokumen::create([
                        'pengajuan_skema_id' => $pengajuan->id,
                        'jenis_dokumen' => $request->input("jenis_dokumen.{$index}", 'lainnya'),
                        'nama_file' => $file->getClientOriginalName(),
                        'path' => $path,
                        'ukuran' => $file->getSize(),
                    ]);
                }
            }

            $this->storeSingleFiles(
                $request->file('persyaratan_dasar', []),
                'pengajuan_persyaratan_dasar',
                $storedPaths,
                fn ($id, $file, $path) => PengajuanPersyaratanDasar::create([
                    'pengajuan_skema_id' => $pengajuan->id,
                    'persyaratan_dasar_id' => $id,
                    'nama_file' => $file->getClientOriginalName(),
                    'path' => $path,
                    'ukuran' => $file->getSize(),
                ])
            );

            $this->storeSingleFiles(
                $request->file('bukti_administratif', []),
                'pengajuan_bukti_administratif',
                $storedPaths,
                fn ($id, $file, $path) => PengajuanBuktiAdministratif::create([
                    'pengajuan_skema_id' => $pengajuan->id,
                    'bukti_administratif_id' => $id,
                    'nama_file' => $file->getClientOriginalName(),
                    'path' => $path,
                    'ukuran' => $file->getSize(),
                ])
            );

            $this->storeSingleFiles(
                $request->file('bukti_portofolio', []),
                'pengajuan_bukti_portofolio',
                $storedPaths,
                fn ($id, $file, $path) => PengajuanBuktiPortofolio::create([
                    'pengajuan_skema_id' => $pengajuan->id,
                    'bukti_portofolio_template_id' => $id,
                    'nama_file' => $file->getClientOriginalName(),
                    'path' => $path,
                    'ukuran' => $file->getSize(),
                ])
            );

            $this->storeSingleFiles(
                $request->file('bukti_kompetensi', []),
                'pengajuan_bukti_kompetensi',
                $storedPaths,
                fn ($id, $file, $path) => PengajuanBuktiKompetensi::create([
                    'pengajuan_skema_id' => $pengajuan->id,
                    'kriteria_unjuk_kerja_id' => $id,
                    'nama_file' => $file->getClientOriginalName(),
                    'path' => $path,
                    'ukuran' => $file->getSize(),
                ])
            );

            DB::commit();

            return redirect()->route('dashboard.user')
                ->with('success', 'Pengajuan skema berhasil dikirim. Mohon menunggu konfirmasi admin.');
        } catch (ValidationException $e) {
            DB::rollBack();
            Storage::disk('public')->delete($storedPaths);
            throw $e;
        } catch (Throwable $e) {
            DB::rollBack();
            Storage::disk('public')->delete($storedPaths);
            report($e);

            return back()->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan pengajuan. Silakan coba kembali.');
        }
    }

    private function storeSingleFiles(array $files, string $directory, array &$storedPaths, callable $persist): void
    {
        foreach ($files as $id => $file) {
            if (! $file || ! $file->isValid()) {
                continue;
            }

            $path = $file->store($directory, 'public');
            $storedPaths[] = $path;
            $persist($id, $file, $path);
        }
    }

    public function show($id)
    {
        $pengajuan = PengajuanSkema::with(['program', 'apl01', 'apl02.unitKompetensi', 'dokumen', 'approver'])
            ->findOrFail($id);

        if (Auth::user()->role !== 'admin' && $pengajuan->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke pengajuan ini.');
        }

        return view('pengajuan.show', compact('pengajuan'));
    }

    public function draft(Request $request)
    {
        $request->session()->put('pengajuan_draft', $request->except(['ttd_digital']));

        return response()->json([
            'success' => true,
            'message' => 'Draft berhasil disimpan.',
        ]);
    }

    public function destroy($id)
    {
        $pengajuan = PengajuanSkema::findOrFail($id);

        if ($pengajuan->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus pengajuan ini.');
        }

        if ($pengajuan->status !== 'draft') {
            return back()->with('error', 'Hanya pengajuan dengan status draft yang dapat dihapus.');
        }

        foreach ($pengajuan->dokumen as $dokumen) {
            Storage::disk('public')->delete($dokumen->path);
        }

        $pengajuan->delete();

        return redirect()->route('dashboard.user')
            ->with('success', 'Pengajuan berhasil dihapus.');
    }
}
