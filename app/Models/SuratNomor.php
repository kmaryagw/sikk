<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratNomor extends Model
{
    use HasFactory;

    protected $table = 'surat_nomor';
    protected $primaryKey = 'sn_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'sn_id', 
        'unit_id', 
        'skl_id', 
        'oj_id', 
        'sn_nomor', 
        'sn_tanggal', 
        'sn_perihal', 
        'sn_keterangan', 
        'sn_status'
    ];

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class, 'unit_id', 'unit_id');
    }

    public function lingkup()
    {
        return $this->belongsTo(SuratKlasifikasiLingkup::class, 'skl_id', 'skl_id');
    }

    public function organisasiJabatan()
    {
        return $this->belongsTo(OrganisasiJabatan::class, 'oj_id', 'oj_id');
    }
}