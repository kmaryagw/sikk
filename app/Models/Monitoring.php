<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Monitoring extends Model
{
    use HasFactory;
    protected $table = 'monitoring';

    // Tentukan primary key
    protected $primaryKey = 'mtg_ig';

    // Tentukan kolom-kolom yang dapat diisi (fillable)
    protected $fillable = [
        'pm_id', 
        'mtg_capaian', 
        'mtg_kondisi', 
        'mtg_kendala', 
        'mtg_tindak_lanjut', 
        'mtg_tindak_lanjut_tanggal', 
        'mtg_bukti', 
        'rk_id'
    ];

    // Tentukan relasi dengan model lain (jika ada)
    public function periode_monev()
    {
        return $this->belongsTo(periode_monev::class, 'pm_id', 'pm_id');
    }

    public function RencanaKerja()
    {
        return $this->belongsTo(RencanaKerja::class, 'rk_id', 'rk_id');
    }
}
