<?php

namespace App\Services;

use App\Models\Pembayaran;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Notification;
use Midtrans\Snap;
use Midtrans\Transaction;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function createSnapToken(Pembayaran $pembayaran, User $user): string
    {
        $params = [
            'transaction_details' => [
                'order_id' => $pembayaran->order_id,
                'gross_amount' => (int) $pembayaran->nominal,
            ],
            'customer_details' => [
                'first_name' => $user->nama,
                'email' => $user->email,
                'phone' => $user->no_hp ?? '',
            ],
            'item_details' => [[
                'id' => 'SKEMA-'.$pembayaran->pengajuan_skema_id,
                'price' => (int) $pembayaran->nominal,
                'quantity' => 1,
                'name' => 'Sertifikasi: '.($pembayaran->pengajuan->program->nama ?? 'Skema Sertifikasi'),
            ]],
            'callbacks' => [
                'finish' => route('pembayaran.finish', $pembayaran->id),
            ],
            'expiry' => [
                'start_time' => Carbon::now()->format('Y-m-d H:i:s O'),
                'unit' => 'days',
                'duration' => 1,
            ],
        ];

        $enabledPayments = config('midtrans.enabled_payments');
        if (! empty($enabledPayments)) {
            $params['enabled_payments'] = $this->parseEnabledPayments($enabledPayments);
        }

        try {
            $snapToken = Snap::getSnapToken($params);

            $pembayaran->update([
                'snap_token' => $snapToken,
                'expired_at' => now()->addDay(),
            ]);

            return $snapToken;
        } catch (Exception $e) {
            Log::error('Midtrans Snap Error', ['error' => $e->getMessage()]);
            throw new Exception('Gagal membuat transaksi Midtrans');
        }
    }

    private function parseEnabledPayments($enabledPayments): array
    {
        if (is_array($enabledPayments)) {
            return $enabledPayments;
        }

        return array_values(array_filter(array_map('trim', explode(',', $enabledPayments))));
    }

    public function handleNotification(): array
    {
        $notification = new Notification();

        $orderId = (string) $notification->order_id;
        $statusCode = (string) $notification->status_code;
        $grossAmount = (string) $notification->gross_amount;
        $signatureKey = (string) $notification->signature_key;

        $expectedSignature = hash('sha512', $orderId.$statusCode.$grossAmount.config('midtrans.server_key'));

        if (! hash_equals($expectedSignature, $signatureKey)) {
            Log::warning('Midtrans signature invalid', compact('orderId'));
            abort(403, 'Invalid signature');
        }

        $pembayaran = Pembayaran::where('order_id', $orderId)->first();

        if (! $pembayaran) {
            return ['success' => false, 'message' => 'Pembayaran tidak ditemukan'];
        }

        $transactionStatus = (string) ($notification->transaction_status ?? '');
        $details = json_decode(json_encode($notification), true) ?: [];

        DB::transaction(function () use ($pembayaran, $notification, $grossAmount, $transactionStatus, $details): void {
            $updates = [
                'payment_type' => $notification->payment_type ?? null,
                'transaction_id' => $notification->transaction_id ?? null,
                'transaction_status' => $transactionStatus ?: null,
                'transaction_time' => $notification->transaction_time ?? null,
                'gross_amount' => $grossAmount ?: null,
                'payment_details' => $details,
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

            if (($updates['status'] ?? null) === 'success' && $pembayaran->pengajuan->status !== 'paid') {
                $pembayaran->pengajuan->update(['status' => 'paid']);
            }
        });

        return [
            'success' => true,
            'order_id' => $orderId,
            'status' => $transactionStatus,
        ];
    }

    public function checkStatus(string $orderId): array
    {
        return (array) Transaction::status($orderId);
    }

    public function cancel(string $orderId): array
    {
        return (array) Transaction::cancel($orderId);
    }
}
