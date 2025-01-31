<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodeMonitoring extends Model
{
    use HasFactory;

    // Menentukan nama tabel jika tidak sesuai dengan nama konvensi
    protected $table = 'periode_monitoring';

    // Menentukan primary key
    protected $primaryKey = 'pmo_id';
    public $incrementing = false;

    // Menghindari timestamps otomatis jika tidak diperlukan
    public $timestamps = true;

    // Menentukan fillable untuk mass assignment
    protected $fillable = [
        'pmo_id',
        'th_id',
        'pmo_tanggal_mulai',
        'pmo_tanggal_selesai',
    ];

    // Mendefinisikan relasi dengan tabel tahun_kerja

    public function rencanaKerjas()
{
    return $this->belongsToMany(RencanaKerja::class, 'rencana_kerja_pelaksanaan', 'pm_id', 'rk_id');
}
    public function tahunKerja()
    {
        return $this->belongsTo(tahun_kerja::class, 'th_id', 'th_id');
    }

    public function periodeMonev()
{
    return $this->belongsToMany(periode_monev::class, 'periode_monitoring_periode_monev', 'pmo_id', 'pm_id');
}


    

}