<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PenarikanLaba;

class PenarikanLabaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'investor_id' => 'required|exists:investors,id',
            'tgl'         => 'required|date',
            'jumlah'      => 'required|numeric|min:1',
        ]);

        PenarikanLaba::create([
            'investor_id' => $request->investor_id,
            'tgl'         => $request->tgl,
            'jumlah'      => $request->jumlah,
        ]);

        return redirect()->back()->with('success', 'Penarikan laba berhasil dicatat.');
    }
}
