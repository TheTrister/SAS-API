<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keterangan_Izin extends Model
{
    use HasFactory;
    protected $fillable = [
        'ID_KEHADIRAN',
        'KETERANGAN',
        'STATUS'
    ];
}
