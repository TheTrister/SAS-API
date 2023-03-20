<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ToJsonResource;
use App\Models\Keterangan_Izin;
use Illuminate\Support\Facades\Validator;

class IzinController extends Controller
{
    public function index()
    {
        $izin = Keterangan_Izin::latest()->paginate(1000);
        return new ToJsonResource(true, 'Data Izin Siswa', $izin);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ID_KEHADIRAN' => 'required',
            'KETERANGAN'  => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $izin = Keterangan_Izin::create([
            'ID_KEHADIRAN' => $request->ID_KEHADIRAN,
            'KETERANGAN' => $request->KETERANGAN,
        ]);
        return new ToJsonResource(true, 'Izin telah ditambahlkan', $izin);
    }
}
