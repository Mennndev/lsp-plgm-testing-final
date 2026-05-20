<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PengajuanSkema extends Model
{
    protected $table = 'pengajuan_skema';

    protected $fillable = [
        'user_id',
        'program_pelatihan_id',
        'status',
        'tanggal_pengajuan',
        'tanggal_disetujui',
        'catatan_admin',
        'approved_by',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'datetime',
        'tanggal_disetujui' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(ProgramPelatihan::class, 'program_pelatihan_id');
    }

    public function apl01(): HasOne
    {
        return $this->hasOne(PengajuanApl01::class);
    }

    public function apl02(): HasMany
    {
        return $this->hasMany(PengajuanApl02::class);
    }

    public function dokumen(): HasMany
    {
        return $this->hasMany(PengajuanDokumen::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    // Accessor untuk status badge color
    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'draft' => 'secondary',
            default => 'secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Menunggu',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'draft' => 'Draft',
            default => ucfirst($this->status),
        };
    }
    // Di app/Models/PengajuanSkema.php

    public function portfolio()
    {
        return $this->hasMany(PengajuanPortofolio::class);
    }

    public function pengajuanPersyaratanDasar()
    {
        return $this->hasMany(PengajuanPersyaratanDasar::class);
    }

    public function pengajuanBuktiAdministratif()
    {
        return $this->hasMany(PengajuanBuktiAdministratif::class);
    }

    public function pengajuanBuktiPortofolio()
    {
        return $this->hasMany(PengajuanBuktiPortofolio::class);
    }

   public function buktiKompetensi() // Nama disederhanakan
    {
        return $this->hasMany(PengajuanBuktiKompetensi::class);
    }

    public function pembayaran(): HasOne
    {
        return $this->hasOne(Pembayaran::class, 'pengajuan_skema_id');
    }

    public function jadwalAsesmen(): HasOne
    {
        return $this->hasOne(JadwalAsesmen::class, 'pengajuan_skema_id');
    }

    public function selfAssessments()
{
    return $this->hasMany(PengajuanSelfAssessment::class);
}

public function asesorAssessments()
{
    return $this->hasMany(PengajuanAsesorAssessment::class);
}

public function asesors()
{
    return $this->belongsToMany(
        User::class,
        'pengajuan_asesor',
        'pengajuan_skema_id',
        'asesor_id'
    );
}

public function formulirAsesmen()
{
    return $this->hasMany(FormulirAsesmen::class, 'pengajuan_skema_id');
}

public function sertifikat(): HasOne
{
    return $this->hasOne(Sertifikat::class, 'pengajuan_skema_id');
}


}
