<?php

namespace App\Http\Controllers\Api;

use App\Models\Siswa;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ToJsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SiswaController extends Controller
{
    public function index()
    {
        $siswaOld = DB::table("siswas")
            ->join("jurusans", "siswas.ID_JURUSAN", "=", "jurusans.ID")
            ->join("kelas", "siswas.ID_KELAS", "=", "kelas.ID")
            ->where('NIS', "21237")
            // ->get();
            ->first();
        // dd($siswaOld[0]->NAMA);
        dd($siswaOld->NAMA);
        return new ToJsonResource(true, 'Selamat Datang ', $siswaOld);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'NIS' => 'required',
            'IMEI' => 'required',
            'PASSWORD' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $siswaOld = Siswa::where('NIS', $request->NIS)
            ->where('PASSWORD', $request->PASSWORD)
            ->where('IMEI', $request->IMEI)
            ->get();

        $siswaNew = Siswa::where('NIS', $request->NIS)
            ->where('PASSWORD', $request->PASSWORD)
            ->where('IMEI', '')
            ->get();

        $siswaFalsePass = Siswa::where('NIS', $request->NIS)->get();

        if (count($siswaOld) == 1) {
            return new ToJsonResource(true, 'Selamat Datang ' . $siswaOld->NAMA, $siswaOld);
        } elseif (count($siswaNew) == 1) {
            Siswa::where('NIS', $request->NIS)->update([
                'IMEI' => $request->IMEI
            ]);
            return new ToJsonResource(true, 'Berhasil Ditautkan Perangkat', $siswaNew);
        } elseif (count($siswaFalsePass) == 1) {
            return new ToJsonResource(false, 'Perangkat tidak sesuai', $siswaFalsePass);
        } else {
            return new ToJsonResource(false, 'Username tidak ditemukan', $siswaOld);
        }
    }
    public function show($post)
    {
        $siswa = DB::table("siswas")
            ->join("jurusans", "siswas.ID_JURUSAN", "=", "jurusans.ID")
            ->join("kelas", "siswas.ID_KELAS", "=", "kelas.ID")
            ->where('NIS', $post)
            ->get();
        if (!(count($siswa) == 1)) {
            if (count($siswa) == 0) {
                $message = "Tidak ada data";
            } else {
                $message = "Data lebih dari satu";
            }
            $respon = false;
        } else {
            $message = "Data Siswa NIS = " . $post;
            $respon = true;
        }
        return new ToJsonResource($respon, $message, $siswa);
        // return new ToJsonResource(true, 'Data Siswa Ditemukan', $siswa);
    }
}
