<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class program_studi extends Model
{
    use HasFactory;
    protected $table = 'program_studi';
    protected $primaryKey = 'prodi_id';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'prodi_id',
        'nama_prodi',
        'id_falkutas',
    ];

    public function Falkutasn()
    {
        return $this->belongsTo(Falkutasn::class, 'id_falkutas', 'id_falkutas');
    }
}
