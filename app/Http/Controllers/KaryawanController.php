<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;

class KaryawanController extends Controller
{

    public function index()
    {
        return view('karyawan.index', [
            'title' => 'Karyawan',
            'karyawan' => Karyawan::where('void', 0)->get(),
        ]);
    }

    public function addKaryawan(Request $request)
    {
        Karyawan::create([
            'nama' => $request->nama,
            'no_tlp' => $request->no_tlp,
            'alamat' => $request->alamat,
            'tgl_masuk' => $request->tgl_masuk,
            'no_tlp' => $request->no_tlp,
            'pembagian' => 0,
            'void' => 0
        ]);

        return redirect()->back()->with('success', 'Data karyawan berhasil dibuat');
    }

    public function editKaryawan(Request $request)
    {
        Karyawan::where('id', $request->id)->update([
            'nama' => $request->nama,
            'no_tlp' => $request->no_tlp,
            'alamat' => $request->alamat,
            'tgl_masuk' => $request->tgl_masuk,
            'no_tlp' => $request->no_tlp,
            'pembagian' => 0,
        ]);

        return redirect()->back()->with('success', 'Data karyawan berhasil diubah');
    }

    public function deleteKaryawan($id)
    {
        Karyawan::where('id', $id)->update([
            'void' => 1
        ]);

        return redirect()->back()->with('success', 'Data karyawan berhasil dihapus');
    }
}
