<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login_page()
    {
        return view('auth.login', ['title' => 'Login Page']);
    }

    public function login(Request $request)
    {
        $attributes = $request->validate([
            'username' => ['required'],
            'password' => ['required']
        ]);

        // if (Auth::attempt($attributes, true)) {

        //     $user = User::where('username', $request->username)->where('role_id', 1)->first();


        //     session([
        //         'name' => $user->name,
        //         'role_id' => $user->role_id,
        //     ]);

        //     return redirect(route('kasir'));
        // }

        $user = User::where('username', $request->username)->where('role_id', 1)->first();

        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                // dd($user);
                Auth::login($user);

                // return redirect(route('dashboard'))->with('success','Selamat datang '. $user->name);
            } else {
                throw ValidationException::withMessages([
                    'password' => 'Password salah'
                ]);
            }
        }

        throw ValidationException::withMessages([
            'username' => 'Username atau password salah'
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return redirect(route('loginPage'))->with('success', 'Logout Berhasil');
    }

    public function block()
    {
        return view('auth.block');
    }
}
