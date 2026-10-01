<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanAsesorUnitAssessment extends Model
{
    protected $table = 'pengajuan_asesor_unit_assessments';

    protected $fillable = [
        'pengajuan_skema_id',
        'unit_kompetensi_id',
        'asesor_id',
        'nilai',
        'catatan',
    ];

    public function pengajuan()
    {
        return $this->belongsTo(PengajuanSkema::class, 'pengajuan_skema_id');
    }

    public function unitKompetensi()
    {
        return $this->belongsTo(UnitKompetensi::class, 'unit_kompetensi_id');
    }

    public function asesor()
    {
        return $this->belongsTo(User::class, 'asesor_id');
    }
}
