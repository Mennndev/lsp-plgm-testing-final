<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanApl01 extends Model
{
    protected $table = 'pengajuan_apl01';

    protected $fillable = [
        'pengajuan_skema_id',
        'nama_lengkap',
        'nik',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'kebangsaan',
        'alamat_rumah',
        'kode_pos',
        'telepon_rumah',
        'telepon_kantor',
        'hp',
        'email',
        'kualifikasi_pendidikan',
        'pekerjaan',
        'nama_institusi',
        'jabatan',
        'alamat_kantor',
        'fax',
        'email_kantor',
        'nama_sertifikat',
        'nomor_sertifikat',
        'tujuan_asesmen',
        'bukti_penyertaan_dasar',
        'bukti_administrasif',
        'catatan',
        'ttd',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tujuan_asesmen' => 'array',
    ];

    public function pengajuanSkema(): BelongsTo
    {
        return $this->belongsTo(PengajuanSkema::class);
    }
}
