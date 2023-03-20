<?php

namespace App\Http\Controllers\Api;

use App\Models\Kehadiran;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ToJsonResource;
use App\Http\Resources\ToJsonResourceDouble;
use Illuminate\Support\Facades\Validator;

class HistoriController extends Controller
{
    public function index()
    {
        $kehadiran = Kehadiran::latest()->paginate(5);
        return new ToJsonResource(true, 'Data Kehadiran', $kehadiran);
    }
    public function show($nis)
    {
        $kehadiran = Kehadiran::where('NIS', $nis)->latest()->paginate(100);
        return new ToJsonResource(true, 'Histori Kehadiran nis : ' . $nis, $kehadiran);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'NIS'     => 'required',
            'STATUS'    => 'required',
            'BULAN' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $nis = $request->NIS;
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

        // jika hadir dan pulang 
        if ($request->STATUS == '1') {
            $kehadiran = Kehadiran::where('NIS', $request->NIS)
                ->where('STATUS', 'H')
                ->where('WAKTU', '>=', $waktuAwal)
                ->where('WAKTU', '<=', $waktuAkhir)
                ->orWhere('NIS', $request->NIS)
                ->where('STATUS', 'P')
                ->where('WAKTU', '>=', $waktuAwal)
                ->where('WAKTU', '<=', $waktuAkhir)
                ->where('STATUS', 'IP')
                ->where('WAKTU', '>=', $waktuAwal)
                ->where('WAKTU', '<=', $waktuAkhir)
                ->get();
            return new ToJsonResource(true, $tes, $kehadiran);
            // jika sakit/ izin
        } elseif ($request->STATUS == '2') {
            $kehadiran = Kehadiran::where('NIS', $request->NIS)
                ->where('STATUS', 'I')
                ->where('WAKTU', '>=', $waktuAwal)
                ->where('WAKTU', '<=', $waktuAkhir)
                ->orWhere('NIS', $request->NIS)
                ->where('STATUS', 'S')
                ->where('WAKTU', '>=', $waktuAwal)
                ->where('WAKTU', '<=', $waktuAkhir)
                ->get();
            return new ToJsonResource(true, $tes, $kehadiran);
        } elseif ($request->STATUS == 0) {
            $kehadiran = Kehadiran::where('NIS', $request->NIS)
                ->where('WAKTU', '>=', $waktuAwal)
                ->where('WAKTU', '<=', $waktuAkhir)
                ->get();
            return new ToJsonResource(true, $tes, $kehadiran);
        }
    }
}
