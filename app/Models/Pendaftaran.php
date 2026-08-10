<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pendaftaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'kota',
        'provinsi',
        'pendidikan',
        'pekerjaan',
        'instansi',
        'jabatan',
        'email_instansi',
        'alamat_instansi',
        'telepon_instansi',
        'skema',
        'jadwal',
        'no_ktp',
        'ktp_path',
        'ttd_path',
        'setuju',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'setuju' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function program()
    {
        return $this->belongsTo(
            ProgramPelatihan::class,
            'skema',
            'kode_skema'
        );
    }
}
