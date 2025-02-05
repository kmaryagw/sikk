<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratKlasifikasiLingkup extends Model
{
    use HasFactory;

    protected $table = 'surat_klasifikasi_lingkup';
    protected $primaryKey = 'skl_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'skl_id', 
        'skl_nama', 
        'skl_kode', 
        'skl_aktif', 
        'skp_id'
    ];

    public function perihal()
    {
        return $this->belongsTo(SuratKlasifikasiPerihal::class, 'skp_id', 'skp_id');
    }

    public function suratNomor()
    {
        return $this->hasMany(SuratNomor::class, 'skl_id', 'skl_id');
    }
}