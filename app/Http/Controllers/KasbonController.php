<?php

namespace App\Http\Controllers;

use App\Models\AksesCabang;
use App\Models\Cabang;
use App\Models\Karyawan;
use App\Models\Kasbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KasbonController extends Controller
{
    public function index(Request $request)
    {

        $data_user = AksesCabang::where('user_id', Auth::id())->get();
        $dt_akses = [];
        foreach ($data_user as $da) {

            $dt_akses[] = $da->cabang_id;
        }

        if ($request->query('tgl1')) {
            $tgl1 = $request->query('tgl1');
            $tgl2 = $request->query('tgl2');
            $cabang_id = $request->query('cabang_id');
        } else {
            $tgl1 = date('Y-m-01');
            $tgl2 = date('Y-m-t');

            if (empty($dt_akses)) {
                $cabang_id = NULL;
            } else {
                $cabang_id = $dt_akses[0];
            }
        }

        if ($cabang_id === NULL) {
            $cabang = [];
            $kasbon = [];
            $karyawan = [];
        } else {
            $cabang = Cabang::whereIn('id', $dt_akses)->get();

            if ($cabang_id === 'all') {
                $kasbon = Kasbon::where('void', 0)->where('tgl', '>=', $tgl1)->where('tgl', '<=', $tgl2)->with(['karyawan', 'user', 'cabang'])->orderBy('tgl', 'ASC')->get();
                $karyawan = Karyawan::where('void', 0)->with('cabang')->get();
            } else {
                $kasbon = Kasbon::where('void', 0)->where('tgl', '>=', $tgl1)->where('tgl', '<=', $tgl2)->where('cabang_id', $cabang_id)->with(['karyawan', 'user', 'cabang'])->orderBy('tgl', 'ASC')->get();
                $karyawan = Karyawan::where('void', 0)->where('cabang_id', $cabang_id)->with('cabang')->get();
            }
        }
        return view('kasbon.index', [
            'title' => 'Piutang',
            'kasbon' => $kasbon,
            'karyawan' => $karyawan,
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'cabang' => $cabang,
            'cabang_id' => $cabang_id,
        ]);
    }

    public function addKasbon(Request $request)
    {
        $dt_karyawan = Karyawan::where('id', $request->karyawan_id)->first();
        Kasbon::create([
            'karyawan_id' => $request->karyawan_id,
            'cabang_id' => $dt_karyawan->cabang_id,
            'jumlah' => $request->jumlah,
            'tgl' => $request->tgl,
            'user_id' => Auth::id(),
            'void' => 0
        ]);

        return redirect()->back()->with('success', 'Data piutang berhasil dibuat');
    }

    public function editKasbon(Request $request)
    {
        Kasbon::where('id', $request->id)->update([
            'karyawan_id' => $request->karyawan_id,
            'jumlah' => $request->jumlah,
            'tgl' => $request->tgl,
            'user_id' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Data piutang berhasil diubah');
    }

    public function deleteKasbon($id)
    {
        Kasbon::where('id', $id)->update([
            'void' => 1
        ]);

        return redirect()->back()->with('success', 'Data piutang berhasil dihapus');
    }
}
