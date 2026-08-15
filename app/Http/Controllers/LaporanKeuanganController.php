<?php

namespace App\Http\Controllers;

use App\Models\AmbilGaji;
use App\Models\Dana;
use App\Models\Investor;
use App\Models\Jurnal;
use App\Models\Kasbon;
use App\Models\PembelianProduk;
use App\Models\Pendapatan;
use App\Models\PengeluaranAkun;
use App\Models\Penjualan;
use App\Models\PenjualanKaryawan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LaporanKeuanganController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        if (!$startDate) {
            $startDate = Carbon::now()->startOfMonth()->toDateString();
        }

        if (!$endDate) {
            $endDate = Carbon::now()->endOfMonth()->toDateString();
        }

        // [SANGAT PENTING - HARD LIMIT]: Semua query tidak boleh menarik data sebelum 2026-07-01.
        if ($startDate < '2026-08-01') {
            $startDate = '2026-08-01';
        }

        // 1. Hitung JASA LAYANAN:
        // Table penjualan (sum harga * qty) di mana void = 0, memiliki service_id, dan relasi service.jenis = 1.
        $jasaLayanan = Penjualan::where('void', 0)
            ->whereNotNull('service_id')
            ->whereBetween('tgl', [$startDate, $endDate])
            ->whereHas('service', function ($query) {
                $query->where('jenis', 1);
            })
            ->select(DB::raw('SUM(harga * qty) as total'))
            ->value('total') ?? 0;

        // 2. Hitung PENJUALAN PRODAK:
        // Table penjualan (sum harga * qty) di mana void = 0, memiliki service_id, dan relasi service.jenis = 2.
        $penjualanProdak = Penjualan::where('void', 0)
            ->whereNotNull('service_id')
            ->whereBetween('tgl', [$startDate, $endDate])
            ->whereHas('service', function ($query) {
                $query->where('jenis', 2);
            })
            ->select(DB::raw('SUM(harga * qty) as total'))
            ->value('total') ?? 0;

        // 3. Hitung PENDAPATAN DLL:
        // Table pendapatan (sum jumlah). Filter tanggal berdasarkan kolom tgl dari start_date sampai end_date.
        $pendapatanDll = Pendapatan::whereBetween('tgl', [$startDate, $endDate])
            ->sum('jumlah') ?? 0;

        $totalPemasukan = $jasaLayanan + $penjualanProdak + $pendapatanDll;

        // --- BAGIAN POKOK ---
        // PenjualanKaryawan dengan jenis_service 1 dan 2 (SUM(harga))
        $gajiCapster = PenjualanKaryawan::where('void', 0)
            ->whereBetween('tgl', [$startDate, $endDate])
            ->whereIn('jenis_service', [1, 2])
            ->sum('harga') ?? 0;

        // Pengeluaran (Kasbon & Ambil Gaji)
        $totalKasbon = Kasbon::where('void', 0)
            ->whereDate('tgl', '>=', $startDate)
            ->whereDate('tgl', '<=', $endDate)
            ->sum('jumlah') ?? 0;

        $totalAmbilGaji = AmbilGaji::where('void', 0)
            ->whereDate('tgl', '>=', $startDate)
            ->whereDate('tgl', '<=', $endDate)
            ->sum('jumlah') ?? 0;

        $pengeluaranGajiCapster = $totalKasbon + $totalAmbilGaji;
        $aktualGajiCapster = $gajiCapster - $pengeluaranGajiCapster;
        $totalAktualCapster = $aktualGajiCapster;

        // D. PRODAK: PembelianProduk (sum jumlah)
        $prodak = PembelianProduk::whereBetween('tgl', [$startDate, $endDate])
            ->sum('jumlah') ?? 0;

        // TOTAL POKOK (menggunakan aktualGajiCapster untuk sisa pokok gaji capster yang harus dikeluarkan + pengeluaran produk)
        $totalPokok = $gajiCapster + $prodak;

        // 1. Ambil daftar service khusus untuk pilihan produk (jenis = 2 dan void = 0)
        $listServiceProduk = \App\Models\Service::where('jenis', 2)
            ->where('void', 0)
            ->get();

        // 2. Ambil detail data pembelian produk berdasarkan rentang tanggal filter saat ini (atau bulan aktif)
        $detailProduk = \App\Models\PembelianProduk::with(['service', 'user'])
            ->whereDate('tgl', '>=', $startDate)
            ->whereDate('tgl', '<=', $endDate)
            ->orderBy('tgl', 'desc')
            ->get();

        // --- BAGIAN OPERASIONAL ---
        $operasional = Jurnal::with('akun')
            ->select('akun_id', DB::raw('SUM(jumlah) as total_jumlah'))
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('tgl', [$startDate, $endDate]);
            })
            ->groupBy('akun_id')
            ->get();

        $totalOperasional = $operasional->sum('total_jumlah');

        // --- BAGIAN DIV (DANA) ---
        $divData = Dana::select('jenis', DB::raw('SUM(jumlah) as total_jumlah'))
            ->whereDate('tgl', '>=', $startDate)
            ->whereDate('tgl', '<=', $endDate)
            ->groupBy('jenis')
            ->pluck('total_jumlah', 'jenis');

        $tabungan = $divData['TABUNGAN'] ?? 0;
        $cadangan = $divData['CADANGAN'] ?? 0;
        $danaSefty = $divData['DANA SEFTY'] ?? 0;
        $totalDiv = $tabungan + $cadangan + $danaSefty;

        // --- BAGIAN LABA ---
        $totalLaba = $totalPemasukan - $totalPokok - $totalOperasional - $totalDiv;

        $investorData = Investor::with('persenInvestor')->get();

        $detailDana = Dana::with('user')
            ->whereDate('tgl', '>=', $startDate)
            ->whereDate('tgl', '<=', $endDate)
            ->orderBy('tgl', 'desc')
            ->get();

        $detailPendapatan = Pendapatan::with('user')
            ->whereDate('tgl', '>=', $startDate)
            ->whereDate('tgl', '<=', $endDate)
            ->get();

        $title = 'Laporan Keuangan';

        return view('laporan_keuangan.index', compact(
            'startDate',
            'endDate',
            'jasaLayanan',
            'penjualanProdak',
            'pendapatanDll',
            'totalPemasukan',
            'gajiCapster',
            'pengeluaranGajiCapster',
            'aktualGajiCapster',
            'totalAktualCapster',
            'prodak',
            'totalPokok',
            'operasional',
            'totalOperasional',
            'tabungan',
            'cadangan',
            'danaSefty',
            'totalDiv',
            'totalLaba',
            'investorData',
            'detailDana',
            'detailPendapatan',
            'title',
            'listServiceProduk',
            'detailProduk'
        ));
    }

    public function storePendapatanDll(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jumlah' => 'required|numeric',
            'keterangan' => 'required|string',
        ]);

        Pendapatan::create([
            'tgl' => $request->tanggal,
            'jumlah' => $request->jumlah,
            'ket' => $request->keterangan,
            'user_id' => Auth::id(),
        ]);

        return redirect()->back()->with('sukses', 'Data Pendapatan DLL berhasil ditambahkan!');
    }

    public function destroyPendapatanDll($id)
    {
        $pendapatan = Pendapatan::findOrFail($id);
        $pendapatan->delete();

        return redirect()->back()->with('sukses', 'Data Pendapatan DLL berhasil dihapus!');
    }

    public function storeDana(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jenis' => 'required|in:TABUNGAN,CADANGAN,DANA SEFTY',
            'jumlah' => 'required|numeric',
            'keterangan' => 'required|string',
        ]);

        Dana::create([
            'tgl' => $request->tanggal,
            'jenis' => $request->jenis,
            'jumlah' => $request->jumlah,
            'ket' => $request->keterangan,
            'user_id' => Auth::id(),
        ]);

        return redirect()->back()->with('sukses', 'Data Dana (DIV) berhasil ditambahkan!');
    }

    public function destroyDana($id)
    {
        $dana = Dana::findOrFail($id);
        $dana->delete();

        return redirect()->back()->with('sukses', 'Data Dana (DIV) berhasil dihapus!');
    }

    public function storeProduk(Request $request)
    {
        $request->validate([
            'tgl'    => 'required|date',
            'service_id' => 'required',
            'qty'        => 'required|numeric|min:1',
            'jumlah'     => 'required|numeric|min:0',
        ]);

        \App\Models\PembelianProduk::create([
            'tgl'    => $request->tgl,
            'service_id' => $request->service_id,
            'qty'        => $request->qty,
            'jumlah'     => $request->jumlah,
            'user_id'    => auth()->id(), // Otomatis menyimpan ID user yang sedang login
        ]);

        return redirect()->back()->with('success', 'Data Pembelian Produk berhasil ditambahkan.');
    }

    public function destroyProduk($id)
    {
        $produk = \App\Models\PembelianProduk::findOrFail($id);
        $produk->delete();

        return redirect()->back()->with('success', 'Data Pembelian Produk berhasil dihapus.');
    }
}
