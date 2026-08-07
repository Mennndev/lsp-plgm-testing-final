<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\PengajuanSkema;
use App\Services\MidtransService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PembayaranController extends Controller
{
    protected MidtransService $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    public function show($pengajuanId)
    {
        $pengajuan = PengajuanSkema::with(['program', 'pembayaran'])
            ->where('user_id', Auth::id())
            ->findOrFail($pengajuanId);

        if (! in_array($pengajuan->status, ['approved', 'paid'], true)) {
            return redirect()->route('pengajuan.show', $pengajuanId)
                ->with('error', 'Pengajuan belum disetujui.');
        }

        $pembayaran = $pengajuan->pembayaran;

        if ($pembayaran && $pengajuan->program && $pembayaran->nominal != $pengajuan->program->estimasi_biaya) {
            $pembayaran->update(['nominal' => $pengajuan->program->estimasi_biaya]);
        }

        if ($pembayaran && $pembayaran->status === 'success') {
            return redirect()->route('pengajuan.show', $pengajuanId)
                ->with('success', 'Pembayaran sudah berhasil.');
        }

        return view('pembayaran.show', [
            'pengajuan' => $pengajuan,
            'pembayaran' => $pembayaran,
            'clientKey' => config('midtrans.client_key'),
        ]);
    }

    public function process($pengajuanId)
    {
        $pengajuan = PengajuanSkema::with(['program', 'pembayaran'])
            ->where('user_id', Auth::id())
            ->findOrFail($pengajuanId);

        if ($pengajuan->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan belum disetujui.',
            ], 400);
        }

        $pembayaran = $pengajuan->pembayaran;

        if ($pembayaran && $pembayaran->status === 'success') {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran sudah berhasil.',
            ], 409);
        }

        if ($pembayaran && $pembayaran->snap_token && $pembayaran->canPay()) {
            if ($pembayaran->expired_at && $pembayaran->expired_at->isFuture()) {
                return response()->json([
                    'success' => true,
                    'snap_token' => $pembayaran->snap_token,
                ]);
            }

            $pembayaran->update([
                'order_id' => Pembayaran::generateOrderId(),
                'status' => 'pending',
                'snap_token' => null,
            ]);
        }

        if (! $pembayaran) {
            $pembayaran = Pembayaran::create([
                'pengajuan_skema_id' => $pengajuan->id,
                'user_id' => Auth::id(),
                'order_id' => Pembayaran::generateOrderId(),
                'nominal' => $pengajuan->program->estimasi_biaya ?? 500000,
                'status' => 'pending',
            ]);
        }

        try {
            $snapToken = $this->midtransService->createSnapToken($pembayaran, Auth::user());

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function finish(Request $request, $id)
    {
        $pembayaran = Pembayaran::where('user_id', Auth::id())->findOrFail($id);

        return redirect()->route('pengajuan.show', $pembayaran->pengajuan_skema_id)
            ->with('info', 'Pembayaran sedang diproses. Status akan diperbarui otomatis.');
    }

    public function notification(Request $request)
    {
        try {
            return response()->json($this->midtransService->handleNotification());
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function checkStatus($pengajuanId)
    {
        $pengajuan = PengajuanSkema::with('pembayaran')
            ->where('user_id', Auth::id())
            ->findOrFail($pengajuanId);

        $pembayaran = $pengajuan->pembayaran;

        if (! $pembayaran) {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran tidak ditemukan.',
            ], 404);
        }

        if (in_array($pembayaran->status, ['pending', 'processing'], true) && $pembayaran->order_id) {
            try {
                $status = $this->midtransService->checkStatus($pembayaran->order_id);
                $transactionStatus = $status['transaction_status'] ?? null;

                if ($transactionStatus) {
                    $updates = [
                        'payment_type' => $status['payment_type'] ?? $pembayaran->payment_type,
                        'transaction_id' => $status['transaction_id'] ?? $pembayaran->transaction_id,
                        'transaction_status' => $transactionStatus,
                    ];

                    if (in_array($transactionStatus, ['capture', 'settlement'], true)) {
                        $updates['status'] = 'success';
                        $updates['paid_at'] = now();
                    } elseif ($transactionStatus === 'pending') {
                        $updates['status'] = 'processing';
                    } elseif (in_array($transactionStatus, ['deny', 'cancel'], true)) {
                        $updates['status'] = 'failed';
                    } elseif ($transactionStatus === 'expire') {
                        $updates['status'] = 'expired';
                    } elseif ($transactionStatus === 'refund') {
                        $updates['status'] = 'refunded';
                    }

                    $pembayaran->update($updates);

                    if (($updates['status'] ?? null) === 'success' && $pengajuan->status !== 'paid') {
                        $pengajuan->update(['status' => 'paid']);
                    }
                }

                return response()->json([
                    'success' => true,
                    'status' => $pembayaran->fresh()->status,
                    'status_label' => $pembayaran->fresh()->status_label,
                ]);
            } catch (Exception $e) {
                report($e);
            }
        }

        return response()->json([
            'success' => true,
            'status' => $pembayaran->status,
            'status_label' => $pembayaran->status_label,
        ]);
    }

    public function reset($pengajuanId)
    {
        $pengajuan = PengajuanSkema::with(['program', 'pembayaran'])
            ->where('user_id', Auth::id())
            ->findOrFail($pengajuanId);

        if ($pengajuan->status !== 'approved') {
            return back()->with('error', 'Pengajuan belum disetujui.');
        }

        $pembayaran = $pengajuan->pembayaran;

        if ($pembayaran && $pembayaran->status === 'success') {
            return back()->with('error', 'Pembayaran yang sudah berhasil tidak dapat direset.');
        }

        if ($pembayaran) {
            $pembayaran->update([
                'order_id' => Pembayaran::generateOrderId(),
                'status' => 'pending',
                'payment_type' => null,
                'transaction_id' => null,
                'transaction_status' => null,
                'snap_token' => null,
                'pdf_url' => null,
                'payment_details' => null,
                'paid_at' => null,
                'expired_at' => null,
            ]);
        }

        return back()->with('success', 'Pembayaran telah direset. Silakan lakukan pembayaran ulang.');
    }
}
