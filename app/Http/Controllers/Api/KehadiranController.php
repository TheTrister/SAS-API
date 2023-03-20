<?php

namespace App\Http\Controllers\Api;

use App\Models\Kehadiran;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ToJsonResource;
use App\Http\Resources\ToJsonResourceDouble;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\Keterangan_Izin;
use PhpParser\Node\Stmt\Else_;

class KehadiranController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        $kehadiran = Kehadiran::latest()->paginate(5);
        return new ToJsonResource(true, 'Data Kehadiran', $kehadiran);
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request)
    {
        // jika sakit / izin
        if ($request->STATUS == 'S' || $request->STATUS == 'I') {

            $validator = Validator::make($request->all(), [
                'KETERANGAN'     => 'required',
                'NIS'     => 'required',
                // 'WAKTU'     => 'required',
                'LOKASI'   => 'required',
                'STATUS'   => 'required',
                'WAKTU' => 'required',
                'JUMLAH_HARI' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            $cekAbsen = Kehadiran::where('NIS', $request->NIS)
                ->where('WAKTU', '>=', date('Y-m-d 00:00:00', strtotime($request->WAKTU)))
                ->where('WAKTU', '<=', date('Y-m-d 23:59:59', strtotime($request->WAKTU)))
                ->get();

            if (count($cekAbsen) > 0) {
                return new ToJsonResource(false, 'invalid data', $cekAbsen);
            }

            $no = 0;
            $jmlIzin = $request->JUMLAH_HARI;
            while ($no < $jmlIzin) {
                $date[$no] = date('Y-m-d H:i:s', strtotime('+' . $no . ' day', strtotime($request->WAKTU)));
                $tgl[$no] = date('Ymd', strtotime('+' . $no . ' day', strtotime($request->WAKTU)));
                $kode[$no] = $request->NIS . '.' . $tgl[$no];

                if (!(date('D', strtotime($date[$no])) == 'Sat' || date('D', strtotime($date[$no])) == 'Sun')) {
                    $kehadiran[$no] = Kehadiran::create([
                        'NIS'     => $request->NIS,
                        'WAKTU'     => $date[$no],
                        'LOKASI'   => $request->LOKASI,
                        'STATUS'   => $request->STATUS,
                        'ID_KETERANGAN'   => $kode[$no],
                    ]);

                    $izin[$no] = Keterangan_Izin::create([
                        'ID_KEHADIRAN' => $kode[$no],
                        'KETERANGAN' => $request->KETERANGAN,
                        'STATUS' => '0'
                    ]);
                } else {
                    $jmlIzin++;
                }

                $no++;
            }


            return new ToJsonResource(true, 'siap', $kehadiran[$no - 1]);
            //jika izin pulang
        } elseif ($request->STATUS == 'IP') {
            $validator = Validator::make($request->all(), [
                'NIS'     => 'required',
                'WAKTU'     => 'required',
                'LOKASI'   => 'required',
                'STATUS'   => 'required',
                'KETERANGAN'     => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            $cekAbsen = Kehadiran::where('NIS', $request->NIS)
            ->where('STATUS', 'P')
            ->where('WAKTU', '>=', date('Y-m-d 00:00:00', strtotime($request->WAKTU)))
            ->where('WAKTU', '<=', date('Y-m-d 23:59:59', strtotime($request->WAKTU)))
            ->orWhere('NIS', $request->NIS)
            ->where('STATUS', 'I')
            ->where('WAKTU', '>=', date('Y-m-d 00:00:00', strtotime($request->WAKTU)))
            ->where('WAKTU', '<=', date('Y-m-d 23:59:59', strtotime($request->WAKTU)))
            ->orWhere('NIS', $request->NIS)
            ->where('STATUS', 'S')
            ->where('WAKTU', '>=', date('Y-m-d 00:00:00', strtotime($request->WAKTU)))
            ->where('WAKTU', '<=', date('Y-m-d 23:59:59', strtotime($request->WAKTU)))
            ->orWhere('NIS', $request->NIS)
            ->where('STATUS', 'IP')
            ->where('WAKTU', '>=', date('Y-m-d 00:00:00', strtotime($request->WAKTU)))
            ->where('WAKTU', '<=', date('Y-m-d 23:59:59', strtotime($request->WAKTU)))                ->get();

            if (!(count($cekAbsen) == 0)) {
                return new ToJsonResource(false, 'invalid data', $cekAbsen);
            }

            $kehadiran = Kehadiran::create([
                'NIS'     => $request->NIS,
                'WAKTU'     => $request->WAKTU,
                'LOKASI'   => $request->LOKASI,
                'STATUS'   => $request->STATUS,
                'ID_KETERANGAN'   => $request->NIS . '.' . date('Ymd', strtotime($request->WAKTU)),
            ]);
            $izin = Keterangan_Izin::create([
                'ID_KEHADIRAN' => $request->NIS . '.' . date('Ymd'),
                'KETERANGAN' => $request->KETERANGAN,
                'STATUS' => '0'
            ]);
            return new ToJsonResource(true, $request->NIS . '.' . date('Ymd'), $kehadiran);
            // jika hadir
        } elseif ($request->STATUS == 'H') {
            $validator = Validator::make($request->all(), [
                'NIS'     => 'required',
                'WAKTU'     => 'required',
                'LOKASI'   => 'required',
                'STATUS'   => 'required',
                // 'KETERANGAN'     => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            $cekAbsen = Kehadiran::where('NIS', $request->NIS)
                ->where('WAKTU', '>=', date('Y-m-d 00:00:00', strtotime($request->WAKTU)))
                ->where('WAKTU', '<=', date('Y-m-d 23:59:59', strtotime($request->WAKTU)))
                ->get();

            if (!(count($cekAbsen) == 0)) {
                return new ToJsonResource(false, 'invalid data', $cekAbsen);
            }

            if (date('H', strtotime($request->WAKTU)) > 07) {
                $hadir = 'T';
            } else {
                $hadir = 'H';
            }

            $kehadiran = Kehadiran::create([
                'NIS'     => $request->NIS,
                'WAKTU'     => $request->WAKTU,
                'LOKASI'   => $request->LOKASI,
                'STATUS'   => $hadir,
                'ID_KETERANGAN'   => $request->NIS . '.' . date('Ymd'),
            ]);
            return new ToJsonResource(true, $request->NIS . '.' . date('Ymd'), $kehadiran);
        } elseif ($request->STATUS == 'P') {
            $cekAbsen = Kehadiran::where('NIS', $request->NIS)
                ->where('STATUS', 'P')
                ->where('WAKTU', '>=', date('Y-m-d 00:00:00', strtotime($request->WAKTU)))
                ->where('WAKTU', '<=', date('Y-m-d 23:59:59', strtotime($request->WAKTU)))
                ->orWhere('NIS', $request->NIS)
                ->where('STATUS', 'I')
                ->where('WAKTU', '>=', date('Y-m-d 00:00:00', strtotime($request->WAKTU)))
                ->where('WAKTU', '<=', date('Y-m-d 23:59:59', strtotime($request->WAKTU)))
                ->orWhere('NIS', $request->NIS)
                ->where('STATUS', 'S')
                ->where('WAKTU', '>=', date('Y-m-d 00:00:00', strtotime($request->WAKTU)))
                ->where('WAKTU', '<=', date('Y-m-d 23:59:59', strtotime($request->WAKTU)))
                ->orWhere('NIS', $request->NIS)
                ->where('STATUS', 'IP')
                ->where('WAKTU', '>=', date('Y-m-d 00:00:00', strtotime($request->WAKTU)))
                ->where('WAKTU', '<=', date('Y-m-d 23:59:59', strtotime($request->WAKTU)))
                ->get();

            if (!(count($cekAbsen) == 0)) {
                return new ToJsonResource(false, 'invalid data', $cekAbsen);
            }

            $kehadiran = Kehadiran::create([
                'NIS'     => $request->NIS,
                'WAKTU'     => $request->WAKTU,
                'LOKASI'   => $request->LOKASI,
                'STATUS'   => $request->STATUS,
                'ID_KETERANGAN'   => '',
            ]);
            return new ToJsonResource(true, $request->NIS . '.' . date('Ymd'), $kehadiran);
        }
    }
    public function show($nis)
    {
        $kehadiran = Kehadiran::where('NIS', $nis)
            ->where('WAKTU', '>=', date('Y-m-d 00:00:00'))
            ->where('WAKTU', '<=', date('Y-m-d 23:59:59'))
            ->get();

        if (count($kehadiran) == 0) {
            $sip = 'empty';
        } else {
            $sip = 'ada';
        }
        return new ToJsonResource(true, $sip, $kehadiran);
    }
    // public function update(Request $request, Kehadiran $kehadiran)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'NIS'     => 'required',
    //         'WAKTU'     => 'required',
    //         'LOKASI'   => 'required',
    //         'STATUS'   => 'required',
    //         'ID_KETERANGAN' => 'required'
    //     ]);


    //     if ($validator->fails()) {
    //         return response()->json($validator->errors(), 422);
    //     }
    //     $kehadiran->update([
    //         'NIS'     => $request->NIS,
    //         'WAKTU'     => $request->WAKTU,
    //         'LOKASI'   => $request->LOKASI,
    //         'STATUS'   => $request->STATUS,
    //         'ID_KETERANGAN'   => $request->ID_KETERANGAN,
    //     ]);
    //     return new ToJsonResource(true, 'Data Kehadiran Berhasil Diubah!', $kehadiran);
    // }

    // public function destroy(Kehadiran $kehadiran)
    // {
    //     $kehadiran->delete();

    //     return new ToJsonResource(true, 'Data Kehadiran Berhasil Dihapus!', null);
    // }
}
