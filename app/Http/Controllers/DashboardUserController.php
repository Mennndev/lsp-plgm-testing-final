<?php

namespace App\Http\Controllers;

use App\Models\JadwalAsesmen;
use App\Models\PengajuanSkema;
use App\Models\Sertifikat;
use Illuminate\Support\Facades\Auth;

class DashboardUserController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $asesmenList = JadwalAsesmen::query()
            ->with(['pengajuan.program', 'asesor'])
            ->whereHas('pengajuan', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->orderBy('tanggal_mulai')
            ->get()
            ->map(function ($jadwal) {
                return (object) [
                    'pengajuan_id' => $jadwal->pengajuan_skema_id,
                    'kode' => 'ASM-' . str_pad((string) $jadwal->id, 5, '0', STR_PAD_LEFT),
                    'skema_nama' => $jadwal->pengajuan->program->nama ?? '-',
                    'tuk_nama' => $jadwal->mode_asesmen === 'online' ? 'Online Meeting' : 'TUK',
                    'tuk_alamat' => $jadwal->lokasi ?? '-',
                    'tanggal_asesmen' => optional($jadwal->tanggal_mulai)->format('d/m/Y H:i'),
                    'link_meeting' => $jadwal->tautan_meeting,
                    'asesor_nama' => $jadwal->asesor->nama ?? $jadwal->asesor->name ?? '-',
                    'jenis_bukti' => '-',
                    'rekomendasi' => '-',
                    'status' => $jadwal->status_label,
                    'status_asesmen' => $jadwal->status_label,
                ];
            });

        // Fetch user's pengajuan skema
        $pengajuanList = PengajuanSkema::with('program')
            ->where('user_id', $user->id)
            ->latest('tanggal_pengajuan')
            ->get();

       $riwayatList = Sertifikat::with(['pengajuan.program'])
    ->where('user_id', $user->id)
    ->latest('tanggal_terbit')
    ->get()
    ->map(function ($sertifikat) {
        return (object) [
            'nama_skema' => $sertifikat->pengajuan->program->nama ?? '-',
            'jenis_bukti' => $sertifikat->jenis_bukti ?? 'Sertifikat Kompetensi',
            'nomor_sertifikat' => $sertifikat->nomor_sertifikat ?? '-',
            'tanggal_berlaku' => $sertifikat->tanggal_berlaku_sampai
                ? $sertifikat->tanggal_berlaku_sampai->format('d/m/Y')
                : '-',
            'status' => $sertifikat->status_sertifikat,
            'file_sertifikat' => $sertifikat->file_sertifikat,
        ];
    }); // data untuk tabel "Riwayat Asesmen"

        // Hitung notifikasi yang belum dibaca
        $notificationCount = $user->unreadNotificationCount();

        // Ambil 5 notifikasi terbaru untuk dropdown
        $latestNotifications = $user->notifications()->take(5)->get();

        return view('dashboard.user', compact(
            'user',
            'asesmenList',
            'pengajuanList',
            'riwayatList',
            'notificationCount',
            'latestNotifications'
        ));
    }

}
