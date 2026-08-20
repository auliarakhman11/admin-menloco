# Logika & Kalkulasi Saldo Berjalan Investor (Carry-Over Balance)

Dokumentasi implementasi kalkulasi saldo akumulatif ( Pokok, DIV, dan Total ) berdasarkan saldo akhir dari bulan sebelumnya.

---

## 1. Rumus Basic Logika Berjalan

$$\text{Saldo Awal (Bulan } N) = \text{Saldo Akhir (Bulan } N-1)$$

* **Saldo Pokok Akhir:**  
  `Saldo Awal Pokok + Setoran Pokok (Bulan Ini) - Penarikan Pokok (Bulan Ini)`

* **Saldo DIV Akhir:**  
  `Saldo Awal DIV + Hak Bagi Hasil (Bulan Ini) - Penarikan Laba (Bulan Ini)`

* **Total Saldo Investor:**  
  `Saldo Pokok Akhir + Saldo DIV Akhir`

---

## 2. Implementasi Service / Helper (Laravel)

File `app/Services/SaldoInvestorService.php`:

```php
namespace App\Services;

use App\Models\Investor;
use App\Models\PenarikanLaba;  // Model penarikan_laba
use Carbon\Carbon;

class SaldoInvestorService
{
    /**
     * Hitung ringkasan saldo berjalan investor pada bulan/tahun tertentu
     */
    public function getSaldoBerjalan($investorId, $bulan, $tahun)
    {
        // Tanggal batas awal bulan yang dipilih
        $startOfMonth = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();

        // -------------------------------------------------------------
        // 1. HITUNG SALDO AWAL (Akumulasi SEMUA transaksi sebelum bulan ini)
        // -------------------------------------------------------------
        $totalSetoranPokokLalu = 0;
        $totalTarikPokokLalu   = 0;
        $totalBagiHasilLalu    = 0;

        $totalTarikLabaLalu = PenarikanLaba::where('investor_id', $investorId)
            ->where('tgl', '<', $startOfMonth)
            ->sum('jumlah');

        // Saldo Awal Pokok & DIV untuk bulan ini
        $saldoAwalPokok = $totalSetoranPokokLalu - $totalTarikPokokLalu;
        $saldoAwalDiv   = $totalBagiHasilLalu - $totalTarikLabaLalu;

        // -------------------------------------------------------------
        // 2. HITUNG MUTASI BULAN INI
        // -------------------------------------------------------------
        $setorPokokBulanIni = 0;
        $tarikPokokBulanIni = 0;
        $bagiHasilBulanIni  = 0;

        $tarikLabaBulanIni = PenarikanLaba::where('investor_id', $investorId)
            ->whereMonth('tgl', $bulan)
            ->whereYear('tgl', $tahun)
            ->sum('jumlah');

        // -------------------------------------------------------------
        // 3. HITUNG SALDO AKHIR BULAN INI
        // -------------------------------------------------------------
        $saldoAkhirPokok = $saldoAwalPokok + $setorPokokBulanIni - $tarikPokokBulanIni;
        $saldoAkhirDiv   = $saldoAwalDiv + $bagiHasilBulanIni - $tarikLabaBulanIni;
        $saldoTotal      = $saldoAkhirPokok + $saldoAkhirDiv;

        return [
            'saldo_awal_pokok'  => $saldoAwalPokok,
            'saldo_awal_div'    => $saldoAwalDiv,
            'saldo_awal_total'  => $saldoAwalPokok + $saldoAwalDiv,
            
            'mutasi' => [
                'setor_pokok'  => $setorPokokBulanIni,
                'tarik_pokok'  => $tarikPokokBulanIni,
                'bagi_hasil'   => $bagiHasilBulanIni,
                'tarik_laba'   => $tarikLabaBulanIni,
            ],

            'saldo_akhir_pokok' => $saldoAkhirPokok,
            'saldo_akhir_div'   => $saldoAkhirDiv,
            'saldo_total'       => $saldoTotal,
        ];
    }
}
```

---

## 3. Contoh Penggunaan pada Controller

```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SaldoInvestorService;

class LaporanInvestorController extends Controller
{
    public function index(Request $request, SaldoInvestorService $saldoService)
    {
        $investorId = $request->get('investor_id');
        $bulan      = $request->get('bulan', date('m'));
        $tahun      = $request->get('tahun', date('Y'));

        $laporanSaldo = $saldoService->getSaldoBerjalan($investorId, $bulan, $tahun);

        return view('laporan.investor', compact('laporanSaldo', 'bulan', 'tahun'));
    }
}
```

---

## 4. Struktur Output Data (Array Result)

```json
{
  "saldo_awal_pokok": 10000000,
  "saldo_awal_div": 1500000,
  "saldo_awal_total": 11500000,
  "mutasi": {
    "setor_pokok": 2000000,
    "tarik_pokok": 0,
    "bagi_hasil": 500000,
    "tarik_laba": 1000000
  },
  "saldo_akhir_pokok": 12000000,
  "saldo_akhir_div": 1000000,
  "saldo_total": 13000000
}
```
