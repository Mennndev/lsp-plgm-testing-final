<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sertifikat extends Model
{
    protected $table = 'sertifikat';

    protected $fillable = [
        'pengajuan_skema_id',
        'user_id',
        'nomor_sertifikat',
        'jenis_bukti',
        'tanggal_terbit',
        'tanggal_berlaku_sampai',
        'file_sertifikat',
        'status',
    ];

    protected $casts = [
        'tanggal_terbit' => 'date',
        'tanggal_berlaku_sampai' => 'date',
    ];

    public function pengajuan()
    {
        return $this->belongsTo(PengajuanSkema::class, 'pengajuan_skema_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getStatusSertifikatAttribute()
    {
        if ($this->tanggal_berlaku_sampai && $this->tanggal_berlaku_sampai->isPast()) {
            return 'Expired';
        }

        return ucfirst($this->status);
    }
}
