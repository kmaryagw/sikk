<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fakultasn extends Model
{
    use HasFactory;
    protected $table = 'fakultasn';
    protected $primaryKey = 'id_fakultas';
    public $incrementing = false;
    protected $keyType = 'string'; 

    
     
    protected $fillable = [
        'id_fakultas',
        'nama_fakultas',
    ];
}
