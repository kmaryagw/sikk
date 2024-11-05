<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RencanaKerja extends Model
{
    use HasFactory;

    protected $table = 'rencana_kerja';
    protected $primaryKey = 'rk_id';
    public $incrementing = false;
    protected $fillable = [
        'rk_id',
        'rk_nama',
        'th_id',
        'unit_id',
        // 'pm_id' seharusnya dihilangkan dari fillable karena ini untuk relasi many-to-many
    ];

    public function tahunKerja()
    {
        return $this->belongsTo(tahun_kerja::class, 'th_id', 'th_id');
    }

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class, 'unit_id', 'unit_id');
    }

    public function periodes()
    {
        return $this->belongsToMany(periode_monev::class, 'rencana_kerja_pelaksanaan', 'rk_id', 'pm_id')->orderBy('pm_nama');
    }

    // Hapus relasi periode_monev jika tidak digunakan dalam konteks ini
    // Jika Anda hanya ingin mengakses satu periode monev, pastikan itu sesuai dengan logika Anda
    public function periodeMonev()
    {
        return $this->belongsTo(periode_monev::class, 'pm_id', 'pm_id');
    }

    public function realisasi()
    {
        return $this->hasMany(RealisasiRenja::class, 'rk_id'); // Pastikan foreign key yang benar
    }

    public function periodeMonitoring()
    {
        return $this->hasOne(PeriodeMonitoring::class, 'rk_id', 'rk_id');
    }

    public $timestamps = true;
}