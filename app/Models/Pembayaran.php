<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';

    protected $fillable = [
        'pengajuan_skema_id',
        'user_id',
        'verifier_id',
        'order_id',
        'nominal',
        'metode_pembayaran',
        'payment_type',
        'transaction_id',
        'transaction_status',
        'transaction_time',
        'gross_amount',
        'snap_token',
        'pdf_url',
        'payment_details',
        'status',
        'paid_at',
        'expired_at',
        'catatan',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'gross_amount' => 'decimal:2',
        'payment_details' => 'array',
        'transaction_time' => 'datetime',
        'paid_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    public function pengajuan()
    {
        return $this->belongsTo(PengajuanSkema::class, 'pengajuan_skema_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verifier_id');
    }

    public static function generateOrderId(): string
    {
        return 'LSP-'.now()->format('YmdHis').'-'.strtoupper(uniqid());
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Pembayaran',
            'processing' => 'Sedang Diproses',
            'success' => 'Berhasil',
            'failed' => 'Gagal',
            'expired' => 'Kadaluarsa',
            'refunded' => 'Dikembalikan',
            default => ucfirst((string) $this->status),
        };
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'processing' => 'info',
            'success' => 'success',
            'failed' => 'danger',
            'expired' => 'secondary',
            'refunded' => 'dark',
            default => 'secondary',
        };
    }

    public function getFormattedNominalAttribute(): string
    {
        return 'Rp '.number_format((float) $this->nominal, 0, ',', '.');
    }

    public function canPay(): bool
    {
        return in_array($this->status, ['pending', 'failed', 'expired'], true);
    }
}
