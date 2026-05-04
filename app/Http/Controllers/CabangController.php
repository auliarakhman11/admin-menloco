<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\Cabang;
use App\Models\PengeluaranAkun;
use App\Models\User;
use Illuminate\Http\Request;

class CabangController extends Controller
{
    public function index()
    {
        return view('cabang.index', [
            'title' => 'Cabang',
            'cabang' => Cabang::with(['user', 'pengeluaranAkun'])->get(),
            'akun' => Akun::all(),
        ]);
    }

    public function addCabang(Request $request)
    {

        $validator = request()->validate(
            [
                'nama' => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'confirmed'],
            ],
            [
                'nama.required' => 'Nama tidak boleh kosong',
                'nama.string' => 'Nama hanya boleh hufuf dan angka',
                'nama.max' => 'Nama maksimal 255 karakter',
                'username.required' => 'Username tidak boleh kosong',
                'username.string' => 'Username hanya boleh hufuf dan angka',
                'username.max' => 'Username maksimal 255 karakter',
                'username.unique' => 'Username yang sama sudah terdaftar',
                'password.required' => 'Password tidak boleh kosong',
                'password.string' => 'Password hanya boleh hufuf dan angka',
                'password.confirmed' => 'Password tidak sama',

            ]
        );


        $cabang = Cabang::create([
            'nama' => $request->nama,
            'time_zone' => 1,
            'possition' => 0,
            'off' => 0

        ]);

        User::create([
            'name' => $request->nama,
            'username' => $request->username,
            'password' => bcrypt(request('password')),
            'role_id' => 2,
            'cabang_id' => $cabang->id
        ]);

        return redirect()->back()->with('success', 'Data cabang berhasil dibuat');
    }

    public function editCabang(Request $request)
    {
        Cabang::where('id', $request->id)->update([
            'nama' => $request->nama,
        ]);

        User::where('cabang_id', $request->id)->update([
            'name' => $request->nama,
        ]);

        return redirect()->back()->with('success', 'Data cabang berhasil diubah');
    }

    public function addPengeluaranAkun(Request $request)
    {

        PengeluaranAkun::where('cabang_id', $request->id)->delete();

        $akun_id = $request->akun_id;
        $jenis = $request->jenis;
        $jumlah = $request->jumlah;

        $pengeluaran = [];

        for ($count = 0; $count < count($akun_id); $count++) {
            $pengeluaran[] = [
                'cabang_id' => $request->id,
                'akun_id' => $akun_id[$count],
                'jenis' => $jenis[$count],
                'jumlah' => $jumlah[$count],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }

        PengeluaranAkun::insert($pengeluaran);

        return redirect()->back()->with('success', 'Data pengeluaran berhasil diubah');
    }

    public function deletePengeluaranAkun($id)
    {
        PengeluaranAkun::where('id', $id)->delete();
        return redirect()->back()->with('success', 'Data pengeluaran berhasil diubah');
    }
}
