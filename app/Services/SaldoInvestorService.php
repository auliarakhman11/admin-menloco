<?php

namespace App\Services;

use App\Models\Investor;
use App\Models\PenarikanLaba;
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
