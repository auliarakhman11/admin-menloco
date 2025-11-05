<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\Jurnal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JurnalController extends Controller
{
    public function pengeluaran(Request $request)
    {

        if ($request->query('tgl1')) {
            $tgl1 = $request->query('tgl1');
            $tgl2 = $request->query('tgl2');
        } else {
            $tgl1 = date('Y-m-01');
            $tgl2 = date('Y-m-t');
        }

        return view('jurnal.pengeluaran', [
            'title' => 'Laporan Pengeluaran',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'jurnal' => Jurnal::where('tgl', '>=', $tgl1)->where('tgl', '<=', $tgl2)->where('jenis', 2)->where('void', 0)->with(['akun', 'user'])->get(),
            'akun' => Akun::all(),
        ]);
    }

    public function addPengeluaran(Request $request)
    {
        Jurnal::create([
            'cabang_id' => 1,
            'akun_id' => $request->akun_id,
            'jumlah' => $request->jumlah,
            'ket' => $request->ket,
            'jenis' => 2,
            'tgl' => $request->tgl,
            'void' => 0,
            'user_id' => Auth::id()
        ]);

        return redirect()->back()->with('success', 'Data berhasil dibuat');
    }

    public function editPengeluaran(Request $request)
    {
        Jurnal::where('id', $request->id)->update([
            'akun_id' => $request->akun_id,
            'jumlah' => $request->jumlah,
            'ket' => $request->ket,
            'tgl' => $request->tgl,
            'user_id' => Auth::id()
        ]);

        return redirect()->back()->with('success', 'Data berhasil diubah');
    }

    public function deletePengeluaran($id)
    {
        Jurnal::where('id', $id)->update([
            'void' => $id,
        ]);

        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }
}
