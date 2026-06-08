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
        'payment_details' => 'array',
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

    // Generate Order ID unik
    public static function generateOrderId()
    {
        return 'LSP-' . date('YmdHis') . '-' . strtoupper(uniqid());
    }

    // Status label
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'Menunggu Pembayaran',
            'processing' => 'Sedang Diproses',
            'success' => 'Berhasil',
            'failed' => 'Gagal',
            'expired' => 'Kadaluarsa',
            'refunded' => 'Dikembalikan',
            default => $this->status,
        };
    }

    // Status badge color
    public function getStatusBadgeColorAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'processing' => 'info',
            'success' => 'success',
            'failed' => 'danger',
            'expired' => 'secondary',
            'refunded' => 'dark',
            default => 'secondary',
        };
    }

    // Format nominal
    public function getFormattedNominalAttribute()
    {
        return 'Rp ' . number_format($this->nominal, 0, ',', '.');
    }

    // Cek apakah bisa bayar
    public function canPay()
    {
        return in_array($this->status, ['pending', 'failed', 'expired']);
    }

}
