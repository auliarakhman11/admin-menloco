<?php

namespace App\Http\Controllers;

use App\Models\AmbilGaji;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AmbilGajiController extends Controller
{
    public function index(Request $request)
    {

        if ($request->query('tgl1')) {
            $tgl1 = $request->query('tgl1');
            $tgl2 = $request->query('tgl2');
        } else {
            $tgl1 = date('Y-m-01');
            $tgl2 = date('Y-m-t');
        }
        return view('ambil_gaji.index', [
            'title' => 'Laporan Pendapatan',
            'ambil_gaji' => AmbilGaji::where('void', 0)->where('tgl', '>=', $tgl1)->where('tgl', '<=', $tgl2)->with(['karyawan', 'user'])->orderBy('tgl', 'ASC')->get(),
            'karyawan' => Karyawan::where('void', 0)->get(),
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
        ]);
    }

    public function addAmbilGaji(Request $request)
    {
        AmbilGaji::create([
            'karyawan_id' => $request->karyawan_id,
            'jumlah' => $request->jumlah,
            'tgl' => $request->tgl,
            'user_id' => Auth::id(),
            'void' => 0
        ]);

        return redirect()->back()->with('success', 'Data berhasil dibuat');
    }

    public function editAmbilGaji(Request $request)
    {
        AmbilGaji::where('id', $request->id)->update([
            'karyawan_id' => $request->karyawan_id,
            'jumlah' => $request->jumlah,
            'tgl' => $request->tgl,
            'user_id' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Data berhasil diubah');
    }

    public function deleteAmbilGaji($id)
    {
        AmbilGaji::where('id', $id)->update([
            'void' => 1
        ]);

        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }
}
