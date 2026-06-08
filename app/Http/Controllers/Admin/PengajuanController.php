<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanSkema;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PengajuanController extends Controller
{
    private const DEFAULT_PAYMENT_AMOUNT = 500000;

    public function index(Request $request)
    {
        $query = PengajuanSkema::with(['user', 'program']);

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by program
        if ($request->has('program') && $request->program != '') {
            $query->where('program_pelatihan_id', $request->program);
        }

        // Filter by date
        if ($request->has('tanggal_dari') && $request->tanggal_dari != '') {
            $query->whereDate('tanggal_pengajuan', '>=', $request->tanggal_dari);
        }
        if ($request->has('tanggal_sampai') && $request->tanggal_sampai != '') {
            $query->whereDate('tanggal_pengajuan', '<=', $request->tanggal_sampai);
        }

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $pengajuanList = $query->orderBy('tanggal_pengajuan', 'desc')
            ->paginate(20);

        return view('admin.pengajuan.index', compact('pengajuanList'));
    }

    public function show($id)
    {
        $pengajuan = PengajuanSkema::with([
            'user',
            'program',
            'apl01',
            'buktiKompetensi.kuk.elemen.unit',
            'pengajuanBuktiAdministratif',
            'pengajuanBuktiPortofolio',
            'pengajuanPersyaratanDasar',
            'pembayaran',
            'approver',
            'jadwalAsesmen.asesor',
            'sertifikat',
            'asesors',
        ])->findOrFail($id);

        $selfAssessments = $pengajuan->selfAssessments()->with('kuk.elemen.unit')->get();
        $buktiKompetensi = $pengajuan->buktiKompetensi;
        $listAsesor = User::where('role', 'asesor')->get();
        $assignedAsesorId = $pengajuan->asesors->first()?->id;

        return view('admin.pengajuan.show', compact('pengajuan', 'selfAssessments', 'buktiKompetensi', 'listAsesor', 'assignedAsesorId'));
    }

    public function approve($id, Request $request)
    {
        $request->validate([
            'catatan_admin' => 'nullable|string',
        ]);

        $pengajuan = PengajuanSkema::with(['user', 'program'])->findOrFail($id);

        $pengajuan->update([
            'status' => 'approved',
            'tanggal_disetujui' => now(),
            'catatan_admin' => $request->catatan_admin,
            'approved_by' => Auth::id(),
        ]);

        // Buat record pembayaran (tanpa snap token dulu, nanti generate saat user buka halaman bayar)
        \App\Models\Pembayaran::create([
            'pengajuan_skema_id' => $pengajuan->id,
            'user_id' => $pengajuan->user_id,
            'order_id' => \App\Models\Pembayaran::generateOrderId(),
            'nominal' => $pengajuan->program->estimasi_biaya ?? 500000,
            'status' => 'pending',
            'expired_at' => now()->addDays(7), // 7 hari untuk bayar
        ]);

        // Kirim notifikasi ke user
        \App\Services\NotificationService::sendPengajuanApproved($pengajuan->user, $pengajuan);

        return redirect()->route('admin.pengajuan.show', $id)
            ->with('success', 'Pengajuan berhasil disetujui.  User dapat melakukan pembayaran.');
    }

    public function reject($id, Request $request)
    {
        $request->validate([
            'catatan_admin' => 'required|string',
        ], [
            'catatan_admin.required' => 'Catatan admin wajib diisi untuk penolakan.',
        ]);

        $pengajuan = PengajuanSkema::with(['user', 'program'])->findOrFail($id);

        $pengajuan->update([
            'status' => 'rejected',
            'catatan_admin' => $request->catatan_admin,
            'approved_by' => Auth::id(),
        ]);

        // Kirim notifikasi ke user
        NotificationService::sendPengajuanRejected($pengajuan->user, $pengajuan, $request->catatan_admin);

        return redirect()->route('admin.pengajuan.show', $id)
            ->with('success', 'Pengajuan berhasil ditolak dan notifikasi telah dikirim ke user.');
    }

    public function assignAsesor(Request $request, $pengajuanId)
    {
        $request->validate([
            'asesor_id' => 'required|exists:users,id',
        ]);

        // Hapus dulu kalau sebelumnya sudah ada (biar 1 asesor saja)
        DB::table('pengajuan_asesor')
            ->where('pengajuan_skema_id', $pengajuanId)
            ->delete();

        // Insert baru
        DB::table('pengajuan_asesor')->insert([
            'pengajuan_skema_id' => $pengajuanId,
            'asesor_id' => $request->asesor_id,
            'role' => 'utama',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Asesor berhasil ditugaskan');
    }
}
