<?php

namespace App\Http\Controllers\Api;

use App\Models\Kehadiran;
use App\Models\Siswa;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ToJsonResource;
use App\Http\Resources\ToJsonResourceDouble;
use Illuminate\Support\Facades\Validator;


class JumlahHistoriController extends Controller
{
    public function show($imei)
    {
        $kehadiran = Siswa::where('IMEI', $imei)->get();
        return new ToJsonResource(true, 'Akun perangkat : ' . $imei, $kehadiran);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'NIS'     => 'required',
            'BULAN' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $tahun = date('Y');
        if ($request->BULAN == 0) {
            $bulan = intval(date('m'));
        } else {
            $bulan = $request->BULAN;
        }
        $tglA = date('d', strtotime($bulan));
        $tglB = date('t', strtotime($tahun . '-' . $bulan));


        // $waktuAwal = date('Y-m-1', strtotime($bulan));
        // $waktuAkhir = date('Y-m-t', strtotime($bulan));
        $waktuAwal = $tahun . '-' . $bulan . '-1';
        $waktuAkhir = $tahun . '-' . $bulan . '-' . $tglB;
        $tes = $tahun . '-' . $bulan . '-' . $tglB;

        $izin = Kehadiran::where('NIS', $request->NIS)
            ->where('STATUS', 'I')
            ->where('WAKTU', '>=', $waktuAwal)
            ->where('WAKTU', '<=', $waktuAkhir)
            ->orWhere('NIS', $request->NIS)
            ->where('STATUS', 'S')
            ->where('WAKTU', '>=', $waktuAwal)
            ->where('WAKTU', '<=', $waktuAkhir)
            ->get();
        $hadir = Kehadiran::where('NIS', $request->NIS)
            ->where('STATUS', 'H')
            ->where('WAKTU', '>=', $waktuAwal)
            ->where('WAKTU', '<=', $waktuAkhir)
            ->orWhere('NIS', $request->NIS)
            ->where('STATUS', 'P')
            ->where('WAKTU', '>=', $waktuAwal)
            ->where('WAKTU', '<=', $waktuAkhir)
            ->orWhere('NIS', $request->NIS)
            ->where('STATUS', 'IP')
            ->where('WAKTU', '>=', $waktuAwal)
            ->where('WAKTU', '<=', $waktuAkhir)
            ->get();

        $data[] = array(
            'jmlHadir' => count($hadir),
            'jmlIzin' => count($izin),
        );
        return new ToJsonResource(true, $tes, $data);
        
    }
}