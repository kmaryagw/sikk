<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitKerja extends Model
{
    use HasFactory;

    protected $table = 'unit_kerja';
    protected $primaryKey = 'unit_id';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'unit_id',
        'unit_nama',
        'unit_kerja',
    ];

    public function monitoringikuDetail()
    {
        return $this->hasMany(MonitoringIKU_Detail::class, 'unit_id', 'unit_id');
    }

    public function rencanaKerja()
    {
        return $this->hasMany(RencanaKerja::class, 'unit_id', 'unit_id');
    }
    
    // public function indikatorKinerja()
    // {
    //     return $this->hasMany(IndikatorKinerja::class, 'unit_id', 'unit_id');
    // }

    public function indikatorKinerja()
    {
        return $this->belongsToMany(IndikatorKinerja::class, 'indikatorkinerja_unitkerja', 'unit_id', 'ik_id')
                    ->withPivot('ik_id', 'unit_id'); // Jika Anda perlu mengambil data pivot
    }
}
