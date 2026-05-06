<?php

namespace App\Http\Controllers;

use App\Models\AksesCabang;
use App\Models\AksesMenu;
use App\Models\AksesProses;
use App\Models\Cabang;
use App\Models\JenisUser;
use App\Models\Menu;
use App\Models\Proses;
use App\Models\Role;
use App\Models\Seksi;
use App\Models\Submenu;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return view('user.index', [
            'title' => 'User',
            'user' => User::whereIn('role_id', [1, 3])->with('AksesCabang')->get(),
            'cabang' => Cabang::all(),
        ]);
    }

    // public function getDataUser()
    // {
    //     $dt_user = User::query()->orderBy('role_id','ASC')->with(['role']);
    //     return datatables()->of($dt_user)
    //                     ->addColumn('action', function($data){
    //                         $button = '<button type="button" class="btn btn-sm btn-primary edit_user" data-bs-toggle="modal" data-bs-target="#modal_edit_user" user_id="'.$data->id.'"><i class="bx bx-cloud-upload"></i></button>';
    //                         $button .= '&nbsp;&nbsp;';   
    //                         return $button;
    //                     })
    //                     ->rawColumns(['action'])        
    //                     ->addIndexColumn()
    //                     ->make(true);
    // }

    public function addUser()
    {
        $validator = request()->validate(
            [
                'name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'confirmed'],
                'role_id' => ['required'],
            ],
            [
                'name.required' => 'Nama tidak boleh kosong',
                'name.string' => 'Nama hanya boleh hufuf dan angka',
                'name.max' => 'Nama maksimal 255 karakter',
                'username.required' => 'Username tidak boleh kosong',
                'username.string' => 'Username hanya boleh hufuf dan angka',
                'username.max' => 'Username maksimal 255 karakter',
                'username.unique' => 'Username yang sama sudah terdaftar',
                'password.required' => 'Password tidak boleh kosong',
                'password.string' => 'Password hanya boleh hufuf dan angka',
                'password.confirmed' => 'Password tidak sama',
                'role_id.required' => 'Role harus diisi',

            ]
        );


        // if ($validator->fails())
        // {
        //     return response()->json(['errors'=>$validator->errors()->all()]);
        // }

        if (request('cabang_id')) {
            $user = User::create([
                'name' => request('name'),
                'username' => request('username'),
                'password' => bcrypt(request('password')),
                'role_id' => request('role_id'),
            ]);

            $cabang_id = request('cabang_id');

            for ($count = 0; $count < count($cabang_id); $count++) {
                AksesCabang::create([
                    'user_id' => $user->id,
                    'cabang_id' => $cabang_id[$count],
                ]);
            }

            return redirect()->back()->with('success', 'Data user berhasil dibuat');
        } else {
            return redirect()->back()->with('error', 'Akses Cabang harus diisi');
        }
    }

    public function gantiPassword()
    {
        return view('user.ganti_password', [
            'title' => 'Ganti Password'
        ]);
    }

    public function editPassword(Request $request)
    {
        if (!(Hash::check($request->old_password, Auth::user()->password))) {
            return redirect(route('gantiPassword'))->with('error', 'Password saat ini tidak cocok');
        }
        $validator = request()->validate(
            [
                'password' => ['required', 'string', 'confirmed'],
                'old_password' => ['required']

            ],
            [
                'password.required' => 'Password tidak boleh kososng',
                'password.string' => 'Password hanya boleh hufuf dan angka',
                'password.confirmed' => 'Password tidak sama',
            ]
        );

        User::where('id', Auth::user()->id)->update([
            'password' => bcrypt($request->password)
        ]);

        return redirect(route('gantiPassword'))->with('success', 'Password berhasil diganti');
    }

    public function getUser($id)
    {
        $data  = User::where('id', $id)->first();
        return response()->json($data);
    }

    public function editUser(Request $request)
    {
        $cabang_id = $request->cabang_id;

        if ($cabang_id) {
            User::where('id', $request->id)->update([
                'name' => $request->name,
                'role_id' => $request->role_id,
            ]);

            AksesCabang::where('user_id', $request->id)->delete();


            for ($count = 0; $count < count($cabang_id); $count++) {
                AksesCabang::create([
                    'user_id' => $request->id,
                    'cabang_id' => $cabang_id[$count],
                ]);
            }


            return redirect()->back()->with('success', 'Data user berhasil diubah');
        } else {
            return redirect()->back()->with('error', 'Akses Cabang harus diisi');
        }
    }

    public function resetPassword($id)
    {
        User::where('id', $id)->update([
            'password' => bcrypt('123456')
        ]);

        return redirect()->back()->with('success', 'Password berhasil direset, Password user menjadi 123456');
    }
}
