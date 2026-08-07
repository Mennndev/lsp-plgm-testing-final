<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\PengajuanSkema;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PengajuanController extends Controller
{
    private const DEFAULT_PAYMENT_AMOUNT = 500000;

    public function index(Request $request)
    {
        $query = PengajuanSkema::with(['user', 'program']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('program')) {
            $query->where('program_pelatihan_id', $request->program);
        }

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_pengajuan', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_pengajuan', '<=', $request->tanggal_sampai);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $pengajuanList = $query->orderBy('tanggal_pengajuan', 'desc')->paginate(20);

        return view('admin.pengajuan.index', compact('pengajuanList'));
    }

    public function show($id)
    {
        $pengajuan = PengajuanSkema::with([
            'user', 'program', 'apl01', 'buktiKompetensi.kuk.elemen.unit',
            'pengajuanBuktiAdministratif', 'pengajuanBuktiPortofolio',
            'pengajuanPersyaratanDasar', 'pembayaran', 'approver',
            'jadwalAsesmen.asesor', 'sertifikat', 'asesors',
        ])->findOrFail($id);

        $selfAssessments = $pengajuan->selfAssessments()->with('kuk.elemen.unit')->get();
        $buktiKompetensi = $pengajuan->buktiKompetensi;
        $listAsesor = User::where('role', 'asesor')->get();
        $assignedAsesorId = $pengajuan->asesors->first()?->id;

        return view('admin.pengajuan.show', compact('pengajuan', 'selfAssessments', 'buktiKompetensi', 'listAsesor', 'assignedAsesorId'));
    }

    public function approve($id, Request $request)
    {
        $request->validate(['catatan_admin' => 'nullable|string']);

        $pengajuan = DB::transaction(function () use ($id, $request) {
            $pengajuan = PengajuanSkema::with(['user', 'program'])->lockForUpdate()->findOrFail($id);

            if (in_array($pengajuan->status, ['approved', 'paid'], true)) {
                return $pengajuan;
            }

            if ($pengajuan->status !== 'pending') {
                abort(422, 'Hanya pengajuan berstatus menunggu yang dapat disetujui.');
            }

            $pengajuan->update([
                'status' => 'approved',
                'tanggal_disetujui' => now(),
                'catatan_admin' => $request->catatan_admin,
                'approved_by' => Auth::id(),
            ]);

            Pembayaran::firstOrCreate(
                ['pengajuan_skema_id' => $pengajuan->id],
                [
                    'user_id' => $pengajuan->user_id,
                    'order_id' => Pembayaran::generateOrderId(),
                    'nominal' => $pengajuan->program->estimasi_biaya ?? self::DEFAULT_PAYMENT_AMOUNT,
                    'status' => 'pending',
                    'expired_at' => now()->addDays(7),
                ]
            );

            return $pengajuan;
        });

        if ($pengajuan->wasChanged('status')) {
            NotificationService::sendPengajuanApproved($pengajuan->user, $pengajuan);
        }

        return redirect()->route('admin.pengajuan.show', $id)
            ->with('success', 'Pengajuan berhasil disetujui. User dapat melakukan pembayaran.');
    }

    public function reject($id, Request $request)
    {
        $request->validate([
            'catatan_admin' => 'required|string',
        ], [
            'catatan_admin.required' => 'Catatan admin wajib diisi untuk penolakan.',
        ]);

        $pengajuan = PengajuanSkema::with(['user', 'program'])->findOrFail($id);

        if ($pengajuan->status === 'paid') {
            return back()->with('error', 'Pengajuan yang sudah dibayar tidak dapat ditolak.');
        }

        $pengajuan->update([
            'status' => 'rejected',
            'catatan_admin' => $request->catatan_admin,
            'approved_by' => Auth::id(),
        ]);

        NotificationService::sendPengajuanRejected($pengajuan->user, $pengajuan, $request->catatan_admin);

        return redirect()->route('admin.pengajuan.show', $id)
            ->with('success', 'Pengajuan berhasil ditolak dan notifikasi telah dikirim ke user.');
    }

    public function assignAsesor(Request $request, $pengajuanId)
    {
        $request->validate([
            'asesor_id' => [
                'required',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', 'asesor')),
            ],
        ], [
            'asesor_id.exists' => 'User yang dipilih bukan asesor yang valid.',
        ]);

        PengajuanSkema::findOrFail($pengajuanId);

        DB::transaction(function () use ($request, $pengajuanId): void {
            DB::table('pengajuan_asesor')->where('pengajuan_skema_id', $pengajuanId)->delete();
            DB::table('pengajuan_asesor')->insert([
                'pengajuan_skema_id' => $pengajuanId,
                'asesor_id' => $request->asesor_id,
                'role' => 'utama',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return back()->with('success', 'Asesor berhasil ditugaskan');
    }
}
