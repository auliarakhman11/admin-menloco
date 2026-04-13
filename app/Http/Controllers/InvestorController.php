<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Investor;
use App\Models\PersenInvestor;
use Illuminate\Http\Request;

class InvestorController extends Controller
{
    public function index()
    {
        // dd(Cabang::all());

        return view('investor.index', [
            'title' => 'Investor',
            'investor' => Investor::select('investor.*')->with(['persenInvestor', 'persenInvestor.cabang'])->get(),
            'cabang' => Cabang::all(),
        ]);
    }

    public function addInvestor()
    {
        $cabang_id = request('cabang_id');

        if ($cabang_id) {
            $persen = request('persen');

            $investor = Investor::create([
                'nm_investor' => request('nm_investor'),
            ]);

            for ($count = 0; $count < count($persen); $count++) {
                $check_dt_invertor = PersenInvestor::where('cabang_id', $cabang_id[$count])->where('investor_id', $investor->id)->first();
                if ($check_dt_invertor) {
                    continue;
                } else {
                    PersenInvestor::create([
                        'investor_id' => $investor->id,
                        'cabang_id' => $cabang_id[$count],
                        'persen' => $persen[$count],
                    ]);
                }
            }

            return redirect()->back()->with('success', 'Data investor berhasil dibuat');
        } else {
            return redirect()->back()->with('error', 'Data outlet Belum Diisi');
        }
    }

    public function deletePersenInvestor($id)
    {
        PersenInvestor::where('id', $id)->delete();

        return redirect()->back()->with('success', 'Data investor berhasil dihapus');
    }

    public function editInvestor(Request $request)
    {
        Investor::where('id', $request->id)->update([
            'nm_investor' => $request->nm_investor
        ]);

        $persen_id = $request->persen_id;
        $cabang_id = $request->cabang_id;
        $cabang_id_old = $request->cabang_id_old;
        $persen = $request->persen;

        $cabang_id_edit = $request->cabang_id_edit;
        $persen_edit = $request->persen_edit;

        if ($persen_id) {
            for ($count = 0; $count < count($persen_id); $count++) {

                PersenInvestor::where('id', $persen_id[$count])->update([
                    'cabang_id' => $cabang_id[$count],
                    'persen' => $persen[$count],
                ]);
            }
        }


        if ($cabang_id_edit) {
            for ($count = 0; $count < count($cabang_id_edit); $count++) {
                PersenInvestor::create([
                    'investor_id' => $request->id,
                    'cabang_id' => $cabang_id_edit[$count],
                    'persen' => $persen_edit[$count],
                ]);
            }
        }

        return redirect()->back()->with('success', 'Data investor berhasil dirubah');
    }
}
