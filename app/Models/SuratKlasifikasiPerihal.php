<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratKlasifikasiPerihal extends Model
{
    use HasFactory;

    protected $table = 'surat_klasifikasi_perihal';
    protected $primaryKey = 'skp_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'skp_id', 
        'skp_nama', 
        'skp_aktif', 
        'skf_id'
    ];

    public function fungsi()
    {
        return $this->belongsTo(SuratKlasifikasiFungsi::class, 'skf_id', 'skf_id');
    }

    public function lingkup()
    {
        return $this->hasMany(SuratKlasifikasiLingkup::class, 'skp_id', 'skp_id');
    }
}