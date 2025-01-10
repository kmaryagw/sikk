<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Falkutasn extends Model
{
    use HasFactory;
    protected $table = 'falkutasn';
    protected $primaryKey = 'id_falkutas';
    public $incrementing = false;
    protected $keyType = 'string'; 

    
     
    protected $fillable = [
        'id_falkutas',
        'nama_falkutas',
    ];
    public function programStudis()
    {
        return $this->hasMany(program_studi::class, 'id_falkutas');
    }

}
