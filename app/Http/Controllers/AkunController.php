<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\Jurnal;
use App\Models\PengeluaranAkun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AkunController extends Controller
{
    public function index()
    {
        return view('akun.index', [
            'title' => 'Akun',
            'akun' => Akun::all(),
        ]);
    }

    public function addAkun(Request $request)
    {
        Akun::create([
            'nm_akun' => $request->nm_akun,
            'jml_pengeluaran' => $request->jml_pengeluaran
        ]);

        return redirect()->back()->with('success', 'Data akun berhasil dibuat');
    }

    public function editAkun(Request $request)
    {
        Akun::where('id', $request->id)->update([
            'nm_akun' => $request->nm_akun,
            'jml_pengeluaran' => $request->jml_pengeluaran
        ]);

        return redirect()->back()->with('success', 'Data akun berhasil diubah');
    }

    public function deleteAkun($id)
    {
        DB::transaction(function () use ($id) {
            Jurnal::where('akun_id', $id)->delete();
            PengeluaranAkun::where('akun_id', $id)->delete();
            Akun::where('id', $id)->delete();
        });

        return redirect()->back()->with('success', 'Data akun berhasil dihapus');
    }
}
