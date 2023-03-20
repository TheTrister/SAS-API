<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;
    protected $fillable = [
        'NIS',
        'NAMA',
        'ID_JURUSAN',
        'ID_KELAS',
        'NO_HP',
        'IMEI',
        'PASSWORD',
        'EMAIL'
    ];
}
