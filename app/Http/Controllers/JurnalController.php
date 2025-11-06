<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\Invoice;
use App\Models\Jurnal;
use App\Models\Penjualan;
use App\Models\PenjualanKaryawan;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JurnalController extends Controller
{

    public function dashboard(Request $request){
        if ($request->query('tgl1')) {
            $tgl1 = $request->query('tgl1');
            $tgl2 = $request->query('tgl2');
        } else {
            $tgl1 = date('Y-m-01');
            $tgl2 = date('Y-m-t');
        }

        $periode = Penjualan::where('tgl', '>=', $tgl1)->where('tgl', '<=', $tgl2)->where('void', 0)->groupBy('tgl')->get();
        $penjualan = Invoice::select('invoice.*')->selectRaw("SUM(total) as ttl_penjualan, SUM(diskon) as ttl_diskon")->where('tgl', '>=', $tgl1)->where('tgl', '<=', $tgl2)->where('void', 0)->groupBy('tgl')->get();

        $data_periode = [];
        $data_penjuaalan = [];

        $total_penjualan = 0;
        
        foreach ($periode as $pr) {

            $dt_penjualan = $penjualan->where('tgl',$pr->tgl)->first();

            $data_periode[] =  date("d/m/Y", strtotime($pr->tgl));
            $data_penjuaalan[] =  $dt_penjualan ? ($dt_penjualan->ttl_penjualan - $dt_penjualan->ttl_diskon) : 0;
            $total_penjualan += $dt_penjualan ? ($dt_penjualan->ttl_penjualan - $dt_penjualan->ttl_diskon) : 0;
        }

        $dt_pr = json_encode($data_periode);

        $data_c = [];

        $dt_chart = [];
        $dt_chart['label'] = 'Grafik Penjualan';
        // $rc1 = str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
        // $rc2 = str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
        // $rc3 = str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
        $color = '141414';
        $dt_chart['data'] =  $data_penjuaalan;
        $dt_chart['backgroundColor'] = '#' . $color;
        $dt_chart['borderColor'] = '#' . $color;
        $dt_chart['borderWidth'] = 1;
        $dt_chart['color'] = 'green';
        $data_c[] = $dt_chart;

        $dtc = json_encode($data_c);

        $karyawan = PenjualanKaryawan::select('karyawan_id')->selectRaw("SUM(harga) as ttl_kapster")->where('tgl', '>=', $tgl1)->where('tgl', '<=', $tgl2)->where('void', 0)->groupBy('karyawan_id')->with('karyawan')->get();
        $penjualan = Penjualan::select('penjualan_karyawan.karyawan_id','service.jenis','penjualan.qty','penjualan.service_id')->selectRaw("SUM(penjualan.qty * penjualan.harga) as ttl_penjualan")->leftJoin('service','penjualan.service_id','=','service.id')->leftJoin('penjualan_karyawan','penjualan.invoice_id','=','penjualan_karyawan.invoice_id')->where('penjualan.tgl', '>=', $tgl1)->where('penjualan.tgl', '<=', $tgl2)->where('penjualan.void', 0)->groupBy('penjualan.id')->get();
        $invoice = Invoice::select('penjualan_karyawan.karyawan_id','invoice.id')->selectRaw("SUM(invoice.total) as ttl_invoice, SUM(invoice.diskon) as ttl_diskon")->leftJoin('penjualan_karyawan','invoice.id','=','penjualan_karyawan.invoice_id')->where('invoice.tgl', '>=', $tgl1)->where('invoice.tgl', '<=', $tgl2)->where('invoice.void', 0)->groupBy('invoice.id')->get();

        $dt_kepster = [];
        
        foreach ($karyawan as $k) {

            $dt_penjualan = $penjualan->where('karyawan_id',$k->karyawan_id)->where('jenis',1)->all();
            $dt_invoice = $invoice->where('karyawan_id',$k->karyawan_id)->all();

            $ttl_penjualan = 0;
            foreach ($dt_penjualan as $dp) {
                $ttl_penjualan += $dp->ttl_penjualan;
            }

            $ttl_diskon = 0;
            foreach ($dt_invoice as $dp) {
                $ttl_diskon += $dp->ttl_diskon;
            }

            $dt_kepster [ ] = [
                'nm_karyawan' => $k->karyawan->nama,
                'ttl_kapster' => $k->ttl_kapster,
                'ttl_penjualan' => $ttl_penjualan,
                'ttl_diskon' => $ttl_diskon,
            ];
        }

        $ttl_penjualan_produk = Penjualan::selectRaw("SUM(penjualan.qty * penjualan.harga) as ttl_penjualan")->leftJoin('service','penjualan.service_id','=','service.id')->where('penjualan.tgl', '>=', $tgl1)->where('penjualan.tgl', '<=', $tgl2)->where('penjualan.void', 0)->where('service.jenis',2)->first();

        $service = Service::where('jenis',1)->get();

        $dt_service_kepster = [];

        foreach ($service as $s) {
            $kapster = [];
            foreach ($karyawan as $k) {
                $dt_penjualan = $penjualan->where('service_id',$s->id)->where('karyawan_id',$k->karyawan_id)->all();
                $tot_penjualan = 0;
                foreach ($dt_penjualan as $dp) {
                    $tot_penjualan += $dp->qty;
                }
                $kapster [] = $tot_penjualan;
            }
            

            $dt_service_kepster[] = [
                'nm_service' => $s->nm_service,
                'kapster' => $kapster,
            ];
        }

        return view('jurnal.dashboard', [
            'title' => 'Dashboard',
            'periode' => $dt_pr,
            'chart' => $dtc,
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'dt_kepster' => $dt_kepster,
            'total_penjualan' => $total_penjualan,
            'ttl_penjualan_produk' => $ttl_penjualan_produk ? $ttl_penjualan_produk->ttl_penjualan : 0,
            'jurnal' => Jurnal::selectRaw("SUM(jumlah) as jml_pengeluaran")->where('jenis',2)->where('tgl', '>=', $tgl1)->where('tgl', '<=', $tgl2)->where('void', 0)->first(),
            'dt_service_kepster' => $dt_service_kepster,
            'karyawan' => $karyawan,

        ]);

    }

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
