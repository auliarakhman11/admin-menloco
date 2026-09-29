<?php

namespace App\Http\Controllers;

use App\Models\AksesCabang;
use App\Models\Akun;
use App\Models\AmbilGaji;
use App\Models\Cabang;
use App\Models\Dana;
use App\Models\Div;
use App\Models\Investor;
use App\Models\Jurnal;
use App\Models\Kasbon;
use App\Models\PembelianProduk;
use App\Models\PenarikanLaba;
use App\Models\Pendapatan;
use App\Models\Penjualan;
use App\Models\PenjualanKaryawan;
use App\Models\SaldoGaji;
use App\Models\SaldoOperasional;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LaporanKeuanganController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->start_date ?: Carbon::now()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?: Carbon::now()->endOfMonth()->toDateString();

        // [SANGAT PENTING - HARD LIMIT]: Semua query tidak boleh menarik data sebelum 2026-08-01.
        if ($startDate < '2026-08-01') {
            $startDate = '2026-08-01';
        }

        // Akses Cabang & Cabang
        $dt_akses = AksesCabang::where('user_id', Auth::id())->pluck('cabang_id')->toArray();
        $cabang = Cabang::whereIn('id', $dt_akses)->get();
        $akun = Akun::all();

        // ==========================================
        // 1. PEMASUKAN (Penjualan & Pendapatan)
        // ==========================================
        // Ambil penjualan (jasa & produk) sekaligus beserta relasi service dalam 1 query
        $penjualanGrouped = Penjualan::where('void', 0)
            ->whereNotNull('service_id')
            ->whereBetween('tgl', [$startDate, $endDate])
            ->whereHas('service', function ($query) {
                $query->whereIn('jenis', [1, 2]);
            })
            ->with('service')
            ->select('service_id', DB::raw('SUM(qty) as total_qty'), DB::raw('SUM(harga * qty) as total_penjualan'))
            ->groupBy('service_id')
            ->get();

        // Penjualan grouping with pembayaran_id untuk breakdown Cash & Transfer
        $penjualanByPembayaran = Penjualan::where('void', 0)
            ->whereNotNull('service_id')
            ->whereBetween('tgl', [$startDate, $endDate])
            ->whereHas('service', function ($query) {
                $query->whereIn('jenis', [1, 2]);
            })
            ->with('service')
            ->select('service_id', 'pembayaran_id', DB::raw('SUM(harga * qty) as total_penjualan'))
            ->groupBy('service_id', 'pembayaran_id')
            ->get();

        // Detail Popup Modal
        $detailJasaLayanan = $penjualanGrouped->filter(function ($item) {
            return $item->service && $item->service->jenis == 1;
        })->values();

        $detailPenjualanProdak = $penjualanGrouped->filter(function ($item) {
            return $item->service && $item->service->jenis == 2;
        })->values();

        // Nilai Total Pemasukan
        $jasaLayanan = $detailJasaLayanan->sum('total_penjualan');
        $penjualanProdak = $detailPenjualanProdak->sum('total_penjualan');

        // Pendapatan DLL (1 query untuk data dan total)
        $detailPendapatan = Pendapatan::with('user')
            ->whereBetween('tgl', [$startDate, $endDate])
            ->get();
        $pendapatanDll = $detailPendapatan->sum('jumlah');

        $totalPemasukan = $jasaLayanan + $penjualanProdak + $pendapatanDll;

        // ==========================================
        // 2. BAGIAN POKOK (Gaji/Komisi Capster & Pembelian Produk)
        // ==========================================
        // Komisi Capster (PenjualanKaryawan)
        $penjualanKaryawanGrouped = PenjualanKaryawan::where('void', 0)
            ->whereBetween('tgl', [$startDate, $endDate])
            ->whereIn('jenis_service', [1, 2])
            ->with('karyawan')
            ->select('karyawan_id', 'jenis_service', DB::raw('SUM(harga) as total_gaji'))
            ->groupBy('karyawan_id', 'jenis_service')
            ->get();

        $komisiService = $penjualanKaryawanGrouped->where('jenis_service', 1)->sum('total_gaji');
        $komisiProduk = $penjualanKaryawanGrouped->where('jenis_service', 2)->sum('total_gaji');
        $gajiCapster = $komisiService + $komisiProduk;

        // Detail Saldo Gaji Capster (Grouping per karyawan)
        $detailGajiCapster = $penjualanKaryawanGrouped->groupBy('karyawan_id')->map(function ($items) {
            $first = $items->first();
            $item = clone $first;
            $item->total_gaji = $items->sum('total_gaji');
            return $item;
        })->values();

        // Kasbon
        $detailKasbon = Kasbon::where('void', 0)
            ->whereBetween('tgl', [$startDate, $endDate])
            ->with('karyawan')
            ->select('karyawan_id', DB::raw('SUM(jumlah) as total_kasbon'))
            ->groupBy('karyawan_id')
            ->get();
        $totalKasbon = $detailKasbon->sum('total_kasbon');

        // Ambil Gaji
        $detailAmbilGaji = AmbilGaji::where('void', 0)
            ->whereBetween('tgl', [$startDate, $endDate])
            ->with('karyawan')
            ->select('karyawan_id', DB::raw('SUM(jumlah) as total_ambil_gaji'))
            ->groupBy('karyawan_id')
            ->get();
        $totalAmbilGaji = $detailAmbilGaji->sum('total_ambil_gaji');

        $pengeluaranGajiCapster = $totalKasbon + $totalAmbilGaji;
        $aktualGajiCapster = $gajiCapster - $pengeluaranGajiCapster;
        $totalAktualCapster = $aktualGajiCapster;

        // Detail Pengeluaran Capster Gabungan
        $detailPengeluaranCapster = [];
        foreach ($detailKasbon as $k) {
            $karyawanId = $k->karyawan_id;
            $nama = $k->karyawan->nm_karyawan ?? ($k->karyawan->nama ?? ($k->karyawan->name ?? 'Unknown'));
            $detailPengeluaranCapster[$karyawanId] = [
                'nama' => $nama,
                'kasbon' => (float)$k->total_kasbon,
                'ambil_gaji' => 0,
                'total' => (float)$k->total_kasbon
            ];
        }

        foreach ($detailAmbilGaji as $a) {
            $karyawanId = $a->karyawan_id;
            $nama = $a->karyawan->nm_karyawan ?? ($a->karyawan->nama ?? ($a->karyawan->name ?? 'Unknown'));
            if (isset($detailPengeluaranCapster[$karyawanId])) {
                $detailPengeluaranCapster[$karyawanId]['ambil_gaji'] += (float)$a->total_ambil_gaji;
                $detailPengeluaranCapster[$karyawanId]['total'] += (float)$a->total_ambil_gaji;
            } else {
                $detailPengeluaranCapster[$karyawanId] = [
                    'nama' => $nama,
                    'kasbon' => 0,
                    'ambil_gaji' => (float)$a->total_ambil_gaji,
                    'total' => (float)$a->total_ambil_gaji
                ];
            }
        }

        // Pembelian Produk
        $listServiceProduk = Service::where('jenis', 2)->where('void', 0)->get();
        $detailProduk = PembelianProduk::with(['service', 'user'])
            ->whereBetween('tgl', [$startDate, $endDate])
            ->orderBy('tgl', 'desc')
            ->get();
        $prodak = $detailProduk->sum('jumlah');

        $totalPokok = $aktualGajiCapster;

        // -------------------------------------------------------------
        // PERIODE SEBELUMNYA (SALDO BERJALAN)
        // Hard Limit: Data paling awal adalah 2026-08-01
        // -------------------------------------------------------------
        $cutoffDate = Carbon::parse($startDate)->subDay()->toDateString();
        $hasPast = ($cutoffDate >= '2026-08-01');

        // Saldo Berjalan POKOK (Gaji/Komisi Capster & Saldo Gaji)
        $pastGajiCapster = 0;
        $pastPengeluaranCapster = 0;
        $pastSaldoGaji = collect();
        if ($hasPast) {
            $pastGajiCapster = (float)PenjualanKaryawan::where('void', 0)
                ->whereBetween('tgl', ['2026-08-01', $cutoffDate])
                ->whereIn('jenis_service', [1, 2])
                ->sum('harga');

            $pastKasbon = (float)Kasbon::where('void', 0)
                ->whereBetween('tgl', ['2026-08-01', $cutoffDate])
                ->sum('jumlah');

            $pastAmbilGaji = (float)AmbilGaji::where('void', 0)
                ->whereBetween('tgl', ['2026-08-01', $cutoffDate])
                ->sum('jumlah');

            $pastPengeluaranCapster = $pastKasbon + $pastAmbilGaji;

            $pastSaldoGaji = SaldoGaji::whereBetween('tgl', ['2026-08-01', $cutoffDate])
                ->select('jenis', 'pembayaran_id', DB::raw('SUM(jumlah) as total'))
                ->groupBy('jenis', 'pembayaran_id')
                ->get();
        }

        $allSaldoGaji = SaldoGaji::with(['user', 'cabang'])
            ->whereBetween('tgl', [$startDate, $endDate])
            ->orderBy('tgl', 'desc')
            ->get();

        $detail_saldo_gaji_masuk = $allSaldoGaji->where('jenis', 1)->values();
        $detail_saldo_gaji_keluar = $allSaldoGaji->where('jenis', 2)->values();

        $pastSaldoGajiMasukCash = (float)$pastSaldoGaji->where('jenis', 1)->where('pembayaran_id', 1)->sum('total');
        $pastSaldoGajiMasukTransfer = (float)$pastSaldoGaji->where('jenis', 1)->where('pembayaran_id', 2)->sum('total');
        $pastSaldoGajiKeluarCash = (float)$pastSaldoGaji->where('jenis', 2)->where('pembayaran_id', 1)->sum('total');
        $pastSaldoGajiKeluarTransfer = (float)$pastSaldoGaji->where('jenis', 2)->where('pembayaran_id', 2)->sum('total');

        $pastPokokNetCash = ($pastGajiCapster + $pastSaldoGajiMasukCash) - ($pastPengeluaranCapster + $pastSaldoGajiKeluarCash);
        $pastPokokNetTransfer = $pastSaldoGajiMasukTransfer - $pastSaldoGajiKeluarTransfer;
        $pastPokokNet = $pastPokokNetCash + $pastPokokNetTransfer;

        $currSaldoGajiMasuk = (float)$detail_saldo_gaji_masuk->sum('jumlah');
        $currSaldoGajiKeluar = (float)$detail_saldo_gaji_keluar->sum('jumlah');

        $saldoBerjalanGajiSaldo = $pastPokokNet + $currSaldoGajiMasuk;
        $saldoBerjalanGajiPengeluaran = $currSaldoGajiKeluar;
        $saldoBerjalanPokok = $saldoBerjalanGajiSaldo - $saldoBerjalanGajiPengeluaran;

        $pokokItems = [];
        foreach ($allSaldoGaji as $sg) {
            $pokokItems[] = [
                'tgl' => date('d-m-Y', strtotime($sg->tgl)),
                'ket' => 'Saldo Gaji: ' . ($sg->ket ?? '-'),
                'pembayaran' => $sg->pembayaran_id == 2 ? 'Transfer' : 'Cash',
                'jenis' => $sg->jenis == 1 ? 'Masuk' : 'Keluar',
                'jumlah' => (float)$sg->jumlah,
            ];
        }
        $pokokDetail = (object)[
            'past_cash' => $pastPokokNetCash,
            'past_transfer' => $pastPokokNetTransfer,
            'curr_saldo_cash' => (float)$detail_saldo_gaji_masuk->where('pembayaran_id', 1)->sum('jumlah') - (float)$detail_saldo_gaji_keluar->where('pembayaran_id', 1)->sum('jumlah'),
            'curr_saldo_transfer' => (float)$detail_saldo_gaji_masuk->where('pembayaran_id', 2)->sum('jumlah') - (float)$detail_saldo_gaji_keluar->where('pembayaran_id', 2)->sum('jumlah'),
            'curr_harian_cash' => $gajiCapster - $pengeluaranGajiCapster,
            'curr_harian_transfer' => 0,
            'items' => $pokokItems,
        ];

        // ==========================================
        // 3. BAGIAN OPERASIONAL (Jurnal & Saldo Operasional)
        // ==========================================
        $allJurnal = Jurnal::with(['akun', 'user', 'cabang'])
            ->whereBetween('tgl', [$startDate, $endDate])
            ->where('void', 0)
            ->orderBy('tgl', 'desc')
            ->get();

        $detail_jurnal_masuk = $allJurnal->where('jenis', 1)->values();
        $detail_jurnal_keluar = $allJurnal->where('jenis', 2)->values();

        $allSaldoOperasional = SaldoOperasional::with(['akun', 'user', 'cabang'])
            ->whereBetween('tgl', [$startDate, $endDate])
            ->orderBy('tgl', 'desc')
            ->get();

        $detail_saldo_oprasional_masuk = $allSaldoOperasional->where('jenis', 1)->values();
        $detail_saldo_oprasional_keluar = $allSaldoOperasional->where('jenis', 2)->values();

        $pastJurnal = collect();
        $pastSaldoOperasional = collect();
        if ($hasPast) {
            $pastJurnal = Jurnal::where('void', 0)
                ->whereBetween('tgl', ['2026-08-01', $cutoffDate])
                ->select('akun_id', 'jenis', 'pembayaran_id', DB::raw('SUM(jumlah) as total'))
                ->groupBy('akun_id', 'jenis', 'pembayaran_id')
                ->get();

            $pastSaldoOperasional = SaldoOperasional::whereBetween('tgl', ['2026-08-01', $cutoffDate])
                ->select('akun_id', 'jenis', 'pembayaran_id', DB::raw('SUM(jumlah) as total'))
                ->groupBy('akun_id', 'jenis', 'pembayaran_id')
                ->get();
        }

        $allAkunIds = $allJurnal->pluck('akun_id')
            ->merge($pastJurnal->pluck('akun_id'))
            ->merge($allSaldoOperasional->pluck('akun_id'))
            ->merge($pastSaldoOperasional->pluck('akun_id'))
            ->unique()
            ->filter()
            ->values();
        $allAkunMap = Akun::whereIn('id', $allAkunIds)->get()->keyBy('id');

        // Operasional per akun beserta saldo berjalan & Cash/Transfer
        $operasional = $allAkunIds->map(function ($akunId) use ($allJurnal, $pastJurnal, $allAkunMap, $allSaldoOperasional, $pastSaldoOperasional) {
            $currJurnalMasukCash = (float)$allJurnal->where('akun_id', $akunId)->where('jenis', 1)->where('pembayaran_id', 1)->sum('jumlah');
            $currJurnalMasukTransfer = (float)$allJurnal->where('akun_id', $akunId)->where('jenis', 1)->where('pembayaran_id', 2)->sum('jumlah');
            $currJurnalKeluarCash = (float)$allJurnal->where('akun_id', $akunId)->where('jenis', 2)->where('pembayaran_id', 1)->sum('jumlah');
            $currJurnalKeluarTransfer = (float)$allJurnal->where('akun_id', $akunId)->where('jenis', 2)->where('pembayaran_id', 2)->sum('jumlah');

            $currMasuk = $currJurnalMasukCash + $currJurnalMasukTransfer;
            $currKeluar = $currJurnalKeluarCash + $currJurnalKeluarTransfer;

            $pastMasukCash = (float)$pastJurnal->where('akun_id', $akunId)->where('jenis', 1)->where('pembayaran_id', 1)->sum('total');
            $pastMasukTransfer = (float)$pastJurnal->where('akun_id', $akunId)->where('jenis', 1)->where('pembayaran_id', 2)->sum('total');
            $pastKeluarCash = (float)$pastJurnal->where('akun_id', $akunId)->where('jenis', 2)->where('pembayaran_id', 1)->sum('total');
            $pastKeluarTransfer = (float)$pastJurnal->where('akun_id', $akunId)->where('jenis', 2)->where('pembayaran_id', 2)->sum('total');

            // Saldo Operasional
            $pastSaldoOpMasukCash = (float)$pastSaldoOperasional->where('akun_id', $akunId)->where('jenis', 1)->where('pembayaran_id', 1)->sum('total');
            $pastSaldoOpMasukTransfer = (float)$pastSaldoOperasional->where('akun_id', $akunId)->where('jenis', 1)->where('pembayaran_id', 2)->sum('total');
            $pastSaldoOpKeluarCash = (float)$pastSaldoOperasional->where('akun_id', $akunId)->where('jenis', 2)->where('pembayaran_id', 1)->sum('total');
            $pastSaldoOpKeluarTransfer = (float)$pastSaldoOperasional->where('akun_id', $akunId)->where('jenis', 2)->where('pembayaran_id', 2)->sum('total');

            $pastAkunNetCash = ($pastMasukCash + $pastSaldoOpMasukCash) - ($pastKeluarCash + $pastSaldoOpKeluarCash);
            $pastAkunNetTransfer = ($pastMasukTransfer + $pastSaldoOpMasukTransfer) - ($pastKeluarTransfer + $pastSaldoOpKeluarTransfer);
            $pastAkunNet = $pastAkunNetCash + $pastAkunNetTransfer;

            $currSaldoOpMasukCash = (float)$allSaldoOperasional->where('akun_id', $akunId)->where('jenis', 1)->where('pembayaran_id', 1)->sum('jumlah');
            $currSaldoOpMasukTransfer = (float)$allSaldoOperasional->where('akun_id', $akunId)->where('jenis', 1)->where('pembayaran_id', 2)->sum('jumlah');
            $currSaldoOpKeluarCash = (float)$allSaldoOperasional->where('akun_id', $akunId)->where('jenis', 2)->where('pembayaran_id', 1)->sum('jumlah');
            $currSaldoOpKeluarTransfer = (float)$allSaldoOperasional->where('akun_id', $akunId)->where('jenis', 2)->where('pembayaran_id', 2)->sum('jumlah');

            $currSaldoOpMasuk = $currSaldoOpMasukCash + $currSaldoOpMasukTransfer;
            $currSaldoOpKeluar = $currSaldoOpKeluarCash + $currSaldoOpKeluarTransfer;

            $saldoBerjalanSaldo = $pastAkunNet + $currSaldoOpMasuk;
            $saldoBerjalanPengeluaran = $currSaldoOpKeluar;
            $saldoBerjalanAktual = $saldoBerjalanSaldo - $saldoBerjalanPengeluaran;

            $aktualHarian = $currMasuk - $currKeluar;
            $totalAktual = $saldoBerjalanAktual + $aktualHarian;

            $items = [];
            foreach ($allSaldoOperasional->where('akun_id', $akunId) as $so) {
                $items[] = [
                    'tgl' => date('d-m-Y', strtotime($so->tgl)),
                    'ket' => 'Saldo Op: ' . ($so->ket ?? '-'),
                    'pembayaran' => $so->pembayaran_id == 2 ? 'Transfer' : 'Cash',
                    'jenis' => $so->jenis == 1 ? 'Masuk' : 'Keluar',
                    'jumlah' => (float)$so->jumlah,
                ];
            }
            foreach ($allJurnal->where('akun_id', $akunId) as $j) {
                $items[] = [
                    'tgl' => date('d-m-Y', strtotime($j->tgl)),
                    'ket' => $j->ket ?? '-',
                    'pembayaran' => $j->pembayaran_id == 2 ? 'Transfer' : 'Cash',
                    'jenis' => $j->jenis == 1 ? 'Masuk' : 'Keluar',
                    'jumlah' => (float)$j->jumlah,
                ];
            }

            return (object)[
                'akun_id' => $akunId,
                'akun' => $allAkunMap[$akunId] ?? null,
                'saldo_berjalan_saldo' => $saldoBerjalanSaldo,
                'saldo_berjalan_pengeluaran' => $saldoBerjalanPengeluaran,
                'saldo_berjalan_aktual' => $saldoBerjalanAktual,
                'saldo_berjalan' => $saldoBerjalanSaldo,
                'total_jumlah' => $currMasuk,
                'total_keluar' => $currKeluar,
                'aktual_harian' => $aktualHarian,
                'total_aktual' => $totalAktual,
                'past_cash' => $pastAkunNetCash,
                'past_transfer' => $pastAkunNetTransfer,
                'curr_saldo_cash' => $currSaldoOpMasukCash - $currSaldoOpKeluarCash,
                'curr_saldo_transfer' => $currSaldoOpMasukTransfer - $currSaldoOpKeluarTransfer,
                'curr_harian_cash' => $currJurnalMasukCash - $currJurnalKeluarCash,
                'curr_harian_transfer' => $currJurnalMasukTransfer - $currJurnalKeluarTransfer,
                'items' => $items,
            ];
        });

        $totalOperasional = $operasional->sum('total_jumlah');
        $saldoBerjalanTotalOperasional = $operasional->sum('saldo_berjalan_saldo');

        // ==========================================
        // 4. BAGIAN DIV (DANA)
        // ==========================================
        $allDana = Dana::with('user')
            ->whereBetween('tgl', [$startDate, $endDate])
            ->orderBy('tgl', 'desc')
            ->get();

        // Detail Modal
        $detailDanaSaldoMasuk = $allDana->where('jenis_dana', 1)->where('jenis_saldo', 1)->values();
        $detailDanaSaldoKeluar = $allDana->where('jenis_dana', 2)->where('jenis_saldo', 1)->values();
        $detailDana = $allDana->where('jenis_dana', 1)->where('jenis_saldo', 2)->values();
        $detailDanaKeluar = $allDana->where('jenis_dana', 2)->where('jenis_saldo', 2)->values();

        $pastDana = collect();
        if ($hasPast) {
            $pastDana = Dana::whereBetween('tgl', ['2026-08-01', $cutoffDate])
                ->select('jenis', 'jenis_dana', 'jenis_saldo', 'pembayaran_id', DB::raw('SUM(jumlah) as total'))
                ->groupBy('jenis', 'jenis_dana', 'jenis_saldo', 'pembayaran_id')
                ->get();
        }

        $divItems = [
            'TABUNGAN' => 'Tabungan',
            'CADANGAN' => 'Cadangan',
            'DANA SEFTY' => 'Dana Sefty'
        ];
        $divSummary = [];
        foreach ($divItems as $kat => $label) {
            $pastCash = (float)$pastDana->where('jenis', $kat)->where('jenis_dana', 1)->where('pembayaran_id', 1)->sum('total') - (float)$pastDana->where('jenis', $kat)->where('jenis_dana', 2)->where('pembayaran_id', 1)->sum('total');
            $pastTransfer = (float)$pastDana->where('jenis', $kat)->where('jenis_dana', 1)->where('pembayaran_id', 2)->sum('total') - (float)$pastDana->where('jenis', $kat)->where('jenis_dana', 2)->where('pembayaran_id', 2)->sum('total');
            $pastNet = $pastCash + $pastTransfer;

            $currSaldoMasukCash = (float)$detailDanaSaldoMasuk->where('jenis', $kat)->where('pembayaran_id', 1)->sum('jumlah');
            $currSaldoMasukTransfer = (float)$detailDanaSaldoMasuk->where('jenis', $kat)->where('pembayaran_id', 2)->sum('jumlah');
            $currSaldoKeluarCash = (float)$detailDanaSaldoKeluar->where('jenis', $kat)->where('pembayaran_id', 1)->sum('jumlah');
            $currSaldoKeluarTransfer = (float)$detailDanaSaldoKeluar->where('jenis', $kat)->where('pembayaran_id', 2)->sum('jumlah');

            $currHarianMasukCash = (float)$detailDana->where('jenis', $kat)->where('pembayaran_id', 1)->sum('jumlah');
            $currHarianMasukTransfer = (float)$detailDana->where('jenis', $kat)->where('pembayaran_id', 2)->sum('jumlah');
            $currHarianKeluarCash = (float)$detailDanaKeluar->where('jenis', $kat)->where('pembayaran_id', 1)->sum('jumlah');
            $currHarianKeluarTransfer = (float)$detailDanaKeluar->where('jenis', $kat)->where('pembayaran_id', 2)->sum('jumlah');

            $items = [];
            foreach ($allDana->where('jenis', $kat) as $dn) {
                $items[] = [
                    'tgl' => date('d-m-Y', strtotime($dn->tgl)),
                    'ket' => ($dn->jenis_saldo == 1 ? 'Saldo: ' : 'Harian: ') . ($dn->ket ?? '-'),
                    'pembayaran' => $dn->pembayaran_id == 2 ? 'Transfer' : 'Cash',
                    'jenis' => $dn->jenis_dana == 1 ? 'Masuk' : 'Keluar',
                    'jumlah' => (float)$dn->jumlah,
                ];
            }

            $divSummary[$kat] = (object)[
                'label' => $label,
                'past_cash' => $pastCash,
                'past_transfer' => $pastTransfer,
                'past_net' => $pastNet,
                'curr_saldo_masuk' => $currSaldoMasukCash + $currSaldoMasukTransfer,
                'curr_saldo_keluar' => $currSaldoKeluarCash + $currSaldoKeluarTransfer,
                'curr_harian_masuk' => $currHarianMasukCash + $currHarianMasukTransfer,
                'curr_harian_keluar' => $currHarianKeluarCash + $currHarianKeluarTransfer,
                'curr_saldo_cash' => $currSaldoMasukCash - $currSaldoKeluarCash,
                'curr_saldo_transfer' => $currSaldoMasukTransfer - $currSaldoKeluarTransfer,
                'curr_harian_cash' => $currHarianMasukCash - $currHarianKeluarCash,
                'curr_harian_transfer' => $currHarianMasukTransfer - $currHarianKeluarTransfer,
                'items' => $items,
            ];
        }

        $tabunganSaldoMasuk = $divSummary['TABUNGAN']->past_net + $divSummary['TABUNGAN']->curr_saldo_masuk;
        $cadanganSaldoMasuk = $divSummary['CADANGAN']->past_net + $divSummary['CADANGAN']->curr_saldo_masuk;
        $danaSeftySaldoMasuk = $divSummary['DANA SEFTY']->past_net + $divSummary['DANA SEFTY']->curr_saldo_masuk;
        $totalDivSaldoMasuk = $tabunganSaldoMasuk + $cadanganSaldoMasuk + $danaSeftySaldoMasuk;

        $tabunganSaldoKeluar = $divSummary['TABUNGAN']->curr_saldo_keluar;
        $cadanganSaldoKeluar = $divSummary['CADANGAN']->curr_saldo_keluar;
        $danaSeftySaldoKeluar = $divSummary['DANA SEFTY']->curr_saldo_keluar;
        $totalDivSaldoKeluar = $tabunganSaldoKeluar + $cadanganSaldoKeluar + $danaSeftySaldoKeluar;

        $tabungan = $divSummary['TABUNGAN']->curr_harian_masuk;
        $cadangan = $divSummary['CADANGAN']->curr_harian_masuk;
        $danaSefty = $divSummary['DANA SEFTY']->curr_harian_masuk;
        $totalDiv = $tabungan + $cadangan + $danaSefty;

        $tabunganKeluar = $divSummary['TABUNGAN']->curr_harian_keluar;
        $cadanganKeluar = $divSummary['CADANGAN']->curr_harian_keluar;
        $danaSeftyKeluar = $divSummary['DANA SEFTY']->curr_harian_keluar;
        $totalDivKeluar = $tabunganKeluar + $cadanganKeluar + $danaSeftyKeluar;

        // ==========================================
        // 5. BAGIAN LABA & INVESTOR
        // ==========================================
        $totalLaba = $totalPemasukan - $gajiCapster - $totalOperasional - $totalDiv - $prodak;

        $investorData = Investor::with('persenInvestor')->get();

        // Hitung akumulasi laba bersih masa lalu (untuk saldo berjalan investor) & Saldo Berjalan Pemasukan
        $pastTotalLaba = 0;
        $pastJasa = 0;
        $pastProdak = 0;
        $pastPendapatanDll = 0;
        $pastPengeluaranJasaCash = 0;
        $pastPengeluaranJasaTransfer = 0;
        $pastPengeluaranJasa = 0;
        $pastJasaCash = 0;
        $pastJasaTransfer = 0;
        $pastPembelianProduk = 0;
        $pastKomisiProduk = 0;
        $pastPengeluaranProdak = 0;
        $pastProdakCash = 0;
        $pastProdakTransfer = 0;
        $pastPenarikanLaba = collect();

        if ($hasPast) {
            $pastPenjualan = Penjualan::where('void', 0)
                ->whereNotNull('service_id')
                ->whereBetween('tgl', ['2026-08-01', $cutoffDate])
                ->whereHas('service', function ($query) {
                    $query->whereIn('jenis', [1, 2]);
                })
                ->with('service')
                ->select('service_id', 'pembayaran_id', DB::raw('SUM(harga * qty) as total_penjualan'))
                ->groupBy('service_id', 'pembayaran_id')
                ->get();

            $pastJasa = (float)$pastPenjualan->filter(fn($i) => $i->service && $i->service->jenis == 1)->sum('total_penjualan');
            $pastProdak = (float)$pastPenjualan->filter(fn($i) => $i->service && $i->service->jenis == 2)->sum('total_penjualan');
            $pastPendapatanDll = (float)Pendapatan::whereBetween('tgl', ['2026-08-01', $cutoffDate])->sum('jumlah');
            $pastTotalPemasukan = $pastJasa + $pastProdak + $pastPendapatanDll;

            $pastTotalPokok = $pastGajiCapster;
            $pastTotalOperasional = (float)$pastJurnal->where('jenis', 1)->sum('total');
            $pastTotalDiv = (float)$pastDana->where('jenis_saldo', 2)->where('jenis_dana', 1)->sum('total');
            $pastPembelianProduk = (float)PembelianProduk::whereBetween('tgl', ['2026-08-01', $cutoffDate])->sum('jumlah');

            $pastTotalLaba = $pastTotalPemasukan - $pastTotalPokok - $pastTotalOperasional - $pastTotalDiv - $pastPembelianProduk;

            // Ambil penarikan laba masa lalu
            $pastPenarikanLaba = PenarikanLaba::whereBetween('tgl', ['2026-08-01', $cutoffDate])
                ->select('investor_id', 'jenis', 'pembayaran_id', DB::raw('SUM(jumlah) as total'))
                ->groupBy('investor_id', 'jenis', 'pembayaran_id')
                ->get();

            $pastTotalPenarikanLaba = (float)$pastPenarikanLaba->where('jenis', 4)->sum('total');
            $pastTarikLabaCashTotal = (float)$pastPenarikanLaba->where('jenis', 4)->where('pembayaran_id', 1)->sum('total');
            $pastTarikLabaTransferTotal = (float)$pastPenarikanLaba->where('jenis', 4)->where('pembayaran_id', 2)->sum('total');
            // Pengeluaran Jasa masa lalu: pengeluaran capster + operasional keluar (jurnal keluar) + div keluar (dana keluar) + penarikan laba masa lalu
            $pastJurnalKeluarCash = (float)$pastJurnal->where('jenis', 2)->where('pembayaran_id', 1)->sum('total');
            $pastJurnalKeluarTransfer = (float)$pastJurnal->where('jenis', 2)->where('pembayaran_id', 2)->sum('total');
            $pastDivKeluarCash = (float)$pastDana->where('jenis_saldo', 2)->where('jenis_dana', 2)->where('pembayaran_id', 1)->sum('total');
            $pastDivKeluarTransfer = (float)$pastDana->where('jenis_saldo', 2)->where('jenis_dana', 2)->where('pembayaran_id', 2)->sum('total');

            $pastPengeluaranJasaCash = $pastPengeluaranCapster + $pastJurnalKeluarCash + $pastDivKeluarCash + $pastTarikLabaCashTotal;
            $pastPengeluaranJasaTransfer = $pastJurnalKeluarTransfer + $pastDivKeluarTransfer + $pastTarikLabaTransferTotal;
            $pastPengeluaranJasa = $pastPengeluaranJasaCash + $pastPengeluaranJasaTransfer;

            $pastJasaCash = (float)$pastPenjualan->filter(fn($i) => $i->service && $i->service->jenis == 1 && $i->pembayaran_id == 1)->sum('total_penjualan');
            $pastJasaTransfer = (float)$pastPenjualan->filter(fn($i) => $i->service && $i->service->jenis == 1 && $i->pembayaran_id == 2)->sum('total_penjualan');

            // Pengeluaran Prodak masa lalu: pembelian produk + komisi produk capster
            $pastKomisiProduk = (float)PenjualanKaryawan::where('void', 0)
                ->whereBetween('tgl', ['2026-08-01', $cutoffDate])
                ->where('jenis_service', 2)
                ->sum('harga');
            $pastPengeluaranProdak = $pastPembelianProduk + $pastKomisiProduk;

            $pastProdakCash = (float)$pastPenjualan->filter(fn($i) => $i->service && $i->service->jenis == 2 && $i->pembayaran_id == 1)->sum('total_penjualan');
            $pastProdakTransfer = (float)$pastPenjualan->filter(fn($i) => $i->service && $i->service->jenis == 2 && $i->pembayaran_id == 2)->sum('total_penjualan');
        }

        $pastSaldoManualMasukCash = 0;
        $pastSaldoManualMasukTransfer = 0;
        $pastSaldoManualKeluarCash = 0;
        $pastSaldoManualKeluarTransfer = 0;

        if ($hasPast) {
            $pastSaldoManualMasukCash = (float)$pastSaldoGaji->where('jenis', 1)->where('pembayaran_id', 1)->sum('total')
                + (float)$pastSaldoOperasional->where('jenis', 1)->where('pembayaran_id', 1)->sum('total')
                + (float)$pastDana->where('jenis_saldo', 1)->where('jenis_dana', 1)->where('pembayaran_id', 1)->sum('total')
                + (float)$pastPenarikanLaba->where('jenis', 1)->where('pembayaran_id', 1)->sum('total');

            $pastSaldoManualMasukTransfer = (float)$pastSaldoGaji->where('jenis', 1)->where('pembayaran_id', 2)->sum('total')
                + (float)$pastSaldoOperasional->where('jenis', 1)->where('pembayaran_id', 2)->sum('total')
                + (float)$pastDana->where('jenis_saldo', 1)->where('jenis_dana', 1)->where('pembayaran_id', 2)->sum('total')
                + (float)$pastPenarikanLaba->where('jenis', 1)->where('pembayaran_id', 2)->sum('total');

            $pastSaldoManualKeluarCash = (float)$pastSaldoGaji->where('jenis', 2)->where('pembayaran_id', 1)->sum('total')
                + (float)$pastSaldoOperasional->where('jenis', 2)->where('pembayaran_id', 1)->sum('total')
                + (float)$pastDana->where('jenis_saldo', 1)->where('jenis_dana', 2)->where('pembayaran_id', 1)->sum('total')
                + (float)$pastPenarikanLaba->where('jenis', 2)->where('pembayaran_id', 1)->sum('total');

            $pastSaldoManualKeluarTransfer = (float)$pastSaldoGaji->where('jenis', 2)->where('pembayaran_id', 2)->sum('total')
                + (float)$pastSaldoOperasional->where('jenis', 2)->where('pembayaran_id', 2)->sum('total')
                + (float)$pastDana->where('jenis_saldo', 1)->where('jenis_dana', 2)->where('pembayaran_id', 2)->sum('total')
                + (float)$pastPenarikanLaba->where('jenis', 2)->where('pembayaran_id', 2)->sum('total');
        }

        $pastPendapatanDllCash = $pastPendapatanDll + ($pastSaldoManualMasukCash - $pastSaldoManualKeluarCash);
        $pastPendapatanDllTransfer = $pastSaldoManualMasukTransfer - $pastSaldoManualKeluarTransfer;
        $pastPendapatanDllNet = $pastPendapatanDllCash + $pastPendapatanDllTransfer;

        $jasaPastCash = $pastJasaCash - $pastPengeluaranJasaCash;
        $jasaPastTransfer = $pastJasaTransfer - $pastPengeluaranJasaTransfer;
        $prodakPastCash = $pastProdakCash - $pastPengeluaranProdak;
        $prodakPastTransfer = $pastProdakTransfer;
        $pendapatanPastCash = $pastPendapatanDllCash;
        $pendapatanPastTransfer = $pastPendapatanDllTransfer;

        $jasaPastNet = $pastJasa - $pastPengeluaranJasa;
        $prodakPastNet = $pastProdak - $pastPengeluaranProdak;
        $pendapatanPastNet = $pastPendapatanDllNet;
        $pastTotalAktual = $jasaPastNet + $prodakPastNet + $pendapatanPastNet;

        // Ambil semua penarikan laba periode ini dalam 1 query
        $allPenarikanLaba = PenarikanLaba::with('investor')
            ->whereBetween('tgl', [$startDate, $endDate])
            ->get();

        // Detail Modal Periode Ini
        $detailPenarikanLaba = $allPenarikanLaba->where('jenis', 4)->values();
        $detailSaldoLaba = $allPenarikanLaba->where('jenis', 1)->values();
        $detailSaldoLabaKeluar = $allPenarikanLaba->where('jenis', 2)->values();

        // Grouping untuk tabel utama investor (tetap menyediakan properti jml_penarikan_laba agar backward compatible)
        $penarikanLaba = $detailPenarikanLaba->groupBy('investor_id')->map(function ($items, $investorId) {
            $first = $items->first();
            return (object)[
                'investor_id' => $investorId,
                'jml_penarikan_laba' => $items->sum('jumlah'),
                'investor' => $first ? $first->investor : null,
            ];
        })->values();

        // Saldo Masuk Investor (Saldo Berjalan Masa Lalu + Saldo Masuk Manual Periode Ini)
        $saldoLaba = $investorData->map(function ($investor) use ($detailSaldoLaba, $detailSaldoLabaKeluar, $detailPenarikanLaba, $pastPenarikanLaba, $pastTotalLaba, $allPenarikanLaba) {
            $persen = $investor->persenInvestor->sum('persen') ?? 0;
            $pastBagianLaba = ($persen / 100) * $pastTotalLaba;
            $pastTarikLabaCash = (float)$pastPenarikanLaba->where('investor_id', $investor->id)->where('jenis', 4)->where('pembayaran_id', 1)->sum('total');
            $pastTarikLabaTransfer = (float)$pastPenarikanLaba->where('investor_id', $investor->id)->where('jenis', 4)->where('pembayaran_id', 2)->sum('total');
            $pastSaldoMasukCash = (float)$pastPenarikanLaba->where('investor_id', $investor->id)->where('jenis', 1)->where('pembayaran_id', 1)->sum('total');
            $pastSaldoMasukTransfer = (float)$pastPenarikanLaba->where('investor_id', $investor->id)->where('jenis', 1)->where('pembayaran_id', 2)->sum('total');
            $pastSaldoKeluarCash = (float)$pastPenarikanLaba->where('investor_id', $investor->id)->where('jenis', 2)->where('pembayaran_id', 1)->sum('total');
            $pastSaldoKeluarTransfer = (float)$pastPenarikanLaba->where('investor_id', $investor->id)->where('jenis', 2)->where('pembayaran_id', 2)->sum('total');

            $pastSisaCash = ($pastBagianLaba - $pastTarikLabaCash) + ($pastSaldoMasukCash - $pastSaldoKeluarCash);
            $pastSisaTransfer = $pastSaldoMasukTransfer - ($pastTarikLabaTransfer + $pastSaldoKeluarTransfer);
            $pastSisaLaba = $pastSisaCash + $pastSisaTransfer;

            $currSaldoMasukCash = (float)$detailSaldoLaba->where('investor_id', $investor->id)->where('pembayaran_id', 1)->sum('jumlah');
            $currSaldoMasukTransfer = (float)$detailSaldoLaba->where('investor_id', $investor->id)->where('pembayaran_id', 2)->sum('jumlah');
            $currSaldoMasuk = $currSaldoMasukCash + $currSaldoMasukTransfer;

            $currSaldoKeluarCash = (float)$detailSaldoLabaKeluar->where('investor_id', $investor->id)->where('pembayaran_id', 1)->sum('jumlah');
            $currSaldoKeluarTransfer = (float)$detailSaldoLabaKeluar->where('investor_id', $investor->id)->where('pembayaran_id', 2)->sum('jumlah');

            $currTarikLabaCash = (float)$detailPenarikanLaba->where('investor_id', $investor->id)->where('pembayaran_id', 1)->sum('jumlah');
            $currTarikLabaTransfer = (float)$detailPenarikanLaba->where('investor_id', $investor->id)->where('pembayaran_id', 2)->sum('jumlah');

            $items = [];
            foreach ($allPenarikanLaba->where('investor_id', $investor->id) as $pl) {
                $jnsLabel = $pl->jenis == 4 ? 'Tarik Laba' : ($pl->jenis == 1 ? 'Saldo Masuk' : 'Saldo Keluar');
                $items[] = [
                    'tgl' => date('d-m-Y', strtotime($pl->tgl)),
                    'ket' => $jnsLabel,
                    'pembayaran' => $pl->pembayaran_id == 2 ? 'Transfer' : 'Cash',
                    'jenis' => $pl->jenis == 1 ? 'Masuk' : 'Keluar',
                    'jumlah' => (float)$pl->jumlah,
                ];
            }

            return (object)[
                'investor_id' => $investor->id,
                'jml_penarikan_laba' => $pastSisaLaba + $currSaldoMasuk,
                'investor' => $investor,
                'past_cash' => $pastSisaCash,
                'past_transfer' => $pastSisaTransfer,
                'curr_saldo_cash' => $currSaldoMasukCash - $currSaldoKeluarCash,
                'curr_saldo_transfer' => $currSaldoMasukTransfer - $currSaldoKeluarTransfer,
                'curr_harian_cash' => -$currTarikLabaCash,
                'curr_harian_transfer' => -$currTarikLabaTransfer,
                'items' => $items,
            ];
        })->values();

        $saldoLabaKeluar = $detailSaldoLabaKeluar->groupBy('investor_id')->map(function ($items, $investorId) {
            $first = $items->first();
            return (object)[
                'investor_id' => $investorId,
                'jml_penarikan_laba' => $items->sum('jumlah'),
                'investor' => $first ? $first->investor : null,
            ];
        })->values();

        // Rumus perbaikan:
        // Saldo Manual Masuk & Keluar Periode Ini
        $currSaldoPokokMasuk = (float)$detail_saldo_gaji_masuk->sum('jumlah');
        $currSaldoOpMasuk = (float)$detail_saldo_oprasional_masuk->sum('jumlah');
        $currSaldoDivMasuk = (float)$detailDanaSaldoMasuk->sum('jumlah');
        $currSaldoLabaMasuk = (float)$detailSaldoLaba->sum('jumlah');
        $currSaldoManualMasuk = $currSaldoPokokMasuk + $currSaldoOpMasuk + $currSaldoDivMasuk + $currSaldoLabaMasuk;

        $currSaldoPokokKeluar = (float)$detail_saldo_gaji_keluar->sum('jumlah');
        $currSaldoOpKeluar = (float)$detail_saldo_oprasional_keluar->sum('jumlah');
        $currSaldoDivKeluar = (float)$detailDanaSaldoKeluar->sum('jumlah');
        $currSaldoLabaKeluar = (float)$detailSaldoLabaKeluar->sum('jumlah');
        $currSaldoManualKeluar = $currSaldoPokokKeluar + $currSaldoOpKeluar + $currSaldoDivKeluar + $currSaldoLabaKeluar;

        $currSaldoManualMasukCash = (float)$detail_saldo_gaji_masuk->where('pembayaran_id', 1)->sum('jumlah')
            + (float)$detail_saldo_oprasional_masuk->where('pembayaran_id', 1)->sum('jumlah')
            + (float)$detailDanaSaldoMasuk->where('pembayaran_id', 1)->sum('jumlah')
            + (float)$detailSaldoLaba->where('pembayaran_id', 1)->sum('jumlah');
        $currSaldoManualMasukTransfer = (float)$detail_saldo_gaji_masuk->where('pembayaran_id', 2)->sum('jumlah')
            + (float)$detail_saldo_oprasional_masuk->where('pembayaran_id', 2)->sum('jumlah')
            + (float)$detailDanaSaldoMasuk->where('pembayaran_id', 2)->sum('jumlah')
            + (float)$detailSaldoLaba->where('pembayaran_id', 2)->sum('jumlah');

        $currSaldoManualKeluarCash = (float)$detail_saldo_gaji_keluar->where('pembayaran_id', 1)->sum('jumlah')
            + (float)$detail_saldo_oprasional_keluar->where('pembayaran_id', 1)->sum('jumlah')
            + (float)$detailDanaSaldoKeluar->where('pembayaran_id', 1)->sum('jumlah')
            + (float)$detailSaldoLabaKeluar->where('pembayaran_id', 1)->sum('jumlah');
        $currSaldoManualKeluarTransfer = (float)$detail_saldo_gaji_keluar->where('pembayaran_id', 2)->sum('jumlah')
            + (float)$detail_saldo_oprasional_keluar->where('pembayaran_id', 2)->sum('jumlah')
            + (float)$detailDanaSaldoKeluar->where('pembayaran_id', 2)->sum('jumlah')
            + (float)$detailSaldoLabaKeluar->where('pembayaran_id', 2)->sum('jumlah');

        $currSaldoManualCash = $currSaldoManualMasukCash - $currSaldoManualKeluarCash;
        $currSaldoManualTransfer = $currSaldoManualMasukTransfer - $currSaldoManualKeluarTransfer;

        // Saldo Berjalan Pendapatan DLL = Saldo Masa Lalu (Akumulasi bulan lalu) + Saldo Masuk Manual Periode Ini
        $pendapatanSaldoBerjalan = $pastPendapatanDllNet + $currSaldoManualMasuk;

        // Pengeluaran Berjalan Pendapatan DLL = Pengeluaran Saldo Manual Periode Ini
        $pendapatanKeluarFormula = $currSaldoManualKeluar;

        $pendapatanItemsPopup = [];
        if ($pastPendapatanDllNet != 0) {
            $pendapatanItemsPopup[] = ['tgl' => 'Masa Lalu', 'ket' => 'TOTAL AKTUAL Pendapatan DLL Bulan Sebelumnya', 'pembayaran' => 'Cash', 'jenis' => $pastPendapatanDllNet >= 0 ? 'Masuk' : 'Keluar', 'jumlah' => abs($pastPendapatanDllNet)];
        }
        if ($currSaldoPokokMasuk > 0) {
            $pendapatanItemsPopup[] = ['tgl' => 'Periode', 'ket' => 'Saldo Masuk Manual POKOK', 'pembayaran' => 'Cash', 'jenis' => 'Masuk', 'jumlah' => $currSaldoPokokMasuk];
        }
        if ($currSaldoOpMasuk > 0) {
            $pendapatanItemsPopup[] = ['tgl' => 'Periode', 'ket' => 'Saldo Masuk Manual OPERASIONAL', 'pembayaran' => 'Cash', 'jenis' => 'Masuk', 'jumlah' => $currSaldoOpMasuk];
        }
        if ($currSaldoDivMasuk > 0) {
            $pendapatanItemsPopup[] = ['tgl' => 'Periode', 'ket' => 'Saldo Masuk Manual DIV', 'pembayaran' => 'Cash', 'jenis' => 'Masuk', 'jumlah' => $currSaldoDivMasuk];
        }
        if ($currSaldoLabaMasuk > 0) {
            $pendapatanItemsPopup[] = ['tgl' => 'Periode', 'ket' => 'Saldo Masuk Manual Laba Investor', 'pembayaran' => 'Cash', 'jenis' => 'Masuk', 'jumlah' => $currSaldoLabaMasuk];
        }
        if ($currSaldoPokokKeluar > 0) {
            $pendapatanItemsPopup[] = ['tgl' => 'Periode', 'ket' => 'Pengeluaran Saldo Manual POKOK', 'pembayaran' => 'Cash', 'jenis' => 'Keluar', 'jumlah' => $currSaldoPokokKeluar];
        }
        if ($currSaldoOpKeluar > 0) {
            $pendapatanItemsPopup[] = ['tgl' => 'Periode', 'ket' => 'Pengeluaran Saldo Manual OPERASIONAL', 'pembayaran' => 'Cash', 'jenis' => 'Keluar', 'jumlah' => $currSaldoOpKeluar];
        }
        if ($currSaldoDivKeluar > 0) {
            $pendapatanItemsPopup[] = ['tgl' => 'Periode', 'ket' => 'Pengeluaran Saldo Manual DIV', 'pembayaran' => 'Cash', 'jenis' => 'Keluar', 'jumlah' => $currSaldoDivKeluar];
        }
        if ($currSaldoLabaKeluar > 0) {
            $pendapatanItemsPopup[] = ['tgl' => 'Periode', 'ket' => 'Pengeluaran Saldo Manual Laba Investor', 'pembayaran' => 'Cash', 'jenis' => 'Keluar', 'jumlah' => $currSaldoLabaKeluar];
        }

        // Saldo Berjalan Pemasukan (Jasa Layanan, Penjualan Prodak, Pendapatan DLL, dan TOTAL)
        // TOTAL AKTUAL bulan sebelumnya hanya jadi saldo berjalan, bukan terbagi di pengeluaran berjalan
        $pemasukanSaldoBerjalan = (object)[
            'jasa_saldo' => $jasaPastNet,
            'jasa_keluar' => 0,
            'jasa_aktual' => $jasaPastNet,
            'jasa_past_cash' => $jasaPastCash,
            'jasa_past_transfer' => $jasaPastTransfer,
            'jasa_curr_saldo_cash' => 0,
            'jasa_curr_saldo_transfer' => 0,

            'prodak_saldo' => $prodakPastNet,
            'prodak_keluar' => 0,
            'prodak_aktual' => $prodakPastNet,
            'prodak_past_cash' => $prodakPastCash,
            'prodak_past_transfer' => $prodakPastTransfer,
            'prodak_curr_saldo_cash' => 0,
            'prodak_curr_saldo_transfer' => 0,

            'pendapatan_saldo' => $pendapatanSaldoBerjalan,
            'pendapatan_keluar' => $pendapatanKeluarFormula,
            'pendapatan_aktual' => $pendapatanSaldoBerjalan - $pendapatanKeluarFormula,
            'pendapatan_past_cash' => $pastPendapatanDllCash,
            'pendapatan_past_transfer' => $pastPendapatanDllTransfer,
            'pendapatan_curr_saldo_cash' => $currSaldoManualCash,
            'pendapatan_curr_saldo_transfer' => $currSaldoManualTransfer,
            'pendapatan_items' => $pendapatanItemsPopup,

            'total_saldo' => $jasaPastNet + $prodakPastNet + $pendapatanSaldoBerjalan,
            'total_keluar' => $pendapatanKeluarFormula,
            'total_aktual' => ($jasaPastNet + $prodakPastNet + $pendapatanSaldoBerjalan) - $pendapatanKeluarFormula,
            'total_past_cash' => $jasaPastCash + $prodakPastCash + $pastPendapatanDllCash,
            'total_past_transfer' => $jasaPastTransfer + $prodakPastTransfer + $pastPendapatanDllTransfer,
            'total_curr_saldo_cash' => $currSaldoManualCash,
            'total_curr_saldo_transfer' => $currSaldoManualTransfer,
        ];

        // Pemasukan Cash & Transfer breakdown
        $pemasukanDetail = (object)[
            'jasa_cash' => (float)$penjualanByPembayaran->filter(fn($i) => $i->service && $i->service->jenis == 1 && $i->pembayaran_id == 1)->sum('total_penjualan'),
            'jasa_transfer' => (float)$penjualanByPembayaran->filter(fn($i) => $i->service && $i->service->jenis == 1 && $i->pembayaran_id == 2)->sum('total_penjualan'),
            'produk_cash' => (float)$penjualanByPembayaran->filter(fn($i) => $i->service && $i->service->jenis == 2 && $i->pembayaran_id == 1)->sum('total_penjualan'),
            'produk_transfer' => (float)$penjualanByPembayaran->filter(fn($i) => $i->service && $i->service->jenis == 2 && $i->pembayaran_id == 2)->sum('total_penjualan'),
            'pendapatan_cash' => (float)$pendapatanDll,
            'pendapatan_transfer' => 0,
        ];

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
            'saldoBerjalanPokok',
            'prodak',
            'totalPokok',
            'operasional',
            'totalOperasional',
            'saldoBerjalanTotalOperasional',
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
            'komisiProduk',
            'akun',
            'cabang',
            'detail_jurnal_keluar',
            'detail_jurnal_masuk',
            'penarikanLaba',
            'detailPenarikanLaba',
            'detailSaldoLaba',
            'detailSaldoLabaKeluar',
            'saldoLaba',
            'saldoLabaKeluar',
            'detail_saldo_oprasional_masuk',
            'detail_saldo_oprasional_keluar',
            'saldoBerjalanGajiSaldo',
            'saldoBerjalanGajiPengeluaran',
            'detail_saldo_gaji_masuk',
            'detail_saldo_gaji_keluar',
            'pokokDetail',
            'divSummary',
            'pemasukanDetail',
            'pemasukanSaldoBerjalan'
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
            'pembayaran_id' => $request->pembayaran_id,
            'jenis_dana' => $request->jenis_dana,
            'jenis_saldo' => $request->jenis_saldo,
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

    public function penarikanLaba(Request $request)
    {
        $request->validate([
            'investor_id' => 'required|exists:investor,id',
            'tgl'         => 'required|date',
            'jumlah'      => 'required|numeric|min:1',
        ]);

        PenarikanLaba::create([
            'investor_id' => $request->investor_id,
            'tgl'         => $request->tgl,
            'jumlah'      => $request->jumlah,
            'jenis'      => $request->jenis,
            'pembayaran_id'      => $request->pembayaran_id,
        ]);

        return redirect()->back()->with('success', 'Penarikan laba berhasil.');
    }

    public function deletePenarikanDana($id)
    {
        $produk = PenarikanLaba::findOrFail($id);
        $produk->delete();

        return redirect()->back()->with('success', 'Hapus berhasil.');
    }

    public function inputPokok()
    {
        $admin = 1;
        $tgl = date('Y-m-d');
        // $tgl = '2026-08-01';

        $dat_pengeluaran = [];

        $cabang = Cabang::where('off', 0)->get();
        $pengeluaran = Akun::where('jml_pengeluaran', '>', 100)->get();

        foreach ($pengeluaran as $d) {

            if (date('m-d') == '03-28') continue;

            if (date('d') != 31) {
                foreach ($cabang as $c) {
                    $dat_pengeluaran[] = [
                        'cabang_id' => $c->id,
                        'akun_id' => $d->id,
                        'jumlah' => $d->jml_pengeluaran / 30,
                        'ket' => 'Pengeluaran Harian ' . $d->nm_akun,
                        'jenis' => 1,
                        'pembayaran_id' => 1,
                        'tgl' => $tgl,
                        'void' => 0,
                        'user_id' => $admin,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];
                }
            }
        }

        $dat_div = [];

        $div = Div::where('jml_pengeluaran', '>', 100)->get();

        foreach ($div as $d) {

            if (date('m-d') == '03-28') continue;

            if (date('d') != 31) {
                $dat_div[] = [
                    'tgl' => $tgl,
                    'jenis' => $d->nm_div,
                    'jumlah' => $d->jml_pengeluaran / 30,
                    'ket' => 'Pengeluaran Harian ' . $d->nm_div,
                    'pembayaran_id' => 1,
                    'jenis_dana' => 1,
                    'jenis_saldo' => 2,
                    'user_id' => $admin,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }
        }

        Jurnal::insert($dat_pengeluaran);

        Dana::insert($dat_div);


        return true;
    }

    public function inputDiv()
    {

        $admin = 1;
        $tgl = '2026-08-01';

        $dat_div = [];

        $div = Div::where('jml_pengeluaran', '>', 100)->get();

        foreach ($div as $d) {

            if (date('m-d') == '03-28') continue;

            if (date('d') != 31) {
                $dat_div[] = [
                    'tgl' => $tgl,
                    'jenis' => $d->nm_div,
                    'jumlah' => $d->jml_pengeluaran / 30,
                    'ket' => 'Pengeluaran Harian ' . $d->nm_div,
                    'pembayaran_id' => 1,
                    'jenis_dana' => 1,
                    'jenis_saldo' => 2,
                    'user_id' => $admin,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }
        }

        Dana::insert($dat_div);

        return $tgl;
    }

    public function storeSaldoOperasional(Request $request)
    {
        $request->validate([
            'tgl' => 'required|date',
            'akun_id' => 'required',
            'cabang_id' => 'required',
            'pembayaran_id' => 'required',
            'jumlah' => 'required|numeric|min:1',
            'ket' => 'required|string',
            'jenis' => 'required|in:1,2',
        ]);

        SaldoOperasional::create([
            'tgl' => $request->tgl,
            'akun_id' => $request->akun_id,
            'cabang_id' => $request->cabang_id,
            'pembayaran_id' => $request->pembayaran_id,
            'jumlah' => $request->jumlah,
            'ket' => $request->ket,
            'jenis' => $request->jenis,
            'user_id' => Auth::id(),
        ]);

        return redirect()->back()->with('sukses', 'Data Saldo Operasional berhasil ditambahkan!');
    }

    public function deleteSaldoOperasional($id)
    {
        $item = SaldoOperasional::findOrFail($id);
        $item->delete();

        return redirect()->back()->with('sukses', 'Data Saldo Operasional berhasil dihapus!');
    }

    public function storeSaldoGaji(Request $request)
    {
        $request->validate([
            'tgl' => 'required|date',
            'cabang_id' => 'required',
            'pembayaran_id' => 'required',
            'jumlah' => 'required|numeric|min:1',
            'ket' => 'required|string',
            'jenis' => 'required|in:1,2',
        ]);

        SaldoGaji::create([
            'tgl' => $request->tgl,
            'cabang_id' => $request->cabang_id,
            'pembayaran_id' => $request->pembayaran_id,
            'jumlah' => $request->jumlah,
            'ket' => $request->ket,
            'jenis' => $request->jenis,
            'user_id' => Auth::id(),
        ]);

        return redirect()->back()->with('sukses', 'Data Saldo Gaji berhasil ditambahkan!');
    }

    public function deleteSaldoGaji($id)
    {
        $item = SaldoGaji::findOrFail($id);
        $item->delete();

        return redirect()->back()->with('sukses', 'Data Saldo Gaji berhasil dihapus!');
    }
}
