<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RealisasiRenja extends Model
{
    use HasFactory;

    protected $table = 'rencana_kerja_realisasi';

    // Menentukan primary key
    protected $primaryKey = 'rkr_id';

    protected $keyType = 'string';

    protected $fillable = [
        'rkr_id',
        'rk_id',
        'pm_id',
        'rkr_url',
        'rkr_file',
        'rkr_deskripsi',
        'rkr_capaian',
        'rkr_tanggal'
    ];

    protected $dates = ['rkr_tanggal'];

    public $timestamps = true;

    public function rencanaKerja()
    {
        return $this->belongsTo(RencanaKerja::class, 'rk_id', 'rk_id');
    }

    public function periodeMonev()
    {
        return $this->belongsTo(periode_monev::class, 'pm_id','pm_id');
    }
}
