<?php

namespace App\Http\Controllers;

use App\Models\AmbilGaji;
use App\Models\Dana;
use App\Models\Investor;
use App\Models\Jurnal;
use App\Models\Kasbon;
use App\Models\PembelianProduk;
use App\Models\Pendapatan;
use App\Models\Penjualan;
use App\Models\PenjualanKaryawan;
use App\Models\Service;
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

        // [SANGAT PENTING - HARD LIMIT]: Semua query tidak boleh menarik data sebelum 2026-08-01.
        if ($startDate < '2026-08-01') {
            $startDate = '2026-08-01';
        }

        // 1. Hitung JASA LAYANAN:
        $jasaLayanan = Penjualan::where('void', 0)
            ->whereNotNull('service_id')
            ->whereBetween('tgl', [$startDate, $endDate])
            ->whereHas('service', function ($query) {
                $query->where('jenis', 1);
            })
            ->select(DB::raw('SUM(harga * qty) as total'))
            ->value('total') ?? 0;

        // 2. Hitung PENJUALAN PRODAK:
        $penjualanProdak = Penjualan::where('void', 0)
            ->whereNotNull('service_id')
            ->whereBetween('tgl', [$startDate, $endDate])
            ->whereHas('service', function ($query) {
                $query->where('jenis', 2);
            })
            ->select(DB::raw('SUM(harga * qty) as total'))
            ->value('total') ?? 0;

        // 3. Hitung PENDAPATAN DLL:
        $pendapatanDll = Pendapatan::whereBetween('tgl', [$startDate, $endDate])
            ->sum('jumlah') ?? 0;

        $totalPemasukan = $jasaLayanan + $penjualanProdak + $pendapatanDll;

        // --- BAGIAN POKOK ---
        $gajiCapster = PenjualanKaryawan::where('void', 0)
            ->whereBetween('tgl', [$startDate, $endDate])
            ->whereIn('jenis_service', [1, 2])
            ->sum('harga') ?? 0;

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

        $totalPokok = $aktualGajiCapster;

        // Service & Detail Pembelian Produk
        $listServiceProduk = Service::where('jenis', 2)
            ->where('void', 0)
            ->get();

        $detailProduk = PembelianProduk::with(['service', 'user'])
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
            ->where('void', 0)
            ->groupBy('akun_id')
            ->get();

        $totalOperasional = $operasional->sum('total_jumlah');

        // --- BAGIAN DIV (DANA) ---

        $divDataSaldoMasuk = Dana::select('jenis', DB::raw('SUM(jumlah) as total_jumlah'))
            ->whereDate('tgl', '>=', $startDate)
            ->whereDate('tgl', '<=', $endDate)
            ->where('jenis_dana', 1)
            ->where('jenis_saldo', 1)
            ->groupBy('jenis')
            ->pluck('total_jumlah', 'jenis');

        $tabunganSaldoMasuk = $divDataSaldoMasuk['TABUNGAN'] ?? 0;
        $cadanganSaldoMasuk = $divDataSaldoMasuk['CADANGAN'] ?? 0;
        $danaSeftySaldoMasuk = $divDataSaldoMasuk['DANA SEFTY'] ?? 0;
        $totalDivSaldoMasuk = $tabunganSaldoMasuk + $cadanganSaldoMasuk + $danaSeftySaldoMasuk;

        $divDataSaldoKeluar = Dana::select('jenis', DB::raw('SUM(jumlah) as total_jumlah'))
            ->whereDate('tgl', '>=', $startDate)
            ->whereDate('tgl', '<=', $endDate)
            ->where('jenis_dana', 2)
            ->where('jenis_saldo', 1)
            ->groupBy('jenis')
            ->pluck('total_jumlah', 'jenis');

        $tabunganSaldoKeluar = $divDataSaldoKeluar['TABUNGAN'] ?? 0;
        $cadanganSaldoKeluar = $divDataSaldoKeluar['CADANGAN'] ?? 0;
        $danaSeftySaldoKeluar = $divDataSaldoKeluar['DANA SEFTY'] ?? 0;
        $totalDivSaldoKeluar = $tabunganSaldoKeluar + $cadanganSaldoKeluar + $danaSeftySaldoKeluar;

        $divData = Dana::select('jenis', DB::raw('SUM(jumlah) as total_jumlah'))
            ->whereDate('tgl', '>=', $startDate)
            ->whereDate('tgl', '<=', $endDate)
            ->where('jenis_dana', 1)
            ->where('jenis_saldo', 2)
            ->groupBy('jenis')
            ->pluck('total_jumlah', 'jenis');

        $tabungan = $divData['TABUNGAN'] ?? 0;
        $cadangan = $divData['CADANGAN'] ?? 0;
        $danaSefty = $divData['DANA SEFTY'] ?? 0;
        $totalDiv = $tabungan + $cadangan + $danaSefty;

        // --- BAGIAN DIV Keluar (DANA) ---
        $divDataKeluar = Dana::select('jenis', DB::raw('SUM(jumlah) as total_jumlah'))
            ->whereDate('tgl', '>=', $startDate)
            ->whereDate('tgl', '<=', $endDate)
            ->where('jenis_dana', 2)
            ->where('jenis_saldo', 2)
            ->groupBy('jenis')
            ->pluck('total_jumlah', 'jenis');

        $tabunganKeluar = $divDataKeluar['TABUNGAN'] ?? 0;
        $cadanganKeluar = $divDataKeluar['CADANGAN'] ?? 0;
        $danaSeftyKeluar = $divDataKeluar['DANA SEFTY'] ?? 0;
        $totalDivKeluar = $tabunganKeluar + $cadanganKeluar + $danaSeftyKeluar;

        // --- BAGIAN LABA ---
        $totalLaba = $totalPemasukan - $totalPokok - $totalOperasional - $totalDiv;

        $investorData = Investor::with('persenInvestor')->get();

        $detailDana = Dana::with('user')
            ->whereDate('tgl', '>=', $startDate)
            ->whereDate('tgl', '<=', $endDate)
            ->where('jenis_dana', 1)
            ->where('jenis_saldo', 2)
            ->orderBy('tgl', 'desc')
            ->get();

        $detailDanaKeluar = Dana::with('user')
            ->whereDate('tgl', '>=', $startDate)
            ->whereDate('tgl', '<=', $endDate)
            ->where('jenis_dana', 2)
            ->where('jenis_saldo', 2)
            ->orderBy('tgl', 'desc')
            ->get();

        $detailDanaSaldoMasuk = Dana::with('user')
            ->whereDate('tgl', '>=', $startDate)
            ->whereDate('tgl', '<=', $endDate)
            ->where('jenis_dana', 1)
            ->where('jenis_saldo', 1)
            ->orderBy('tgl', 'desc')
            ->get();

        $detailDanaSaldoKeluar = Dana::with('user')
            ->whereDate('tgl', '>=', $startDate)
            ->whereDate('tgl', '<=', $endDate)
            ->where('jenis_dana', 2)
            ->where('jenis_saldo', 1)
            ->orderBy('tgl', 'desc')
            ->get();

        $detailPendapatan = Pendapatan::with('user')
            ->whereDate('tgl', '>=', $startDate)
            ->whereDate('tgl', '<=', $endDate)
            ->get();

        // ==========================================
        // QUERY DETAIL POPUP MODAL
        // ==========================================

        // 1. Detail Jasa Layanan
        $detailJasaLayanan = Penjualan::where('void', 0)
            ->whereNotNull('service_id')
            ->whereBetween('tgl', [$startDate, $endDate])
            ->whereHas('service', function ($query) {
                $query->where('jenis', 1);
            })
            ->with('service')
            ->select('service_id', DB::raw('SUM(qty) as total_qty'), DB::raw('SUM(harga * qty) as total_penjualan'))
            ->groupBy('service_id')
            ->get();

        // 2. Detail Penjualan Prodak
        $detailPenjualanProdak = Penjualan::where('void', 0)
            ->whereNotNull('service_id')
            ->whereBetween('tgl', [$startDate, $endDate])
            ->whereHas('service', function ($query) {
                $query->where('jenis', 2);
            })
            ->with('service')
            ->select('service_id', DB::raw('SUM(qty) as total_qty'), DB::raw('SUM(harga * qty) as total_penjualan'))
            ->groupBy('service_id')
            ->get();

        // 3. Detail Saldo Gaji Capster
        $detailGajiCapster = PenjualanKaryawan::where('void', 0)
            ->whereBetween('tgl', [$startDate, $endDate])
            ->whereIn('jenis_service', [1, 2])
            ->with('karyawan')
            ->select('karyawan_id', DB::raw('SUM(harga) as total_gaji'))
            ->groupBy('karyawan_id')
            ->get();

        // 4. Detail Pengeluaran Gaji Capster (Kasbon & Ambil Gaji)
        $detailKasbon = Kasbon::where('void', 0)
            ->whereDate('tgl', '>=', $startDate)
            ->whereDate('tgl', '<=', $endDate)
            ->with('karyawan')
            ->select('karyawan_id', DB::raw('SUM(jumlah) as total_kasbon'))
            ->groupBy('karyawan_id')
            ->get();

        $detailAmbilGaji = AmbilGaji::where('void', 0)
            ->whereDate('tgl', '>=', $startDate)
            ->whereDate('tgl', '<=', $endDate)
            ->with('karyawan')
            ->select('karyawan_id', DB::raw('SUM(jumlah) as total_ambil_gaji'))
            ->groupBy('karyawan_id')
            ->get();

        $detailPengeluaranCapster = [];

        foreach ($detailKasbon as $k) {
            $karyawanId = $k->karyawan_id;
            $nama = $k->karyawan->nm_karyawan ?? ($k->karyawan->nama ?? ($k->karyawan->name ?? 'Unknown'));
            $detailPengeluaranCapster[$karyawanId] = [
                'nama' => $nama,
                'kasbon' => $k->total_kasbon,
                'ambil_gaji' => 0,
                'total' => $k->total_kasbon
            ];
        }

        foreach ($detailAmbilGaji as $a) {
            $karyawanId = $a->karyawan_id;
            $nama = $a->karyawan->nm_karyawan ?? ($a->karyawan->nama ?? ($a->karyawan->name ?? 'Unknown'));
            if (isset($detailPengeluaranCapster[$karyawanId])) {
                $detailPengeluaranCapster[$karyawanId]['ambil_gaji'] += $a->total_ambil_gaji;
                $detailPengeluaranCapster[$karyawanId]['total'] += $a->total_ambil_gaji;
            } else {
                $detailPengeluaranCapster[$karyawanId] = [
                    'nama' => $nama,
                    'kasbon' => 0,
                    'ambil_gaji' => $a->total_ambil_gaji,
                    'total' => $a->total_ambil_gaji
                ];
            }
        }

        $komisiService = PenjualanKaryawan::where('void', 0)
            ->whereBetween('tgl', [$startDate, $endDate])
            ->where('jenis_service', 1)
            ->sum('harga') ?? 0;

        $komisiProduk = PenjualanKaryawan::where('void', 0)
            ->whereBetween('tgl', [$startDate, $endDate])
            ->where('jenis_service', 2)
            ->sum('harga') ?? 0;

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
            'tabunganKeluar',
            'cadanganKeluar',
            'danaSeftyKeluar',
            'totalDivKeluar',
            'tabunganSaldoMasuk',
            'cadanganSaldoMasuk',
            'danaSeftySaldoMasuk',
            'totalDivSaldoMasuk',
            'tabunganSaldoKeluar',
            'cadanganSaldoKeluar',
            'danaSeftySaldoKeluar',
            'totalDivSaldoKeluar',
            'totalLaba',
            'investorData',
            'detailDana',
            'detailDanaKeluar',
            'detailPendapatan',
            'title',
            'listServiceProduk',
            'detailProduk',
            'detailJasaLayanan',
            'detailPenjualanProdak',
            'detailGajiCapster',
            'detailPengeluaranCapster',
            'detailDanaSaldoMasuk',
            'detailDanaSaldoKeluar',
            'komisiService',
            'komisiProduk'
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
            'jenis_dana' => 1,
            'jenis_saldo' => 2,
            'user_id' => Auth::id(),
        ]);

        return redirect()->back()->with('sukses', 'Data Dana (DIV) berhasil ditambahkan!');
    }

    public function storeDanaKeluar(Request $request)
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
            'jenis_dana' => 2,
            'jenis_saldo' => 2,
            'user_id' => Auth::id(),
        ]);

        return redirect()->back()->with('sukses', 'Data Dana (DIV) berhasil ditambahkan!');
    }

    public function storeSaldoDana(Request $request)
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
            'jenis_dana' => 1,
            'jenis_saldo' => 1,
            'user_id' => Auth::id(),
        ]);

        return redirect()->back()->with('sukses', 'Data Dana (DIV) berhasil ditambahkan!');
    }

    public function storeSaldoDanaKeluar(Request $request)
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
            'jenis_dana' => 2,
            'jenis_saldo' => 1,
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

        PembelianProduk::create([
            'tgl'    => $request->tgl,
            'service_id' => $request->service_id,
            'qty'        => $request->qty,
            'jumlah'     => $request->jumlah,
            'user_id'    => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Data Pembelian Produk berhasil ditambahkan.');
    }

    public function destroyProduk($id)
    {
        $produk = PembelianProduk::findOrFail($id);
        $produk->delete();

        return redirect()->back()->with('success', 'Data Pembelian Produk berhasil dihapus.');
    }
}
