<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratKlasifikasiFungsi extends Model
{
    use HasFactory;

    protected $table = 'surat_klasifikasi_fungsi';
    protected $primaryKey = 'skf_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'skf_id', 
        'skf_nama', 
        'skf_aktif'
    ];

    public function perihal()
    {
        return $this->hasMany(SuratKlasifikasiPerihal::class, 'skf_id', 'skf_id');
    }
}
