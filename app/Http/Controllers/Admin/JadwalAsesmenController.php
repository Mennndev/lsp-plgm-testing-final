<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJadwalAsesmenRequest;
use App\Models\JadwalAsesmen;
use App\Models\PengajuanSkema;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JadwalAsesmenController extends Controller
{
    public function index(Request $request)
    {
        $query = JadwalAsesmen::with(['pengajuan.user', 'pengajuan.program', 'asesor'])
            ->latest('tanggal_mulai');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_mulai', '>=', $request->input('tanggal_dari'));
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_mulai', '<=', $request->input('tanggal_sampai'));
        }

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->whereHas('pengajuan.user', function ($builder) use ($search) {
                $builder->where('nama', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $jadwalList = $query->paginate(20)->withQueryString();

        return view('admin.jadwal-asesmen.index', compact('jadwalList'));
    }

    public function upsert(StoreJadwalAsesmenRequest $request)
    {
        $payload = $request->validated();
        $userId = Auth::id();

        $pengajuan = PengajuanSkema::with('user', 'program')
            ->findOrFail($payload['pengajuan_skema_id']);

        if (! in_array($pengajuan->status, ['approved', 'paid'], true)) {
            return back()->with('error', 'Jadwal asesmen hanya bisa dibuat untuk pengajuan yang sudah disetujui atau sudah dibayar.');
        }

        DB::transaction(function () use ($payload, $pengajuan, $userId) {
            $jadwal = JadwalAsesmen::where('pengajuan_skema_id', $pengajuan->id)
                ->lockForUpdate()
                ->first();

            $isReschedule = false;
            if ($jadwal && $jadwal->tanggal_mulai?->ne($payload['tanggal_mulai'])) {
                $isReschedule = true;
            }

            if (! $jadwal) {
                $jadwal = new JadwalAsesmen([
                    'pengajuan_skema_id' => $pengajuan->id,
                    'created_by' => $userId,
                ]);
            }

            $jadwal->fill([
                'asesor_id' => $payload['asesor_id'] ?? null,
                'tanggal_mulai' => $payload['tanggal_mulai'],
                'tanggal_selesai' => $payload['tanggal_selesai'] ?? null,
                'mode_asesmen' => $payload['mode_asesmen'],
                'lokasi' => $payload['mode_asesmen'] === 'offline' ? ($payload['lokasi'] ?? null) : null,
                'tautan_meeting' => $payload['mode_asesmen'] === 'online' ? ($payload['tautan_meeting'] ?? null) : null,
                'status' => $payload['status'],
                'catatan' => $payload['catatan'] ?? null,
                'updated_by' => $userId,
            ]);

            if ($isReschedule) {
                $jadwal->reschedule_count = ($jadwal->reschedule_count ?? 0) + 1;
                $jadwal->last_rescheduled_at = now();
            }

            $jadwal->save();

            $title = $isReschedule ? 'Jadwal Asesmen Diperbarui' : 'Jadwal Asesmen Tersedia';
            $message = sprintf(
                'Jadwal asesmen untuk skema "%s" ditetapkan pada %s.',
                $pengajuan->program->nama,
                $jadwal->tanggal_mulai->translatedFormat('d F Y H:i')
            );

            NotificationService::send(
                $pengajuan->user,
                $title,
                $message,
                [
                    'type' => 'info',
                    'icon' => 'bi-calendar-check',
                    'link' => route('pengajuan.show', $pengajuan->id),
                    'data' => [
                        'pengajuan_id' => $pengajuan->id,
                        'jadwal_id' => $jadwal->id,
                    ],
                ]
            );
        });

        return back()->with('success', 'Jadwal asesmen berhasil disimpan.');
    }

    public function setStatus(Request $request, JadwalAsesmen $jadwalAsesmen)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:scheduled,completed,postponed,cancelled'],
        ]);

        $jadwalAsesmen->update([
            'status' => $validated['status'],
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'Status jadwal berhasil diperbarui.');
    }

    public function formData(PengajuanSkema $pengajuan)
    {
        $pengajuan->load(['jadwalAsesmen', 'user', 'program']);
        $listAsesor = User::query()->where('role', 'asesor')->orderBy('nama')->get();

        return view('admin.jadwal-asesmen.form', compact('pengajuan', 'listAsesor'));
    }
}
