<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Penjualan;
use App\Models\PenjualanKaryawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaporanController extends Controller
{
    public function laporanPenjualan(Request $request)
    {
        if ($request->query('tgl1')) {
            $tgl1 = $request->query('tgl1');
            $tgl2 = $request->query('tgl2');
        } else {
            $tgl1 = date('Y-m-01');
            $tgl2 = date('Y-m-t');
        }

        return view('laporan.index', [
            'title' => 'Laporan Penjualan',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'laporan' => Invoice::select('tgl')->selectRaw('SUM(total) as ttl_penjualan, SUM(diskon) as ttl_diskon, COUNT(id) as jml_pelanggan')->where('tgl', '>=', $tgl1)->where('tgl', '<=', $tgl2)->where('void', 0)->where('selesai', 1)->groupBy('tgl')->orderBy('tgl', 'DESC')->get(),
            'perlayanan' => Penjualan::select('penjualan.*')->selectRaw('SUM(qty * harga) as ttl_penjualan, SUM(qty) as jml_service')->where('tgl', '>=', $tgl1)->where('tgl', '<=', $tgl2)->where('void', 0)->groupBy('service_id')->orderBy('service_id', 'ASC')->with(['service'])->get(),
            'pembagian' => PenjualanKaryawan::select('penjualan_karyawan.*')->selectRaw('SUM(harga) as ttl_pembagian')->where('tgl', '>=', $tgl1)->where('tgl', '<=', $tgl2)->where('void', 0)->groupBy('karyawan_id')->with('karyawan')->get(),
        ]);
    }

    public function detailLaporanPenjualan($tgl)
    {
        return view('laporan.detailLaporanPenjualan', [
            'tgl' => $tgl,
            'laporan' => Invoice::where('tgl', $tgl)->where('void', 0)->where('selesai', 1)->orderBy('id', 'ASC')->with(['penjualan', 'penjualan.service', 'penjualanKaryawan', 'penjualanKaryawan.karyawan'])->get(),
        ]);
    }

    public function laporanRefund(Request $request)
    {
        if ($request->query('tgl1')) {
            $tgl1 = $request->query('tgl1');
            $tgl2 = $request->query('tgl2');
        } else {
            $tgl1 = date('Y-m-01');
            $tgl2 = date('Y-m-t');
        }

        return view('laporan.laporanRefund', [
            'title' => 'Laporan Penjualan',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'permintaan' => Invoice::select('invoice.*')->where('void', 2)->with(['penjualan', 'penjualan.service', 'penjualanKaryawan', 'penjualanKaryawan.karyawan'])->get(),
            'refund' => Invoice::select('invoice.*')->where('void', 3)->with(['penjualan', 'penjualan.service', 'penjualanKaryawan', 'penjualanKaryawan.karyawan'])->get()
        ]);
    }

    public function TerimaRefund($id)
    {
        Invoice::where('id', $id)->update(['void' => 3]);
        Penjualan::where('invoice_id', $id)->update(['void' => 3]);
        PenjualanKaryawan::where('invoice_id', $id)->update(['void' => 3]);
        return redirect()->back()->with('success', 'Invoice berhasil di refund');
    }

    public function tolakRefund($id)
    {
        Invoice::where('id', $id)->update(['void' => 0, 'ket_refund' => NULL, 'user_refund' => NULL]);
        Penjualan::where('invoice_id', $id)->update(['void' => 0]);
        PenjualanKaryawan::where('invoice_id', $id)->update(['void' => 0]);
        return redirect()->back()->with('success', 'Refund ditolak');
    }
}
