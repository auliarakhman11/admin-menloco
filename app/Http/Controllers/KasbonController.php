<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Kasbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KasbonController extends Controller
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
        return view('kasbon.index', [
            'title' => 'Piutang',
            'kasbon' => Kasbon::where('void', 0)->where('tgl', '>=', $tgl1)->where('tgl', '<=', $tgl2)->with(['karyawan', 'user'])->orderBy('tgl', 'ASC')->get(),
            'karyawan' => Karyawan::where('void', 0)->get(),
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
        ]);
    }

    public function addKasbon(Request $request)
    {
        Kasbon::create([
            'karyawan_id' => $request->karyawan_id,
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
