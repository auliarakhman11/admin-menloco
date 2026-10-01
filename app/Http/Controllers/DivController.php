<?php

namespace App\Http\Controllers;

use App\Models\Div;
use Illuminate\Http\Request;

class DivController extends Controller
{
    public function index()
    {
        return view('div.index', [
            'title' => 'DIV',
            'div' => Div::all(),
        ]);
    }

    public function editDiv(Request $request)
    {
        Div::where('id', $request->id)->update([
            'jml_pengeluaran' => 0
        ]);

        return redirect()->back()->with('success', 'Data akun berhasil diubah');
    }
}
