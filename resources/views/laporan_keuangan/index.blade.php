@extends('template.master')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12 mb-4">
                {{-- Alert Flash Message --}}
                @if (session('sukses'))
                    <div class="alert alert-success alert-dismissible" role="alert">
                        {{ session('sukses') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-3">Filter Laporan Keuangan</h5>
                        <form action="{{ route('laporan-keuangan.index') }}" method="GET">
                            <div class="row g-3 align-items-end">

                                <div class="col-md-3">
                                    <label for="cabang_id" class="form-label">Cabang</label>
                                    <select name="cabang_id" id="cabang_id" class="form-select">
                                        <option value="all" {{ request('cabang_id') == 'all' ? 'selected' : '' }}>Semua
                                            Cabang</option>
                                        @foreach ($cabang as $c)
                                            <option value="{{ $c->id }}"
                                                {{ request('cabang_id') == $c->id ? 'selected' : '' }}>{{ $c->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="start_date" class="form-label">Tanggal Mulai</label>
                                    <input type="date" id="start_date" name="start_date" class="form-control"
                                        value="{{ $startDate }}" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="end_date" class="form-label">Tanggal Selesai</label>
                                    <input type="date" id="end_date" name="end_date" class="form-control"
                                        value="{{ $endDate }}" required>
                                </div>
                                <div class="col-md-3 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bx bx-filter-alt me-1"></i> Filter
                                    </button>
                                    <a href="{{ route('laporan-keuangan.index') }}" class="btn btn-outline-secondary">
                                        <i class="bx bx-refresh me-1"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Laporan Keuangan Pemasukan</h5>
                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalMutasiKas">
                            <i class="bx bx-transfer-alt me-1"></i> Mutasi Kas
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive text-nowrap" style="overflow-x: auto;">
                            <table class="table table-bordered align-middle table-sm">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th rowspan="2" class="align-middle">KATEGORI / AKUN</th>
                                        <th colspan="3">SALDO BERJALAN</th>
                                        <th colspan="3">LAPORAN HARIAN</th>
                                        <th rowspan="2" class="align-middle">TOTAL AKTUAL</th>
                                    </tr>
                                    <tr>
                                        <th>SALDO</th>
                                        <th>PENGELUARAN</th>
                                        <th>AKTUAL</th>
                                        <th>SALDO</th>
                                        <th>PENGELUARAN</th>
                                        <th>AKTUAL HARIAN</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Category Header -->
                                    <tr class="table-secondary fw-bold">
                                        <td colspan="8">PEMASUKAN</td>
                                    </tr>

                                    @php
                                        // 1. Penarikan Laba
                                        $totalPenarikanLaba = $penarikanLaba->sum('jml_penarikan_laba');
                                        $tarikLabaCashPeriode = (float) $detailPenarikanLaba
                                            ->where('pembayaran_id', 1)
                                            ->sum('jumlah');
                                        $tarikLabaTransferPeriode = (float) $detailPenarikanLaba
                                            ->where('pembayaran_id', 2)
                                            ->sum('jumlah');

                                        // 2. Pengeluaran Jasa (Gaji Capster + Jurnal Keluar + Dana Keluar + Penarikan Laba)
                                        $jurnalKeluarCash = (float) $detail_jurnal_keluar
                                            ->where('pembayaran_id', 1)
                                            ->sum('jumlah');
                                        $jurnalKeluarTransfer = (float) $detail_jurnal_keluar
                                            ->where('pembayaran_id', 2)
                                            ->sum('jumlah');
                                        $danaKeluarCash = (float) $detailDanaKeluar
                                            ->where('pembayaran_id', 1)
                                            ->sum('jumlah');
                                        $danaKeluarTransfer = (float) $detailDanaKeluar
                                            ->where('pembayaran_id', 2)
                                            ->sum('jumlah');

                                        $pengeluaranJasaCash =
                                            $pengeluaranGajiCapster +
                                            $jurnalKeluarCash +
                                            $danaKeluarCash +
                                            $tarikLabaCashPeriode;
                                        $pengeluaranJasaTransfer =
                                            $jurnalKeluarTransfer + $danaKeluarTransfer + $tarikLabaTransferPeriode;
                                        $pengeluaranJasa = $pengeluaranJasaCash + $pengeluaranJasaTransfer;

                                        // 3. Pengeluaran Prodak
                                        $pengeluaranProdak = $prodak + $komisiProduk;

                                        // 4. Total Pengeluaran Harian
                                        $totalPengeluaranHarian = $pengeluaranJasa + $pengeluaranProdak;

                                        // Breakdown Cash & Transfer Harian
                                        $jasaHarianCash = ($pemasukanDetail->jasa_cash ?? 0) - $pengeluaranJasaCash;
                                        $jasaHarianTransfer =
                                            ($pemasukanDetail->jasa_transfer ?? 0) - $pengeluaranJasaTransfer;

                                        $prodakHarianCash = ($pemasukanDetail->produk_cash ?? 0) - $pengeluaranProdak;
                                        $prodakHarianTransfer = $pemasukanDetail->produk_transfer ?? 0;

                                        $pendapatanHarianCash = (float) ($pemasukanDetail->pendapatan_cash ?? 0);
                                        $pendapatanHarianTransfer = 0;

                                        $totalCurrHarianCash =
                                            $jasaHarianCash + $prodakHarianCash + $pendapatanHarianCash;
                                        $totalCurrHarianTransfer =
                                            $jasaHarianTransfer + $prodakHarianTransfer + $pendapatanHarianTransfer;

                                        // Items rincian transaksi untuk modal popup
                                        $jasaItems = [];
                                        if (($pemasukanDetail->jasa_cash ?? 0) > 0) {
                                            $jasaItems[] = [
                                                'tgl' => 'Periode',
                                                'ket' => 'Pendapatan Jasa Layanan (Cash)',
                                                'pembayaran' => 'Cash',
                                                'jenis' => 'Masuk',
                                                'jumlah' => (float) $pemasukanDetail->jasa_cash,
                                            ];
                                        }
                                        if (($pemasukanDetail->jasa_transfer ?? 0) > 0) {
                                            $jasaItems[] = [
                                                'tgl' => 'Periode',
                                                'ket' => 'Pendapatan Jasa Layanan (Transfer)',
                                                'pembayaran' => 'Transfer',
                                                'jenis' => 'Masuk',
                                                'jumlah' => (float) $pemasukanDetail->jasa_transfer,
                                            ];
                                        }
                                        foreach ($detailPengeluaranCapster as $dpc) {
                                            if ($dpc['total'] > 0) {
                                                $jasaItems[] = [
                                                    'tgl' => 'Periode',
                                                    'ket' => 'Pengeluaran Gaji: ' . $dpc['nama'],
                                                    'pembayaran' => 'Cash',
                                                    'jenis' => 'Keluar',
                                                    'jumlah' => (float) $dpc['total'],
                                                ];
                                            }
                                        }
                                        foreach ($detail_jurnal_keluar as $jk) {
                                            $jasaItems[] = [
                                                'tgl' => date('d-m-Y', strtotime($jk->tgl)),
                                                'ket' =>
                                                    'Operasional: ' .
                                                    ($jk->akun->nm_akun ?? ($jk->ket ?? 'Jurnal Keluar')),
                                                'pembayaran' => $jk->pembayaran_id == 2 ? 'Transfer' : 'Cash',
                                                'jenis' => 'Keluar',
                                                'jumlah' => (float) $jk->jumlah,
                                            ];
                                        }
                                        foreach ($detailDanaKeluar as $dk) {
                                            $jasaItems[] = [
                                                'tgl' => date('d-m-Y', strtotime($dk->tgl)),
                                                'ket' => 'Divisi Keluar: ' . ($dk->jenis ?? 'Dana Keluar'),
                                                'pembayaran' => $dk->pembayaran_id == 2 ? 'Transfer' : 'Cash',
                                                'jenis' => 'Keluar',
                                                'jumlah' => (float) $dk->jumlah,
                                            ];
                                        }
                                        foreach ($detailPenarikanLaba as $pl) {
                                            $jasaItems[] = [
                                                'tgl' => date('d-m-Y', strtotime($pl->tgl)),
                                                'ket' => 'Tarik Laba: ' . ($pl->investor->nm_investor ?? 'Investor'),
                                                'pembayaran' => $pl->pembayaran_id == 2 ? 'Transfer' : 'Cash',
                                                'jenis' => 'Keluar',
                                                'jumlah' => (float) $pl->jumlah,
                                            ];
                                        }

                                        $prodakItems = [];
                                        if (($pemasukanDetail->produk_cash ?? 0) > 0) {
                                            $prodakItems[] = [
                                                'tgl' => 'Periode',
                                                'ket' => 'Penjualan Produk (Cash)',
                                                'pembayaran' => 'Cash',
                                                'jenis' => 'Masuk',
                                                'jumlah' => (float) $pemasukanDetail->produk_cash,
                                            ];
                                        }
                                        if (($pemasukanDetail->produk_transfer ?? 0) > 0) {
                                            $prodakItems[] = [
                                                'tgl' => 'Periode',
                                                'ket' => 'Penjualan Produk (Transfer)',
                                                'pembayaran' => 'Transfer',
                                                'jenis' => 'Masuk',
                                                'jumlah' => (float) $pemasukanDetail->produk_transfer,
                                            ];
                                        }
                                        foreach ($detailProduk as $dp) {
                                            $prodakItems[] = [
                                                'tgl' => date('d-m-Y', strtotime($dp->tgl)),
                                                'ket' => 'Pembelian Produk: ' . ($dp->service->nm_service ?? 'Produk'),
                                                'pembayaran' => 'Cash',
                                                'jenis' => 'Keluar',
                                                'jumlah' => (float) $dp->jumlah,
                                            ];
                                        }
                                        if ($komisiProduk > 0) {
                                            $prodakItems[] = [
                                                'tgl' => 'Periode',
                                                'ket' => 'Komisi Produk Capster',
                                                'pembayaran' => 'Cash',
                                                'jenis' => 'Keluar',
                                                'jumlah' => (float) $komisiProduk,
                                            ];
                                        }

                                        $pendapatanItems = [];
                                        foreach ($detailPendapatan as $dp) {
                                            $pendapatanItems[] = [
                                                'tgl' => date('d-m-Y', strtotime($dp->tgl)),
                                                'ket' =>
                                                    'Pendapatan: ' .
                                                    ($dp->keterangan ?? ($dp->nama ?? 'Pendapatan Lain')),
                                                'pembayaran' => 'Cash',
                                                'jenis' => 'Masuk',
                                                'jumlah' => (float) $dp->jumlah,
                                            ];
                                        }

                                        $totalPemasukanItems = array_merge($jasaItems, $prodakItems, $pendapatanItems);
                                    @endphp

                                    <!-- Jasa Layanan -->
                                    <tr>
                                        <td class="ps-4 d-flex justify-content-between align-items-center">
                                            <span>Jasa Layanan</span>
                                        </td>
                                        <td class="text-center">
                                            {{ $pemasukanSaldoBerjalan->jasa_saldo != 0 ? number_format($pemasukanSaldoBerjalan->jasa_saldo, 0, ',', '.') : '-' }}
                                        </td>
                                        <td class="text-center">
                                            {{ $pemasukanSaldoBerjalan->jasa_keluar > 0 ? number_format($pemasukanSaldoBerjalan->jasa_keluar, 0, ',', '.') : '-' }}
                                        </td>
                                        <td class="text-center">
                                            <span class="btn-show-detail text-decoration-underline text-primary"
                                                style="cursor: pointer;" data-title="Detail Saldo Berjalan - Jasa Layanan"
                                                data-name="Jasa Layanan (Saldo Berjalan)" data-mode="saldo"
                                                data-past-cash="{{ $pemasukanSaldoBerjalan->jasa_past_cash }}"
                                                data-past-transfer="{{ $pemasukanSaldoBerjalan->jasa_past_transfer }}"
                                                data-curr-saldo-cash="{{ $pemasukanSaldoBerjalan->jasa_curr_saldo_cash }}"
                                                data-curr-saldo-transfer="{{ $pemasukanSaldoBerjalan->jasa_curr_saldo_transfer }}"
                                                data-curr-harian-cash="0" data-curr-harian-transfer="0" data-items='[]'>
                                                {{ number_format($pemasukanSaldoBerjalan->jasa_aktual, 0, ',', '.') }}
                                            </span>
                                        </td>
                                        <td class="text-end"><a href="#modalDetailJasaLayanan"
                                                data-bs-toggle="modal">{{ number_format($jasaLayanan, 0, ',', '.') }}</a>
                                        </td>
                                        <td class="text-end">
                                            {{ number_format($pengeluaranJasa, 0, ',', '.') }}
                                        </td>
                                        <td class="text-end">
                                            {{ number_format($jasaLayanan - $pengeluaranJasa, 0, ',', '.') }}
                                        </td>
                                        <td class="text-end fw-semibold">
                                            <span class="btn-show-detail text-decoration-underline text-primary"
                                                style="cursor: pointer;" data-title="Detail Total Aktual - Jasa Layanan"
                                                data-name="Jasa Layanan" data-mode="total"
                                                data-past-cash="{{ $pemasukanSaldoBerjalan->jasa_past_cash }}"
                                                data-past-transfer="{{ $pemasukanSaldoBerjalan->jasa_past_transfer }}"
                                                data-curr-saldo-cash="{{ $pemasukanSaldoBerjalan->jasa_curr_saldo_cash }}"
                                                data-curr-saldo-transfer="{{ $pemasukanSaldoBerjalan->jasa_curr_saldo_transfer }}"
                                                data-curr-harian-cash="{{ $jasaHarianCash }}"
                                                data-curr-harian-transfer="{{ $jasaHarianTransfer }}"
                                                data-items="{{ json_encode($jasaItems) }}">
                                                {{ number_format($jasaLayanan - $pengeluaranJasa + $pemasukanSaldoBerjalan->jasa_aktual, 0, ',', '.') }}
                                            </span>
                                        </td>
                                    </tr>

                                    <!-- Penjualan Prodak -->
                                    <tr>
                                        <td class="ps-4 d-flex justify-content-between align-items-center">
                                            <span>Penjualan Prodak</span>
                                            <button type="button" class="btn btn-sm btn-primary ms-2"
                                                data-bs-toggle="modal" data-bs-target="#modalProduk"><i
                                                    class="bx bx-plus me-1"></i> Pembelian</button>
                                        </td>
                                        <td class="text-center">
                                            {{ $pemasukanSaldoBerjalan->prodak_saldo != 0 ? number_format($pemasukanSaldoBerjalan->prodak_saldo, 0, ',', '.') : '-' }}
                                        </td>
                                        <td class="text-center">
                                            {{ $pemasukanSaldoBerjalan->prodak_keluar > 0 ? number_format($pemasukanSaldoBerjalan->prodak_keluar, 0, ',', '.') : '-' }}
                                        </td>
                                        <td class="text-center">
                                            <span class="btn-show-detail text-decoration-underline text-primary"
                                                style="cursor: pointer;"
                                                data-title="Detail Saldo Berjalan - Penjualan Prodak"
                                                data-name="Penjualan Prodak (Saldo Berjalan)" data-mode="saldo"
                                                data-past-cash="{{ $pemasukanSaldoBerjalan->prodak_past_cash }}"
                                                data-past-transfer="{{ $pemasukanSaldoBerjalan->prodak_past_transfer }}"
                                                data-curr-saldo-cash="{{ $pemasukanSaldoBerjalan->prodak_curr_saldo_cash }}"
                                                data-curr-saldo-transfer="{{ $pemasukanSaldoBerjalan->prodak_curr_saldo_transfer }}"
                                                data-curr-harian-cash="0" data-curr-harian-transfer="0" data-items='[]'>
                                                {{ number_format($pemasukanSaldoBerjalan->prodak_aktual, 0, ',', '.') }}
                                            </span>
                                        </td>
                                        <td class="text-end"><a href="#modalDetailPenjualanProdak"
                                                data-bs-toggle="modal">{{ number_format($penjualanProdak, 0, ',', '.') }}</a>
                                        </td>
                                        <td class="text-end">{{ number_format($pengeluaranProdak, 0, ',', '.') }}</td>
                                        <td class="text-end">
                                            {{ number_format($penjualanProdak - $pengeluaranProdak, 0, ',', '.') }}
                                        </td>
                                        <td class="text-end fw-semibold">
                                            <span class="btn-show-detail text-decoration-underline text-primary"
                                                style="cursor: pointer;"
                                                data-title="Detail Total Aktual - Penjualan Prodak"
                                                data-name="Penjualan Prodak" data-mode="total"
                                                data-past-cash="{{ $pemasukanSaldoBerjalan->prodak_past_cash }}"
                                                data-past-transfer="{{ $pemasukanSaldoBerjalan->prodak_past_transfer }}"
                                                data-curr-saldo-cash="{{ $pemasukanSaldoBerjalan->prodak_curr_saldo_cash }}"
                                                data-curr-saldo-transfer="{{ $pemasukanSaldoBerjalan->prodak_curr_saldo_transfer }}"
                                                data-curr-harian-cash="{{ $prodakHarianCash }}"
                                                data-curr-harian-transfer="{{ $prodakHarianTransfer }}"
                                                data-items="{{ json_encode($prodakItems) }}">
                                                {{ number_format($penjualanProdak - $pengeluaranProdak + $pemasukanSaldoBerjalan->prodak_aktual, 0, ',', '.') }}
                                            </span>
                                        </td>
                                    </tr>

                                    <!-- Pendapatan DLL -->
                                    <tr>
                                        <td class="ps-4 d-flex justify-content-between align-items-center">
                                            <span>Pendapatan DLL</span>
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#modalTambahPendapatanDll">
                                                <i class="bx bx-plus me-1"></i>
                                            </button>
                                        </td>
                                        <td class="text-center">
                                            {{ $pemasukanSaldoBerjalan->pendapatan_saldo != 0 ? number_format($pemasukanSaldoBerjalan->pendapatan_saldo, 0, ',', '.') : '-' }}
                                        </td>
                                        <td class="text-center">
                                            {{ $pemasukanSaldoBerjalan->pendapatan_keluar > 0 ? number_format($pemasukanSaldoBerjalan->pendapatan_keluar, 0, ',', '.') : '-' }}
                                        </td>
                                        <td class="text-center">
                                            <span class="btn-show-detail text-decoration-underline text-primary"
                                                style="cursor: pointer;"
                                                data-title="Detail Saldo Berjalan - Pendapatan DLL"
                                                data-name="Pendapatan DLL (Saldo Berjalan)" data-mode="saldo"
                                                data-past-cash="{{ $pemasukanSaldoBerjalan->pendapatan_past_cash }}"
                                                data-past-transfer="{{ $pemasukanSaldoBerjalan->pendapatan_past_transfer }}"
                                                data-curr-saldo-cash="{{ $pemasukanSaldoBerjalan->pendapatan_curr_saldo_cash }}"
                                                data-curr-saldo-transfer="{{ $pemasukanSaldoBerjalan->pendapatan_curr_saldo_transfer }}"
                                                data-curr-harian-cash="0" data-curr-harian-transfer="0"
                                                data-items="{{ json_encode($pemasukanSaldoBerjalan->pendapatan_items ?? []) }}">
                                                {{ number_format($pemasukanSaldoBerjalan->pendapatan_aktual, 0, ',', '.') }}
                                            </span>
                                        </td>
                                        <td class="text-end">{{ number_format($pendapatanDll, 0, ',', '.') }}</td>
                                        <td class="text-center">-</td>
                                        <td class="text-end">
                                            {{ number_format($pendapatanDll, 0, ',', '.') }}</td>
                                        <td class="text-end fw-semibold">
                                            <span class="btn-show-detail text-decoration-underline text-primary"
                                                style="cursor: pointer;" data-title="Detail Total Aktual - Pendapatan DLL"
                                                data-name="Pendapatan DLL" data-mode="total"
                                                data-past-cash="{{ $pemasukanSaldoBerjalan->pendapatan_past_cash }}"
                                                data-past-transfer="{{ $pemasukanSaldoBerjalan->pendapatan_past_transfer }}"
                                                data-curr-saldo-cash="{{ $pemasukanSaldoBerjalan->pendapatan_curr_saldo_cash }}"
                                                data-curr-saldo-transfer="{{ $pemasukanSaldoBerjalan->pendapatan_curr_saldo_transfer }}"
                                                data-curr-harian-cash="{{ $pendapatanHarianCash }}"
                                                data-curr-harian-transfer="{{ $pendapatanHarianTransfer }}"
                                                data-items="{{ json_encode(array_merge($pemasukanSaldoBerjalan->pendapatan_items ?? [], $pendapatanItems)) }}">
                                                {{ number_format($pendapatanDll + $pemasukanSaldoBerjalan->pendapatan_aktual, 0, ',', '.') }}
                                            </span>
                                        </td>
                                    </tr>

                                    <!-- TOTAL PEMASUKAN -->
                                    <tr class="table-primary fw-bold">
                                        <td>TOTAL SALDO</td>
                                        <td class="text-center">
                                            {{ number_format($pemasukanSaldoBerjalan->total_saldo, 0, ',', '.') }}
                                        </td>
                                        <td class="text-center">
                                            {{ $pemasukanSaldoBerjalan->total_keluar > 0 ? number_format($pemasukanSaldoBerjalan->total_keluar, 0, ',', '.') : '-' }}
                                        </td>
                                        <td class="text-center">
                                            <span class="btn-show-detail text-decoration-underline text-primary"
                                                style="cursor: pointer;"
                                                data-title="Detail Aktual Saldo Berjalan - TOTAL SALDO"
                                                data-name="TOTAL SALDO (Aktual Saldo Berjalan)" data-mode="saldo"
                                                data-past-cash="{{ $pemasukanSaldoBerjalan->total_past_cash + $dataMutasiKas->past_net_cash }}"
                                                data-past-transfer="{{ $pemasukanSaldoBerjalan->total_past_transfer + $dataMutasiKas->past_net_transfer }}"
                                                data-curr-saldo-cash="{{ $pemasukanSaldoBerjalan->total_curr_saldo_cash + $dataMutasiKas->curr_net_cash }}"
                                                data-curr-saldo-transfer="{{ $pemasukanSaldoBerjalan->total_curr_saldo_transfer + $dataMutasiKas->curr_net_transfer }}"
                                                data-curr-harian-cash="0" data-curr-harian-transfer="0" data-items='[]'>
                                                {{ number_format($pemasukanSaldoBerjalan->total_aktual, 0, ',', '.') }}
                                            </span>
                                        </td>
                                        <td class="text-end">{{ number_format($totalPemasukan, 0, ',', '.') }}</td>
                                        <td class="text-end">
                                            {{ number_format($totalPengeluaranHarian, 0, ',', '.') }}
                                        </td>
                                        <td class="text-end">
                                            {{ number_format($totalPemasukan - $totalPengeluaranHarian, 0, ',', '.') }}
                                        </td>
                                        <td class="text-end">
                                            <span class="btn-show-detail text-decoration-underline text-primary"
                                                style="cursor: pointer;" data-title="Detail Total Aktual - TOTAL SALDO"
                                                data-name="TOTAL SALDO" data-mode="total"
                                                data-past-cash="{{ $pemasukanSaldoBerjalan->total_past_cash + $dataMutasiKas->past_net_cash }}"
                                                data-past-transfer="{{ $pemasukanSaldoBerjalan->total_past_transfer + $dataMutasiKas->past_net_transfer }}"
                                                data-curr-saldo-cash="{{ $pemasukanSaldoBerjalan->total_curr_saldo_cash + $dataMutasiKas->curr_net_cash }}"
                                                data-curr-saldo-transfer="{{ $pemasukanSaldoBerjalan->total_curr_saldo_transfer + $dataMutasiKas->curr_net_transfer }}"
                                                data-curr-harian-cash="{{ $totalCurrHarianCash }}"
                                                data-curr-harian-transfer="{{ $totalCurrHarianTransfer }}"
                                                data-items="{{ json_encode($totalPemasukanItems) }}">
                                                {{ number_format($totalPemasukan - $totalPengeluaranHarian + $pemasukanSaldoBerjalan->total_aktual, 0, ',', '.') }}
                                            </span>
                                        </td>
                                    </tr>


                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card mt-2">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Laporan Keuangan Pengeluaran</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive text-nowrap" style="overflow-x: auto;">
                            <table class="table table-bordered align-middle table-sm">
                                <tbody>
                                    <!-- Category Header POKOK -->
                                    <tr class="table-secondary">
                                        <td class="fw-bold text-dark">POKOK</td>
                                        <td class="text-center"><button type="button" class="btn btn-sm btn-primary"
                                                data-bs-toggle="modal" data-bs-target="#modal_add_saldo_gaji"><i
                                                    class="bx bx-plus me-1"></i></button></td>
                                        <td class="text-center"><button type="button" class="btn btn-sm btn-primary"
                                                data-bs-toggle="modal" data-bs-target="#modal_add_pengeluaran_gaji"><i
                                                    class="bx bx-plus me-1"></i></button></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>

                                    <!-- Baris GAJI/KOMISI CAPSTER -->
                                    <tr>
                                        <td class="fw-bold ps-4">Gaji/Komisi Capster</td>
                                        <td class="text-end">{{ number_format($saldoBerjalanGajiSaldo, 0, ',', '.') }}
                                        </td>
                                        <td class="text-end">
                                            {{ number_format($saldoBerjalanGajiPengeluaran, 0, ',', '.') }}
                                        </td>
                                        <td class="text-end">
                                            <span class="btn-show-detail text-primary text-decoration-underline"
                                                style="cursor: pointer;"
                                                data-title="Detail Aktual Saldo Berjalan - Gaji/Komisi Capster"
                                                data-name="Gaji/Komisi Capster" data-mode="saldo"
                                                data-past-cash="{{ $pokokDetail->past_cash ?? 0 }}"
                                                data-past-transfer="{{ $pokokDetail->past_transfer ?? 0 }}"
                                                data-curr-saldo-cash="{{ $pokokDetail->curr_saldo_cash ?? 0 }}"
                                                data-curr-saldo-transfer="{{ $pokokDetail->curr_saldo_transfer ?? 0 }}"
                                                data-curr-harian-cash="0" data-curr-harian-transfer="0"
                                                data-items="{{ json_encode($pokokDetail->items ?? []) }}">
                                                {{ number_format($saldoBerjalanPokok, 0, ',', '.') }}
                                            </span>
                                        </td>
                                        <!-- Klik Saldo Gaji -->
                                        <td class="text-end" style="cursor: pointer;" data-bs-toggle="modal"
                                            data-bs-target="#modalDetailGajiCapster"
                                            title="Klik untuk lihat detail Saldo Capster">
                                            <span
                                                class="text-primary text-decoration-underline">{{ number_format($gajiCapster, 0, ',', '.') }}</span>
                                        </td>
                                        <!-- Klik Pengeluaran Gaji -->
                                        <td class="text-end text-danger" style="cursor: pointer;" data-bs-toggle="modal"
                                            data-bs-target="#modalDetailPengeluaranCapster"
                                            title="Klik untuk lihat detail Pengeluaran Capster">
                                            <span
                                                class="text-decoration-underline">{{ number_format($pengeluaranGajiCapster, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="text-end text-primary fw-bold">
                                            {{ number_format($aktualGajiCapster, 0, ',', '.') }}</td>
                                        <td class="text-end text-success fw-bold">
                                            <span class="btn-show-detail text-success text-decoration-underline"
                                                style="cursor: pointer;"
                                                data-title="Detail Total Aktual - Gaji/Komisi Capster"
                                                data-name="Gaji/Komisi Capster" data-mode="total"
                                                data-past-cash="{{ $pokokDetail->past_cash ?? 0 }}"
                                                data-past-transfer="{{ $pokokDetail->past_transfer ?? 0 }}"
                                                data-curr-saldo-cash="{{ $pokokDetail->curr_saldo_cash ?? 0 }}"
                                                data-curr-saldo-transfer="{{ $pokokDetail->curr_saldo_transfer ?? 0 }}"
                                                data-curr-harian-cash="{{ $pokokDetail->curr_harian_cash ?? 0 }}"
                                                data-curr-harian-transfer="{{ $pokokDetail->curr_harian_transfer ?? 0 }}"
                                                data-items="{{ json_encode($pokokDetail->items ?? []) }}">
                                                {{ number_format($saldoBerjalanPokok + $totalAktualCapster, 0, ',', '.') }}
                                            </span>
                                        </td>
                                    </tr>

                                    <!-- TOTAL POKOK -->
                                    <tr class="table-light">
                                        <td class="fw-bold text-end">TOTAL POKOK</td>
                                        <td class="text-end fw-bold">
                                            {{ number_format($saldoBerjalanGajiSaldo, 0, ',', '.') }}
                                        </td>
                                        <td class="text-end fw-bold">
                                            {{ number_format($saldoBerjalanGajiPengeluaran, 0, ',', '.') }}
                                        </td>
                                        <td class="text-end fw-bold">
                                            <span class="btn-show-detail text-primary text-decoration-underline"
                                                style="cursor: pointer;"
                                                data-title="Detail Aktual Saldo Berjalan - TOTAL POKOK"
                                                data-name="TOTAL POKOK" data-mode="saldo"
                                                data-past-cash="{{ $pokokDetail->past_cash ?? 0 }}"
                                                data-past-transfer="{{ $pokokDetail->past_transfer ?? 0 }}"
                                                data-curr-saldo-cash="{{ $pokokDetail->curr_saldo_cash ?? 0 }}"
                                                data-curr-saldo-transfer="{{ $pokokDetail->curr_saldo_transfer ?? 0 }}"
                                                data-curr-harian-cash="0" data-curr-harian-transfer="0"
                                                data-items="{{ json_encode($pokokDetail->items ?? []) }}">
                                                {{ number_format($saldoBerjalanPokok, 0, ',', '.') }}
                                            </span>
                                        </td>
                                        <td class="text-end fw-bold">{{ number_format($gajiCapster, 0, ',', '.') }}</td>
                                        <td class="text-end fw-bold">
                                            {{ number_format($pengeluaranGajiCapster, 0, ',', '.') }}
                                        </td>
                                        <td class="text-end fw-bold">{{ number_format($totalPokok, 0, ',', '.') }}</td>
                                        <td class="text-end fw-bold">
                                            <span class="btn-show-detail text-dark text-decoration-underline"
                                                style="cursor: pointer;" data-title="Detail Total Aktual - TOTAL POKOK"
                                                data-name="TOTAL POKOK" data-mode="total"
                                                data-past-cash="{{ $pokokDetail->past_cash ?? 0 }}"
                                                data-past-transfer="{{ $pokokDetail->past_transfer ?? 0 }}"
                                                data-curr-saldo-cash="{{ $pokokDetail->curr_saldo_cash ?? 0 }}"
                                                data-curr-saldo-transfer="{{ $pokokDetail->curr_saldo_transfer ?? 0 }}"
                                                data-curr-harian-cash="{{ $pokokDetail->curr_harian_cash ?? 0 }}"
                                                data-curr-harian-transfer="{{ $pokokDetail->curr_harian_transfer ?? 0 }}"
                                                data-items="{{ json_encode($pokokDetail->items ?? []) }}">
                                                {{ number_format($saldoBerjalanPokok + $totalPokok, 0, ',', '.') }}
                                            </span>
                                        </td>
                                    </tr>

                                    <!-- Category Header OPERASIONAL -->
                                    <tr class="table-secondary">
                                        <td class="fw-bold text-dark">OPERASIONAL</td>
                                        <td class="text-center"><button type="button" class="btn btn-sm btn-primary"
                                                data-bs-toggle="modal" data-bs-target="#modal_add_saldo_oprasional"><i
                                                    class="bx bx-plus me-1"></i></button></td>
                                        <td class="text-center"><button type="button" class="btn btn-sm btn-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modal_add_pengeluaran_oprasional"><i
                                                    class="bx bx-plus me-1"></i></button></td>
                                        <td></td>
                                        <td class="fw-bold text-dark text-center"><button type="button"
                                                class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#modal_add_pemasukan"><i
                                                    class="bx bx-plus me-1"></i></button>
                                        </td>
                                        <td class="fw-bold text-dark text-center"><button type="button"
                                                class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#modal_add_pengeluaran"><i
                                                    class="bx bx-plus me-1"></i></button></td>
                                        <td></td>
                                        <td></td>
                                    </tr>

                                    <!-- Looping Data OPERASIONAL -->
                                    @foreach ($operasional as $item)
                                        <tr>
                                            <td class="ps-4">{{ $item->akun->nm_akun ?? 'Tidak Diketahui' }}</td>
                                            <td class="text-end">
                                                {{ number_format($item->saldo_berjalan_saldo, 0, ',', '.') }}
                                            </td>
                                            <td class="text-end">
                                                {{ number_format($item->saldo_berjalan_pengeluaran, 0, ',', '.') }}</td>
                                            <td class="text-end">
                                                <span class="btn-show-detail text-primary text-decoration-underline"
                                                    style="cursor: pointer;"
                                                    data-title="Detail Aktual Saldo Berjalan - {{ $item->akun->nm_akun ?? 'Operasional' }}"
                                                    data-name="{{ $item->akun->nm_akun ?? 'Operasional' }}"
                                                    data-mode="saldo" data-past-cash="{{ $item->past_cash ?? 0 }}"
                                                    data-past-transfer="{{ $item->past_transfer ?? 0 }}"
                                                    data-curr-saldo-cash="{{ $item->curr_saldo_cash ?? 0 }}"
                                                    data-curr-saldo-transfer="{{ $item->curr_saldo_transfer ?? 0 }}"
                                                    data-curr-harian-cash="0" data-curr-harian-transfer="0"
                                                    data-items="{{ json_encode($item->items ?? []) }}">
                                                    {{ number_format($item->saldo_berjalan_aktual, 0, ',', '.') }}
                                                </span>
                                            </td>
                                            <td class="text-end">{{ number_format($item->total_jumlah, 0, ',', '.') }}
                                            </td>
                                            <td class="text-end">{{ number_format($item->total_keluar, 0, ',', '.') }}
                                            </td>
                                            <td class="text-end">
                                                {{ number_format($item->aktual_harian, 0, ',', '.') }}
                                            </td>
                                            <td class="text-end fw-semibold">
                                                <span class="btn-show-detail text-primary text-decoration-underline"
                                                    style="cursor: pointer;"
                                                    data-title="Detail Total Aktual - {{ $item->akun->nm_akun ?? 'Operasional' }}"
                                                    data-name="{{ $item->akun->nm_akun ?? 'Operasional' }}"
                                                    data-mode="total" data-past-cash="{{ $item->past_cash ?? 0 }}"
                                                    data-past-transfer="{{ $item->past_transfer ?? 0 }}"
                                                    data-curr-saldo-cash="{{ $item->curr_saldo_cash ?? 0 }}"
                                                    data-curr-saldo-transfer="{{ $item->curr_saldo_transfer ?? 0 }}"
                                                    data-curr-harian-cash="{{ $item->curr_harian_cash ?? 0 }}"
                                                    data-curr-harian-transfer="{{ $item->curr_harian_transfer ?? 0 }}"
                                                    data-items="{{ json_encode($item->items ?? []) }}">
                                                    {{ number_format($item->total_aktual, 0, ',', '.') }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach

                                    <!-- TOTAL OPERASIONAL -->
                                    <tr class="table-light">
                                        <td class="fw-bold text-end">TOTAL OPERASIONAL</td>
                                        <td class="text-end fw-bold">
                                            {{ number_format($operasional->sum('saldo_berjalan_saldo'), 0, ',', '.') }}
                                        </td>
                                        <td class="text-end fw-bold">
                                            {{ number_format($operasional->sum('saldo_berjalan_pengeluaran'), 0, ',', '.') }}
                                        </td>
                                        <td class="text-end fw-bold">
                                            <span class="btn-show-detail text-primary text-decoration-underline"
                                                style="cursor: pointer;"
                                                data-title="Detail Aktual Saldo Berjalan - TOTAL OPERASIONAL"
                                                data-name="TOTAL OPERASIONAL" data-mode="saldo"
                                                data-past-cash="{{ $operasional->sum('past_cash') }}"
                                                data-past-transfer="{{ $operasional->sum('past_transfer') }}"
                                                data-curr-saldo-cash="{{ $operasional->sum('curr_saldo_cash') }}"
                                                data-curr-saldo-transfer="{{ $operasional->sum('curr_saldo_transfer') }}"
                                                data-curr-harian-cash="0" data-curr-harian-transfer="0"
                                                data-items="{{ json_encode($operasional->pluck('items')->collapse()->values()) }}">
                                                {{ number_format($operasional->sum('saldo_berjalan_aktual'), 0, ',', '.') }}
                                            </span>
                                        </td>
                                        <td class="text-end fw-bold">{{ number_format($totalOperasional, 0, ',', '.') }}
                                        </td>
                                        <td class="text-end fw-bold">
                                            {{ number_format($operasional->sum('total_keluar'), 0, ',', '.') }}</td>
                                        <td class="text-end fw-bold">
                                            {{ number_format($totalOperasional - $operasional->sum('total_keluar'), 0, ',', '.') }}
                                        </td>
                                        <td class="text-end fw-bold">
                                            <span class="btn-show-detail text-dark text-decoration-underline"
                                                style="cursor: pointer;"
                                                data-title="Detail Total Aktual - TOTAL OPERASIONAL"
                                                data-name="TOTAL OPERASIONAL" data-mode="total"
                                                data-past-cash="{{ $operasional->sum('past_cash') }}"
                                                data-past-transfer="{{ $operasional->sum('past_transfer') }}"
                                                data-curr-saldo-cash="{{ $operasional->sum('curr_saldo_cash') }}"
                                                data-curr-saldo-transfer="{{ $operasional->sum('curr_saldo_transfer') }}"
                                                data-curr-harian-cash="{{ $operasional->sum('curr_harian_cash') }}"
                                                data-curr-harian-transfer="{{ $operasional->sum('curr_harian_transfer') }}"
                                                data-items="{{ json_encode($operasional->pluck('items')->collapse()->values()) }}">
                                                {{ number_format($operasional->sum('total_aktual'), 0, ',', '.') }}
                                            </span>
                                        </td>
                                    </tr>

                                    <!-- Category Header DIV -->
                                    <tr class="table-secondary">
                                        <td class="fw-bold text-dark d-flex justify-content-between align-items-center">
                                            <span>DIV</span>
                                        </td>
                                        <td class="text-center"><button type="button" class="btn btn-sm btn-primary"
                                                data-bs-toggle="modal" data-bs-target="#modalSaldoData"><i
                                                    class="bx bx-plus me-1"></i></button></td>
                                        <td class="text-center"><button type="button" class="btn btn-sm btn-primary"
                                                data-bs-toggle="modal" data-bs-target="#modalSaldoDataKeluar"><i
                                                    class="bx bx-plus me-1"></i></button></td>

                                        <td></td>
                                        <td class="text-center"><button type="button" class="btn btn-sm btn-primary"
                                                data-bs-toggle="modal" data-bs-target="#modalDana"><i
                                                    class="bx bx-plus me-1"></i></button></td>
                                        <td class="text-center"><button type="button" class="btn btn-sm btn-primary"
                                                data-bs-toggle="modal" data-bs-target="#modalDanaKeluar"><i
                                                    class="bx bx-plus me-1"></i></button></td>
                                        <td colspan="2"></td>
                                    </tr>

                                    <!-- Tabungan -->
                                    <tr>
                                        <td class="ps-4">Tabungan</td>
                                        <td class="text-end">{{ number_format($tabunganSaldoMasuk, 0, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format($tabunganSaldoKeluar, 0, ',', '.') }}</td>
                                        <td class="text-end">
                                            <span class="btn-show-detail text-primary text-decoration-underline"
                                                style="cursor: pointer;"
                                                data-title="Detail Aktual Saldo Berjalan - DIV Tabungan"
                                                data-name="DIV Tabungan" data-mode="saldo"
                                                data-past-cash="{{ $divSummary['TABUNGAN']->past_cash ?? 0 }}"
                                                data-past-transfer="{{ $divSummary['TABUNGAN']->past_transfer ?? 0 }}"
                                                data-curr-saldo-cash="{{ $divSummary['TABUNGAN']->curr_saldo_cash ?? 0 }}"
                                                data-curr-saldo-transfer="{{ $divSummary['TABUNGAN']->curr_saldo_transfer ?? 0 }}"
                                                data-curr-harian-cash="0" data-curr-harian-transfer="0"
                                                data-items="{{ json_encode($divSummary['TABUNGAN']->items ?? []) }}">
                                                {{ number_format($tabunganSaldoMasuk - $tabunganSaldoKeluar, 0, ',', '.') }}
                                            </span>
                                        </td>
                                        <td class="text-end">{{ number_format($tabungan, 0, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format($tabunganKeluar, 0, ',', '.') }}</td>
                                        <td class="text-end">
                                            {{ number_format($tabungan - $tabunganKeluar, 0, ',', '.') }}
                                        </td>
                                        <td class="text-end fw-semibold">
                                            <span class="btn-show-detail text-primary text-decoration-underline"
                                                style="cursor: pointer;" data-title="Detail Total Aktual - DIV Tabungan"
                                                data-name="DIV Tabungan" data-mode="total"
                                                data-past-cash="{{ $divSummary['TABUNGAN']->past_cash ?? 0 }}"
                                                data-past-transfer="{{ $divSummary['TABUNGAN']->past_transfer ?? 0 }}"
                                                data-curr-saldo-cash="{{ $divSummary['TABUNGAN']->curr_saldo_cash ?? 0 }}"
                                                data-curr-saldo-transfer="{{ $divSummary['TABUNGAN']->curr_saldo_transfer ?? 0 }}"
                                                data-curr-harian-cash="{{ $divSummary['TABUNGAN']->curr_harian_cash ?? 0 }}"
                                                data-curr-harian-transfer="{{ $divSummary['TABUNGAN']->curr_harian_transfer ?? 0 }}"
                                                data-items="{{ json_encode($divSummary['TABUNGAN']->items ?? []) }}">
                                                {{ number_format($tabunganSaldoMasuk + $tabungan - $tabunganKeluar - $tabunganSaldoKeluar, 0, ',', '.') }}
                                            </span>
                                        </td>
                                    </tr>

                                    <!-- Cadangan -->
                                    <tr>
                                        <td class="ps-4">Cadangan</td>
                                        <td class="text-end">{{ number_format($cadanganSaldoMasuk, 0, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format($cadanganSaldoKeluar, 0, ',', '.') }}</td>
                                        <td class="text-end">
                                            <span class="btn-show-detail text-primary text-decoration-underline"
                                                style="cursor: pointer;"
                                                data-title="Detail Aktual Saldo Berjalan - DIV Cadangan"
                                                data-name="DIV Cadangan" data-mode="saldo"
                                                data-past-cash="{{ $divSummary['CADANGAN']->past_cash ?? 0 }}"
                                                data-past-transfer="{{ $divSummary['CADANGAN']->past_transfer ?? 0 }}"
                                                data-curr-saldo-cash="{{ $divSummary['CADANGAN']->curr_saldo_cash ?? 0 }}"
                                                data-curr-saldo-transfer="{{ $divSummary['CADANGAN']->curr_saldo_transfer ?? 0 }}"
                                                data-curr-harian-cash="0" data-curr-harian-transfer="0"
                                                data-items="{{ json_encode($divSummary['CADANGAN']->items ?? []) }}">
                                                {{ number_format($cadanganSaldoMasuk - $cadanganSaldoKeluar, 0, ',', '.') }}
                                            </span>
                                        </td>
                                        <td class="text-end">{{ number_format($cadangan, 0, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format($cadanganKeluar, 0, ',', '.') }}</td>
                                        <td class="text-end">
                                            {{ number_format($cadangan - $cadanganKeluar, 0, ',', '.') }}
                                        </td>
                                        <td class="text-end fw-semibold">
                                            <span class="btn-show-detail text-primary text-decoration-underline"
                                                style="cursor: pointer;" data-title="Detail Total Aktual - DIV Cadangan"
                                                data-name="DIV Cadangan" data-mode="total"
                                                data-past-cash="{{ $divSummary['CADANGAN']->past_cash ?? 0 }}"
                                                data-past-transfer="{{ $divSummary['CADANGAN']->past_transfer ?? 0 }}"
                                                data-curr-saldo-cash="{{ $divSummary['CADANGAN']->curr_saldo_cash ?? 0 }}"
                                                data-curr-saldo-transfer="{{ $divSummary['CADANGAN']->curr_saldo_transfer ?? 0 }}"
                                                data-curr-harian-cash="{{ $divSummary['CADANGAN']->curr_harian_cash ?? 0 }}"
                                                data-curr-harian-transfer="{{ $divSummary['CADANGAN']->curr_harian_transfer ?? 0 }}"
                                                data-items="{{ json_encode($divSummary['CADANGAN']->items ?? []) }}">
                                                {{ number_format($cadanganSaldoMasuk + $cadangan - $cadanganKeluar - $cadanganSaldoKeluar, 0, ',', '.') }}
                                            </span>
                                        </td>
                                    </tr>

                                    <!-- Dana Sefty -->
                                    <tr>
                                        <td class="ps-4">Dana Sefty</td>
                                        <td class="text-end">{{ number_format($danaSeftySaldoMasuk, 0, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format($danaSeftySaldoKeluar, 0, ',', '.') }}</td>
                                        <td class="text-end">
                                            <span class="btn-show-detail text-primary text-decoration-underline"
                                                style="cursor: pointer;"
                                                data-title="Detail Aktual Saldo Berjalan - DIV Dana Sefty"
                                                data-name="DIV Dana Sefty" data-mode="saldo"
                                                data-past-cash="{{ $divSummary['DANA SEFTY']->past_cash ?? 0 }}"
                                                data-past-transfer="{{ $divSummary['DANA SEFTY']->past_transfer ?? 0 }}"
                                                data-curr-saldo-cash="{{ $divSummary['DANA SEFTY']->curr_saldo_cash ?? 0 }}"
                                                data-curr-saldo-transfer="{{ $divSummary['DANA SEFTY']->curr_saldo_transfer ?? 0 }}"
                                                data-curr-harian-cash="0" data-curr-harian-transfer="0"
                                                data-items="{{ json_encode($divSummary['DANA SEFTY']->items ?? []) }}">
                                                {{ number_format($danaSeftySaldoMasuk - $danaSeftySaldoKeluar, 0, ',', '.') }}
                                            </span>
                                        </td>
                                        <td class="text-end">{{ number_format($danaSefty, 0, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format($danaSeftyKeluar, 0, ',', '.') }}</td>
                                        <td class="text-end">
                                            {{ number_format($danaSefty - $danaSeftyKeluar, 0, ',', '.') }}
                                        </td>
                                        <td class="text-end fw-semibold">
                                            <span class="btn-show-detail text-primary text-decoration-underline"
                                                style="cursor: pointer;" data-title="Detail Total Aktual - DIV Dana Sefty"
                                                data-name="DIV Dana Sefty" data-mode="total"
                                                data-past-cash="{{ $divSummary['DANA SEFTY']->past_cash ?? 0 }}"
                                                data-past-transfer="{{ $divSummary['DANA SEFTY']->past_transfer ?? 0 }}"
                                                data-curr-saldo-cash="{{ $divSummary['DANA SEFTY']->curr_saldo_cash ?? 0 }}"
                                                data-curr-saldo-transfer="{{ $divSummary['DANA SEFTY']->curr_saldo_transfer ?? 0 }}"
                                                data-curr-harian-cash="{{ $divSummary['DANA SEFTY']->curr_harian_cash ?? 0 }}"
                                                data-curr-harian-transfer="{{ $divSummary['DANA SEFTY']->curr_harian_transfer ?? 0 }}"
                                                data-items="{{ json_encode($divSummary['DANA SEFTY']->items ?? []) }}">
                                                {{ number_format($danaSeftySaldoMasuk + $danaSefty - $danaSeftyKeluar - $danaSeftySaldoKeluar, 0, ',', '.') }}
                                            </span>
                                        </td>
                                    </tr>

                                    <!-- TOTAL DIV -->
                                    <tr class="table-light">
                                        <td class="fw-bold text-end">TOTAL DIV</td>
                                        <td class="text-end fw-bold">
                                            {{ number_format($totalDivSaldoMasuk, 0, ',', '.') }}
                                        </td>
                                        <td class="text-end fw-bold">
                                            {{ number_format($totalDivSaldoKeluar, 0, ',', '.') }}
                                        </td>
                                        <td class="text-end fw-bold">
                                            <span class="btn-show-detail text-primary text-decoration-underline"
                                                style="cursor: pointer;"
                                                data-title="Detail Aktual Saldo Berjalan - TOTAL DIV"
                                                data-name="TOTAL DIV" data-mode="saldo"
                                                data-past-cash="{{ ($divSummary['TABUNGAN']->past_cash ?? 0) + ($divSummary['CADANGAN']->past_cash ?? 0) + ($divSummary['DANA SEFTY']->past_cash ?? 0) }}"
                                                data-past-transfer="{{ ($divSummary['TABUNGAN']->past_transfer ?? 0) + ($divSummary['CADANGAN']->past_transfer ?? 0) + ($divSummary['DANA SEFTY']->past_transfer ?? 0) }}"
                                                data-curr-saldo-cash="{{ ($divSummary['TABUNGAN']->curr_saldo_cash ?? 0) + ($divSummary['CADANGAN']->curr_saldo_cash ?? 0) + ($divSummary['DANA SEFTY']->curr_saldo_cash ?? 0) }}"
                                                data-curr-saldo-transfer="{{ ($divSummary['TABUNGAN']->curr_saldo_transfer ?? 0) + ($divSummary['CADANGAN']->curr_saldo_transfer ?? 0) + ($divSummary['DANA SEFTY']->curr_saldo_transfer ?? 0) }}"
                                                data-curr-harian-cash="0" data-curr-harian-transfer="0"
                                                data-items="{{ json_encode(array_merge($divSummary['TABUNGAN']->items ?? [], $divSummary['CADANGAN']->items ?? [], $divSummary['DANA SEFTY']->items ?? [])) }}">
                                                {{ number_format($totalDivSaldoMasuk - $totalDivSaldoKeluar, 0, ',', '.') }}
                                            </span>
                                        </td>
                                        <td class="text-end fw-bold">{{ number_format($totalDiv, 0, ',', '.') }}</td>
                                        <td class="text-end fw-bold">{{ number_format($totalDivKeluar, 0, ',', '.') }}
                                        </td>
                                        <td class="text-end fw-bold">
                                            {{ number_format($totalDiv - $totalDivKeluar, 0, ',', '.') }}</td>
                                        <td class="text-end fw-bold">
                                            <span class="btn-show-detail text-dark text-decoration-underline"
                                                style="cursor: pointer;" data-title="Detail Total Aktual - TOTAL DIV"
                                                data-name="TOTAL DIV" data-mode="total"
                                                data-past-cash="{{ ($divSummary['TABUNGAN']->past_cash ?? 0) + ($divSummary['CADANGAN']->past_cash ?? 0) + ($divSummary['DANA SEFTY']->past_cash ?? 0) }}"
                                                data-past-transfer="{{ ($divSummary['TABUNGAN']->past_transfer ?? 0) + ($divSummary['CADANGAN']->past_transfer ?? 0) + ($divSummary['DANA SEFTY']->past_transfer ?? 0) }}"
                                                data-curr-saldo-cash="{{ ($divSummary['TABUNGAN']->curr_saldo_cash ?? 0) + ($divSummary['CADANGAN']->curr_saldo_cash ?? 0) + ($divSummary['DANA SEFTY']->curr_saldo_cash ?? 0) }}"
                                                data-curr-saldo-transfer="{{ ($divSummary['TABUNGAN']->curr_saldo_transfer ?? 0) + ($divSummary['CADANGAN']->curr_saldo_transfer ?? 0) + ($divSummary['DANA SEFTY']->curr_saldo_transfer ?? 0) }}"
                                                data-curr-harian-cash="{{ ($divSummary['TABUNGAN']->curr_harian_cash ?? 0) + ($divSummary['CADANGAN']->curr_harian_cash ?? 0) + ($divSummary['DANA SEFTY']->curr_harian_cash ?? 0) }}"
                                                data-curr-harian-transfer="{{ ($divSummary['TABUNGAN']->curr_harian_transfer ?? 0) + ($divSummary['CADANGAN']->curr_harian_transfer ?? 0) + ($divSummary['DANA SEFTY']->curr_harian_transfer ?? 0) }}"
                                                data-items="{{ json_encode(array_merge($divSummary['TABUNGAN']->items ?? [], $divSummary['CADANGAN']->items ?? [], $divSummary['DANA SEFTY']->items ?? [])) }}">
                                                {{ number_format($totalDivSaldoMasuk + $totalDiv - $totalDivKeluar - $totalDivSaldoKeluar, 0, ',', '.') }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card mt-2">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Laporan Laba</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive text-nowrap" style="overflow-x: auto;">
                            <table class="table table-bordered align-middle table-sm">
                                <tbody>
                                    <!-- Category Header LABA -->
                                    <tr class="table-success">

                                        <td class="fw-bold text-dark">LABA</td>
                                        <td><button type="button" data-bs-toggle="modal"
                                                data-bs-target="#modalSaldoLaba" class="btn btn-sm btn-success">
                                                +
                                            </button></td>
                                        <td><button type="button" data-bs-toggle="modal"
                                                data-bs-target="#modalSaldoLabaKeluar" class="btn btn-sm btn-success">
                                                +
                                            </button></td>
                                        <td></td>

                                        <td></td>
                                        <td><button type="button" data-bs-toggle="modal"
                                                data-bs-target="#modalPenarikan" class="btn btn-sm btn-success btn-tarik">
                                                + Tarik Laba
                                            </button></td>
                                        <td></td>
                                        <td></td>
                                    </tr>

                                    @php
                                        $totalLaba =
                                            $totalPemasukan - $gajiCapster - $totalOperasional - $totalDiv - $prodak;
                                    @endphp

                                    <!-- TOTAL LABA BERSIH -->
                                    <tr class="table-success fw-bold">
                                        <td>TOTAL LABA BERSIH</td>
                                        <td class="text-center">-</td>
                                        <td class="text-center">-</td>
                                        <td class="text-center">-</td>
                                        <td class="text-end">{{ number_format($totalLaba, 0, ',', '.') }}</td>
                                        <td class="text-center">-</td>
                                        <td class="text-end">{{ number_format($totalLaba, 0, ',', '.') }}</td>
                                        <td class="text-end">
                                            <span class="btn-show-detail text-dark text-decoration-underline"
                                                style="cursor: pointer;"
                                                data-title="Detail Total Aktual - TOTAL LABA BERSIH"
                                                data-name="TOTAL LABA BERSIH" data-mode="total" data-past-cash="0"
                                                data-past-transfer="0" data-curr-saldo-cash="0"
                                                data-curr-saldo-transfer="0" data-curr-harian-cash="{{ $totalLaba }}"
                                                data-curr-harian-transfer="0" data-items='[]'>
                                                {{ number_format($totalLaba, 0, ',', '.') }}
                                            </span>
                                        </td>
                                    </tr>

                                    <!-- Looping Pembagian Investor -->
                                    @foreach ($investorData as $investor)
                                        @php
                                            $persen = $investor->persenInvestor->sum('persen') ?? 0;
                                            $bagianLaba = ($persen / 100) * $totalLaba;

                                            $dtpenarikanLaba = 0;
                                            foreach ($penarikanLaba as $d) {
                                                if ($investor->id == $d->investor_id) {
                                                    $dtpenarikanLaba += $d->jml_penarikan_laba;
                                                }
                                            }

                                            $dtSaldoLaba = 0;
                                            foreach ($saldoLaba as $d) {
                                                if ($investor->id == $d->investor_id) {
                                                    $dtSaldoLaba += $d->jml_penarikan_laba;
                                                }
                                            }

                                            $dtSaldoLabaKeluar = 0;
                                            foreach ($saldoLabaKeluar as $d) {
                                                if ($investor->id == $d->investor_id) {
                                                    $dtSaldoLabaKeluar += $d->jml_penarikan_laba;
                                                }
                                            }

                                            $sLaba = $saldoLaba->firstWhere('investor_id', $investor->id);
                                        @endphp
                                        <tr>
                                            <td class="ps-4">
                                                {{ $investor->nm_investor }} ({{ $persen }}%)
                                            </td>
                                            <td class="text-end">{{ number_format($dtSaldoLaba, 0, ',', '.') }}</td>
                                            <td class="text-end">{{ number_format($dtSaldoLabaKeluar, 0, ',', '.') }}
                                            </td>
                                            <td class="text-end">
                                                <span class="btn-show-detail text-primary text-decoration-underline"
                                                    style="cursor: pointer;"
                                                    data-title="Detail Aktual Saldo Berjalan - {{ $investor->nm_investor }}"
                                                    data-name="{{ $investor->nm_investor }} (Saldo Berjalan)"
                                                    data-mode="saldo" data-past-cash="{{ $sLaba->past_cash ?? 0 }}"
                                                    data-past-transfer="{{ $sLaba->past_transfer ?? 0 }}"
                                                    data-curr-saldo-cash="{{ $sLaba->curr_saldo_cash ?? 0 }}"
                                                    data-curr-saldo-transfer="{{ $sLaba->curr_saldo_transfer ?? 0 }}"
                                                    data-curr-harian-cash="0" data-curr-harian-transfer="0"
                                                    data-items="{{ json_encode($sLaba->items ?? []) }}">
                                                    {{ number_format($dtSaldoLaba - $dtSaldoLabaKeluar, 0, ',', '.') }}
                                                </span>
                                            </td>
                                            <td class="text-end">{{ number_format($bagianLaba, 0, ',', '.') }}</td>
                                            <td class="text-end">{{ number_format($dtpenarikanLaba, 0, ',', '.') }}</td>
                                            <td class="text-end">
                                                {{ number_format($bagianLaba - $dtpenarikanLaba, 0, ',', '.') }}</td>
                                            <td class="text-end fw-semibold">
                                                <span class="btn-show-detail text-primary text-decoration-underline"
                                                    style="cursor: pointer;"
                                                    data-title="Detail Total Aktual - {{ $investor->nm_investor }}"
                                                    data-name="{{ $investor->nm_investor }}" data-mode="total"
                                                    data-past-cash="{{ $sLaba->past_cash ?? 0 }}"
                                                    data-past-transfer="{{ $sLaba->past_transfer ?? 0 }}"
                                                    data-curr-saldo-cash="{{ $sLaba->curr_saldo_cash ?? 0 }}"
                                                    data-curr-saldo-transfer="{{ $sLaba->curr_saldo_transfer ?? 0 }}"
                                                    data-curr-harian-cash="{{ $bagianLaba - $dtpenarikanLaba }}"
                                                    data-curr-harian-transfer="0"
                                                    data-items="{{ json_encode($sLaba->items ?? []) }}">
                                                    {{ number_format($bagianLaba - $dtpenarikanLaba + $dtSaldoLaba - $dtSaldoLabaKeluar, 0, ',', '.') }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Modal Dana (DIV) -->
    <div class="modal fade" id="modalDana" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDanaTitle">Data Dana (DIV)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('laporan-keuangan.store-dana') }}" method="POST">
                        @csrf
                        <h6 class="fw-semibold mb-3">Tambah Data Dana (DIV)</h6>
                        <input type="hidden" name="jenis_dana" value="1">
                        <input type="hidden" name="jenis_saldo" value="2">
                        <div class="row g-3">

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Cabang</label>
                                <select name="cabang_id" class="form-select" required>
                                    <option value="">Pilih Cabang</option>
                                    @foreach ($cabang as $c)
                                        <option value="{{ $c->id }}">{{ $c->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_dana" class="form-label">Tanggal</label>
                                <input type="date" id="tanggal_dana" name="tanggal" class="form-control"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="jenis_dana" class="form-label">Jenis Dana</label>
                                <select id="jenis_dana" name="jenis" class="form-select" required>
                                    <option value="" disabled selected>Pilih Jenis...</option>
                                    <option value="TABUNGAN">TABUNGAN</option>
                                    <option value="CADANGAN">CADANGAN</option>
                                    <option value="DANA SEFTY">DANA SEFTY</option>
                                </select>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="form-group">
                                    <label for="">Jenis Pembayaran</label>
                                    <select name="pembayaran_id" class="form-control" required>
                                        <option value="">Pilih Pembayaran</option>
                                        <option value="1">Cash</option>
                                        <option value="2">Transfer/QRIS</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="jumlah_dana" class="form-label">Jumlah (Rp)</label>
                                <input type="number" id="jumlah_dana" name="jumlah" class="form-control"
                                    placeholder="Contoh: 500000" min="0" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="keterangan_dana" class="form-label">Keterangan</label>
                            <textarea id="keterangan_dana" name="keterangan" class="form-control" rows="2"
                                placeholder="Masukkan keterangan dana..." required></textarea>
                        </div>
                        <div class="d-flex justify-content-end mb-3">
                            <button type="submit" class="btn btn-primary">Simpan Data</button>
                        </div>
                    </form>

                    <hr class="my-4">

                    <h6 class="fw-semibold mb-3">Detail Data DIV (Berdasarkan Filter Tanggal)</h6>
                    <div class="table-responsive text-nowrap">
                        <table class="table table-sm table-bordered table-striped align-middle">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jenis</th>
                                    <th>Pembayaran</th>
                                    <th>Jumlah</th>
                                    <th>CABANG</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($detailDana as $item)
                                    <tr>
                                        <td class="text-center">{{ \Carbon\Carbon::parse($item->tgl)->format('d-m-Y') }}
                                        </td>
                                        <td class="text-center fw-bold">{{ $item->jenis }}</td>
                                        <td class="text-center fw-bold">
                                            {{ $item->pembayaran_id == 1 ? 'Cash' : 'Transfer' }}</td>
                                        <td class="text-end fw-semibold">Rp
                                            {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                                        <td>{{ $item->cabang->nama ?? '-' }}</td>
                                        <td>{{ $item->cabang->nama ?? '-' }}</td>
                                        <td>{{ $item->ket }}</td>
                                        <td class="text-center">
                                            <form action="{{ route('laporan-keuangan.destroy-dana', $item->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Tidak ada data dana (DIV) pada
                                            rentang tanggal ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Dana Keluar (DIV) -->
    <div class="modal fade" id="modalDanaKeluar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDanaKeluarTitle">Data Dana Keluar (DIV)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('laporan-keuangan.store-dana') }}" method="POST">
                        @csrf
                        <h6 class="fw-semibold mb-3">Tambah Data Dana (DIV)</h6>
                        <input type="hidden" name="jenis_dana" value="2">
                        <input type="hidden" name="jenis_saldo" value="2">
                        <div class="row g-3">

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Cabang</label>
                                <select name="cabang_id" class="form-select" required>
                                    <option value="">Pilih Cabang</option>
                                    @foreach ($cabang as $c)
                                        <option value="{{ $c->id }}">{{ $c->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_dana_keluar" class="form-label">Tanggal</label>
                                <input type="date" id="tanggal_dana_keluar" name="tanggal" class="form-control"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="jenis_dana_keluar" class="form-label">Jenis Dana</label>
                                <select id="jenis_dana_keluar" name="jenis" class="form-select" required>
                                    <option value="" disabled selected>Pilih Jenis...</option>
                                    <option value="TABUNGAN">TABUNGAN</option>
                                    <option value="CADANGAN">CADANGAN</option>
                                    <option value="DANA SEFTY">DANA SEFTY</option>
                                </select>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="form-group">
                                    <label for="">Jenis Pembayaran</label>
                                    <select name="pembayaran_id" class="form-control" required>
                                        <option value="">Pilih Pembayaran</option>
                                        <option value="1">Cash</option>
                                        <option value="2">Transfer/QRIS</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="jumlah_dana_keluar" class="form-label">Jumlah (Rp)</label>
                                <input type="number" id="jumlah_dana_keluar" name="jumlah" class="form-control"
                                    placeholder="Contoh: 500000" min="0" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="keterangan_dana_keluar" class="form-label">Keterangan</label>
                            <textarea id="keterangan_dana_keluar" name="keterangan" class="form-control" rows="2"
                                placeholder="Masukkan keterangan dana..." required></textarea>
                        </div>
                        <div class="d-flex justify-content-end mb-3">
                            <button type="submit" class="btn btn-primary">Simpan Data</button>
                        </div>
                    </form>

                    <hr class="my-4">

                    <h6 class="fw-semibold mb-3">Detail Data DIV (Berdasarkan Filter Tanggal)</h6>
                    <div class="table-responsive text-nowrap">
                        <table class="table table-sm table-bordered table-striped align-middle">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jenis</th>
                                    <th>Pembayaran</th>
                                    <th>Jumlah</th>
                                    <th>CABANG</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($detailDanaKeluar as $item)
                                    <tr>
                                        <td class="text-center">{{ \Carbon\Carbon::parse($item->tgl)->format('d-m-Y') }}
                                        </td>
                                        <td class="text-center fw-bold">{{ $item->jenis }}</td>
                                        <td class="text-center fw-bold">
                                            {{ $item->pembayaran_id == 1 ? 'Cash' : 'Transfer' }}</td>
                                        <td class="text-end fw-semibold">Rp
                                            {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                                        <td>{{ $item->cabang->nama ?? '-' }}</td>
                                        <td>{{ $item->cabang->nama ?? '-' }}</td>
                                        <td>{{ $item->ket }}</td>
                                        <td class="text-center">
                                            <form action="{{ route('laporan-keuangan.destroy-dana', $item->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Tidak ada data dana (DIV) pada
                                            rentang tanggal ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Dana Keluar (DIV) -->
    <div class="modal fade" id="modalSaldoData" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalSaldoDataTitle">Saldo Masuk (DIV)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('laporan-keuangan.store-dana') }}" method="POST">
                        @csrf
                        <h6 class="fw-semibold mb-3">Tambah Saldo (DIV)</h6>
                        <input type="hidden" name="jenis_dana" value="1">
                        <input type="hidden" name="jenis_saldo" value="1">
                        <div class="row g-3">

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Cabang</label>
                                <select name="cabang_id" class="form-select" required>
                                    <option value="">Pilih Cabang</option>
                                    @foreach ($cabang as $c)
                                        <option value="{{ $c->id }}">{{ $c->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_dana_keluar" class="form-label">Tanggal</label>
                                <input type="date" id="tanggal_dana_keluar" name="tanggal" class="form-control"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="jenis_dana_keluar" class="form-label">Jenis Dana</label>
                                <select id="jenis_dana_keluar" name="jenis" class="form-select" required>
                                    <option value="" disabled selected>Pilih Jenis...</option>
                                    <option value="TABUNGAN">TABUNGAN</option>
                                    <option value="CADANGAN">CADANGAN</option>
                                    <option value="DANA SEFTY">DANA SEFTY</option>
                                </select>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="form-group">
                                    <label for="">Jenis Pembayaran</label>
                                    <select name="pembayaran_id" class="form-control" required>
                                        <option value="">Pilih Pembayaran</option>
                                        <option value="1">Cash</option>
                                        <option value="2">Transfer/QRIS</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="jumlah_dana_keluar" class="form-label">Jumlah (Rp)</label>
                                <input type="number" id="jumlah_dana_keluar" name="jumlah" class="form-control"
                                    placeholder="Contoh: 500000" min="0" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="keterangan_dana_keluar" class="form-label">Keterangan</label>
                            <textarea id="keterangan_dana_keluar" name="keterangan" class="form-control" rows="2"
                                placeholder="Masukkan keterangan dana..." required></textarea>
                        </div>
                        <div class="d-flex justify-content-end mb-3">
                            <button type="submit" class="btn btn-primary">Simpan Data</button>
                        </div>
                    </form>

                    <hr class="my-4">

                    <h6 class="fw-semibold mb-3">Detail Data DIV (Berdasarkan Filter Tanggal)</h6>
                    <div class="table-responsive text-nowrap">
                        <table class="table table-sm table-bordered table-striped align-middle">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jenis</th>
                                    <th>Pembayaran</th>
                                    <th>Jumlah</th>
                                    <th>CABANG</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($detailDanaSaldoMasuk as $item)
                                    <tr>
                                        <td class="text-center">{{ \Carbon\Carbon::parse($item->tgl)->format('d-m-Y') }}
                                        </td>
                                        <td class="text-center fw-bold">{{ $item->jenis }}</td>
                                        <td class="text-center fw-bold">
                                            {{ $item->pembayaran_id == 1 ? 'Cash' : 'Transfer' }}</td>
                                        <td class="text-end fw-semibold">Rp
                                            {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                                        <td>{{ $item->cabang->nama ?? '-' }}</td>
                                        <td>{{ $item->cabang->nama ?? '-' }}</td>
                                        <td>{{ $item->ket }}</td>
                                        <td class="text-center">
                                            <form action="{{ route('laporan-keuangan.destroy-dana', $item->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Tidak ada data dana (DIV) pada
                                            rentang tanggal ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalSaldoDataKeluar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalSaldoDataKeluarTitle">Saldo Keluar (DIV)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('laporan-keuangan.store-dana') }}" method="POST">
                        @csrf
                        <h6 class="fw-semibold mb-3">Tambah Saldo (DIV)</h6>
                        <input type="hidden" name="jenis_dana" value="2">
                        <input type="hidden" name="jenis_saldo" value="1">
                        <div class="row g-3">

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Cabang</label>
                                <select name="cabang_id" class="form-select" required>
                                    <option value="">Pilih Cabang</option>
                                    @foreach ($cabang as $c)
                                        <option value="{{ $c->id }}">{{ $c->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_dana_keluar" class="form-label">Tanggal</label>
                                <input type="date" id="tanggal_dana_keluar" name="tanggal" class="form-control"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="jenis_dana_keluar" class="form-label">Jenis Dana</label>
                                <select id="jenis_dana_keluar" name="jenis" class="form-select" required>
                                    <option value="" disabled selected>Pilih Jenis...</option>
                                    <option value="TABUNGAN">TABUNGAN</option>
                                    <option value="CADANGAN">CADANGAN</option>
                                    <option value="DANA SEFTY">DANA SEFTY</option>
                                </select>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="form-group">
                                    <label for="">Jenis Pembayaran</label>
                                    <select name="pembayaran_id" class="form-control" required>
                                        <option value="">Pilih Pembayaran</option>
                                        <option value="1">Cash</option>
                                        <option value="2">Transfer/QRIS</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="jumlah_dana_keluar" class="form-label">Jumlah (Rp)</label>
                                <input type="number" id="jumlah_dana_keluar" name="jumlah" class="form-control"
                                    placeholder="Contoh: 500000" min="0" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="keterangan_dana_keluar" class="form-label">Keterangan</label>
                            <textarea id="keterangan_dana_keluar" name="keterangan" class="form-control" rows="2"
                                placeholder="Masukkan keterangan dana..." required></textarea>
                        </div>
                        <div class="d-flex justify-content-end mb-3">
                            <button type="submit" class="btn btn-primary">Simpan Data</button>
                        </div>
                    </form>

                    <hr class="my-4">

                    <h6 class="fw-semibold mb-3">Detail Data DIV (Berdasarkan Filter Tanggal)</h6>
                    <div class="table-responsive text-nowrap">
                        <table class="table table-sm table-bordered table-striped align-middle">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jenis</th>
                                    <th>Pembayaran</th>
                                    <th>Jumlah</th>
                                    <th>CABANG</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($detailDanaSaldoKeluar as $item)
                                    <tr>
                                        <td class="text-center">{{ \Carbon\Carbon::parse($item->tgl)->format('d-m-Y') }}
                                        </td>
                                        <td class="text-center fw-bold">{{ $item->jenis }}</td>
                                        <td class="text-center fw-bold">
                                            {{ $item->pembayaran_id == 1 ? 'Cash' : 'Transfer' }}</td>
                                        <td class="text-end fw-semibold">Rp
                                            {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                                        <td>{{ $item->cabang->nama ?? '-' }}</td>
                                        <td>{{ $item->cabang->nama ?? '-' }}</td>
                                        <td>{{ $item->ket }}</td>
                                        <td class="text-center">
                                            <form action="{{ route('laporan-keuangan.destroy-dana', $item->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Tidak ada data dana (DIV) pada
                                            rentang tanggal ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Pendapatan DLL -->
    <div class="modal fade" id="modalTambahPendapatanDll" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahPendapatanDllTitle">Pendapatan DLL</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form action="{{ route('laporan-keuangan.store-pendapatan-dll') }}" method="POST">
                        @csrf
                        <h6 class="fw-semibold mb-3">Tambah Data Pendapatan DLL</h6>
                        <div class="row g-3">

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Cabang</label>
                                <select name="cabang_id" class="form-select" required>
                                    <option value="">Pilih Cabang</option>
                                    @foreach ($cabang as $c)
                                        <option value="{{ $c->id }}">{{ $c->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <input type="date" id="tanggal" name="tanggal" class="form-control"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="jumlah" class="form-label">Jumlah (Rp)</label>
                                <input type="number" id="jumlah" name="jumlah" class="form-control"
                                    placeholder="Contoh: 150000" min="0" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea id="keterangan" name="keterangan" class="form-control" rows="2"
                                placeholder="Masukkan keterangan pendapatan..." required></textarea>
                        </div>
                        <div class="d-flex justify-content-end mb-3">
                            <button type="submit" class="btn btn-primary">Simpan Data</button>
                        </div>

                    </form>

                    <hr class="my-4">

                    <h6 class="fw-semibold mb-3">Detail Data Pendapatan DLL (Berdasarkan Filter Tanggal)</h6>
                    <div class="table-responsive text-nowrap">
                        <table class="table table-sm table-bordered table-striped align-middle">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jumlah</th>
                                    <th>CABANG</th>
                                    <th>Keterangan</th>
                                    <th>User Input</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($detailPendapatan as $item)
                                    <tr>
                                        <td class="text-center">{{ \Carbon\Carbon::parse($item->tgl)->format('d-m-Y') }}
                                        </td>
                                        <td class="text-end fw-semibold">Rp
                                            {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                                        <td>{{ $item->cabang->nama ?? '-' }}</td>
                                        <td>{{ $item->cabang->nama ?? '-' }}</td>
                                        <td>{{ $item->ket }}</td>
                                        <td class="text-center">{{ $item->user->name ?? '-' }}</td>
                                        <td class="text-center">
                                            <form
                                                action="{{ route('laporan-keuangan.destroy-pendapatan-dll', $item->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Tidak ada data pendapatan DLL
                                            pada rentang tanggal ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Produk -->
    <div class="modal fade" id="modalProduk" tabindex="-1" aria-labelledby="modalProdukLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalProdukLabel">Kelola Data Pembelian Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- FORM INPUT DATA -->
                    <form action="{{ route('laporan-keuangan.store-produk') }}" method="POST">
                        @csrf
                        <div class="row g-3">

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Cabang</label>
                                <select name="cabang_id" class="form-select" required>
                                    <option value="">Pilih Cabang</option>
                                    @foreach ($cabang as $c)
                                        <option value="{{ $c->id }}">{{ $c->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="tanggal_produk" class="form-label">Tanggal</label>
                                <input type="date" name="tgl" id="tanggal_produk" class="form-control"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="service_id" class="form-label">Service (Produk)</label>
                                <select name="service_id" id="service_id" class="form-select" required>
                                    <option value="">-- Pilih Service Produk --</option>
                                    @foreach ($listServiceProduk as $srv)
                                        <option value="{{ $srv->id }}">
                                            {{ $srv->nm_service ?? ($srv->nama_service ?? $srv->nama) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="qty" class="form-label">Qty</label>
                                <input type="number" name="qty" id="qty" class="form-control"
                                    min="1" value="1" required>
                            </div>
                            <div class="col-md-6">
                                <label for="jumlah_produk" class="form-label">Jumlah (Rp)</label>
                                <input type="number" name="jumlah" id="jumlah_produk" class="form-control"
                                    placeholder="Contoh: 50000" required>
                            </div>
                        </div>
                        <div class="mt-3 text-end">
                            <button type="submit" class="btn btn-primary">Simpan Data</button>
                        </div>
                    </form>

                    <hr class="my-4">

                    <!-- TABEL DETAIL DATA BULAN / TANGGAL FILTER -->
                    <h6>Detail Data Produk (Berdasarkan Filter Tanggal)</h6>
                    <div class="table-responsive text-nowrap mt-2">
                        <table class="table table-sm table-bordered table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Service</th>
                                    <th>Qty</th>
                                    <th>Jumlah (Rp)</th>
                                    <th>CABANG</th>
                                    <th>Input By</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($detailProduk as $item)
                                    <tr>
                                        <td>{{ date('d-m-Y', strtotime($item->tgl)) }}</td>
                                        <td>{{ $item->service->nm_service ?? ($item->service->nama_service ?? '-') }}</td>
                                        <td>{{ $item->qty }}</td>
                                        <td>{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                                        <td>{{ $item->cabang->nama ?? '-' }}</td>
                                        <td>{{ $item->cabang->nama ?? '-' }}</td>
                                        <td>{{ $item->user->name ?? '-' }}</td>
                                        <td class="text-center">
                                            <form action="{{ route('laporan-keuangan.destroy-produk', $item->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">Belum ada data pada periode
                                            ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Jasa Layanan -->
    <div class="modal fade" id="modalDetailJasaLayanan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Jasa Layanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Service</th>
                                    <th>Total Qty</th>
                                    <th>Total Penjualan (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $no = 1;
                                    $totalSemua = 0;
                                @endphp
                                @forelse($detailJasaLayanan as $item)
                                    @php $totalSemua += $item->total_penjualan; @endphp
                                    <tr>
                                        <td class="text-center">{{ $no++ }}</td>
                                        <td>{{ $item->service->nm_service ?? ($item->service->nama_service ?? 'Unknown') }}
                                        </td>
                                        <td class="text-center">{{ $item->total_qty }}</td>
                                        <td class="text-end">{{ number_format($item->total_penjualan, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Tidak ada data Jasa Layanan
                                            pada
                                            periode ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td colspan="3" class="text-end">TOTAL KESELURUHAN</td>
                                    <td class="text-end">{{ number_format($totalSemua, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Penjualan Prodak -->
    <div class="modal fade" id="modalDetailPenjualanProdak" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Penjualan Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Service (Produk)</th>
                                    <th>Total Qty</th>
                                    <th>Total Penjualan (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $no = 1;
                                    $totalSemuaProduk = 0;
                                @endphp
                                @forelse($detailPenjualanProdak as $item)
                                    @php $totalSemuaProduk += $item->total_penjualan; @endphp
                                    <tr>
                                        <td class="text-center">{{ $no++ }}</td>
                                        <td>{{ $item->service->nm_service ?? ($item->service->nama_service ?? 'Unknown') }}
                                        </td>
                                        <td class="text-center">{{ $item->total_qty }}</td>
                                        <td class="text-end">{{ number_format($item->total_penjualan, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Tidak ada data Penjualan
                                            Produk
                                            pada periode ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td colspan="3" class="text-end">TOTAL KESELURUHAN</td>
                                    <td class="text-end">{{ number_format($totalSemuaProduk, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Saldo Gaji Capster -->
    <div class="modal fade" id="modalDetailGajiCapster" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Saldo Gaji/Komisi Capster</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Capster</th>
                                    <th>Total Saldo (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $no = 1;
                                    $totalSaldoCapster = 0;
                                @endphp
                                @forelse($detailGajiCapster as $item)
                                    @php $totalSaldoCapster += $item->total_gaji; @endphp
                                    <tr>
                                        <td class="text-center">{{ $no++ }}</td>
                                        <td>{{ $item->karyawan->nm_karyawan ?? ($item->karyawan->nama ?? ($item->karyawan->name ?? 'Unknown')) }}
                                        </td>
                                        <td class="text-end">{{ number_format($item->total_gaji, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">Tidak ada data Saldo Capster.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td colspan="2" class="text-end">TOTAL KESELURUHAN</td>
                                    <td class="text-end">{{ number_format($totalSaldoCapster, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Pengeluaran Capster -->
    <div class="modal fade" id="modalDetailPengeluaranCapster" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Pengeluaran Gaji Capster</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Capster</th>
                                    <th>Total Kasbon (Rp)</th>
                                    <th>Total Ambil Gaji (Rp)</th>
                                    <th>Total Pengeluaran (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $no = 1;
                                    $totalPengeluaranSemua = 0;
                                @endphp
                                @forelse($detailPengeluaranCapster as $karyawanId => $item)
                                    @php $totalPengeluaranSemua += $item['total']; @endphp
                                    <tr>
                                        <td class="text-center">{{ $no++ }}</td>
                                        <td>{{ $item['nama'] }}</td>
                                        <td class="text-end">{{ number_format($item['kasbon'], 0, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format($item['ambil_gaji'], 0, ',', '.') }}</td>
                                        <td class="text-end fw-semibold text-danger">
                                            {{ number_format($item['total'], 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Tidak ada data Pengeluaran
                                            Capster.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td colspan="4" class="text-end">TOTAL PENGELUARAN KESELURUHAN</td>
                                    <td class="text-end text-danger">
                                        {{ number_format($totalPengeluaranSemua, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Form Penarikan -->
    <div class="modal fade" id="modalSaldoLaba" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Tambah Saldo Laba</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('penarikanLaba') }}" method="POST">
                        @csrf
                        <input type="hidden" name="jenis" value="1">
                        <div class="row">

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Cabang</label>
                                <select name="cabang_id" class="form-select" required>
                                    <option value="">Pilih Cabang</option>
                                    @foreach ($cabang as $c)
                                        <option value="{{ $c->id }}">{{ $c->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3 col-6">
                                <label for="tgl" class="form-label">Tanggal</label>
                                <input type="date" class="form-control" name="tgl"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="mb-3 col-6">
                                <label for="tgl" class="form-label">Investor</label>
                                <select name="investor_id" class="form-control">
                                    <option disabled>Pilih Investor</option>
                                    @foreach ($investorData as $i)
                                        <option value="{{ $i->id }}">{{ $i->nm_investor }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-6 mb-3">
                                <div class="form-group">
                                    <label for="">Jenis Pembayaran</label>
                                    <select name="pembayaran_id" class="form-control" required>
                                        <option value="">Pilih Pembayaran</option>
                                        <option value="1">Cash</option>
                                        <option value="2">Transfer/QRIS</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3 col-6">
                                <label for="jumlah" class="form-label">Jumlah (Rp)</label>
                                <input type="number" class="form-control" name="jumlah" min="1"
                                    placeholder="Masukkan nominal" required>
                            </div>
                        </div>


                        <button type="submit" class="btn btn-primary">Simpan</button>

                    </form>

                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Pembayaran</th>
                                    <th>Investor</th>
                                    <th>CABANG</th>
                                    <th>Cabang</th>
                                    <th>Jumlah</th>
                                    <th>Hapus</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($detailSaldoLaba as $d)
                                    <tr>
                                        <td>{{ date('d-m-Y', strtotime($d->tgl)) }}</td>
                                        <td>{{ $d->pembayaran_id == 1 ? 'Cash' : 'Transfer' }}</td>
                                        <td>{{ $d->investor->nm_investor }}</td>
                                        <td>{{ $d->cabang->nama ?? '-' }}</td>
                                        <td>{{ $d->cabang->nama ?? '-' }}</td>
                                        <td class="text-end">{{ number_format($d->jumlah, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            <form action="{{ route('deletePenarikanDana', $d->id) }}" method="POST"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>

                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="modalSaldoLabaKeluar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Tambah Saldo Keluar Laba</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('penarikanLaba') }}" method="POST">
                        @csrf
                        <input type="hidden" name="jenis" value="2">
                        <div class="row">

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Cabang</label>
                                <select name="cabang_id" class="form-select" required>
                                    <option value="">Pilih Cabang</option>
                                    @foreach ($cabang as $c)
                                        <option value="{{ $c->id }}">{{ $c->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3 col-6">
                                <label for="tgl" class="form-label">Tanggal</label>
                                <input type="date" class="form-control" name="tgl"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="mb-3 col-6">
                                <label for="tgl" class="form-label">Investor</label>
                                <select name="investor_id" class="form-control">
                                    <option disabled>Pilih Investor</option>
                                    @foreach ($investorData as $i)
                                        <option value="{{ $i->id }}">{{ $i->nm_investor }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-6 mb-3">
                                <div class="form-group">
                                    <label for="">Jenis Pembayaran</label>
                                    <select name="pembayaran_id" class="form-control" required>
                                        <option value="">Pilih Pembayaran</option>
                                        <option value="1">Cash</option>
                                        <option value="2">Transfer/QRIS</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3 col-6">
                                <label for="jumlah" class="form-label">Jumlah (Rp)</label>
                                <input type="number" class="form-control" name="jumlah" min="1"
                                    placeholder="Masukkan nominal" required>
                            </div>
                        </div>


                        <button type="submit" class="btn btn-primary">Simpan</button>

                    </form>

                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Pembayaran</th>
                                    <th>Investor</th>
                                    <th>CABANG</th>
                                    <th>Cabang</th>
                                    <th>Jumlah</th>
                                    <th>Hapus</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($detailSaldoLabaKeluar as $d)
                                    <tr>
                                        <td>{{ date('d-m-Y', strtotime($d->tgl)) }}</td>
                                        <td>{{ $d->pembayaran_id == 1 ? 'Cash' : 'Transfer' }}</td>
                                        <td>{{ $d->investor->nm_investor }}</td>
                                        <td>{{ $d->cabang->nama ?? '-' }}</td>
                                        <td>{{ $d->cabang->nama ?? '-' }}</td>
                                        <td class="text-end">{{ number_format($d->jumlah, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            <form action="{{ route('deletePenarikanDana', $d->id) }}" method="POST"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>

                </div>

            </div>
        </div>
    </div>


    <div class="modal fade" id="modalPenarikan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Penarikan Laba<span id="namaInvestorModal"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('penarikanLaba') }}" method="POST">
                        @csrf
                        <input type="hidden" name="jenis" value="4">
                        <div class="row">

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Cabang</label>
                                <select name="cabang_id" class="form-select" required>
                                    <option value="">Pilih Cabang</option>
                                    @foreach ($cabang as $c)
                                        <option value="{{ $c->id }}">{{ $c->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3 col-6">
                                <label for="tgl" class="form-label">Tanggal Penarikan</label>
                                <input type="date" class="form-control" name="tgl"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="mb-3 col-6">
                                <label for="tgl" class="form-label">Investor</label>
                                <select name="investor_id" class="form-control">
                                    <option disabled>Pilih Investor</option>
                                    @foreach ($investorData as $i)
                                        <option value="{{ $i->id }}">{{ $i->nm_investor }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-6 mb-3">
                                <div class="form-group">
                                    <label for="">Jenis Pembayaran</label>
                                    <select name="pembayaran_id" class="form-control" required>
                                        <option value="">Pilih Pembayaran</option>
                                        <option value="1">Cash</option>
                                        <option value="2">Transfer/QRIS</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3 col-6">
                                <label for="jumlah" class="form-label">Jumlah Penarikan (Rp)</label>
                                <input type="number" class="form-control" name="jumlah" min="1"
                                    placeholder="Masukkan nominal" required>
                            </div>
                        </div>


                        <button type="submit" class="btn btn-primary">Simpan Penarikan</button>

                    </form>

                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Pembayaran</th>
                                    <th>Investor</th>
                                    <th>CABANG</th>
                                    <th>Cabang</th>
                                    <th>Jumlah</th>
                                    <th>Hapus</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($detailPenarikanLaba as $d)
                                    <tr>
                                        <td>{{ date('d-m-Y', strtotime($d->tgl)) }}</td>
                                        <td>{{ $d->pembayaran_id == 1 ? 'Cash' : 'Transfer' }}</td>
                                        <td>{{ $d->investor->nm_investor }}</td>
                                        <td>{{ $d->cabang->nama ?? '-' }}</td>
                                        <td>{{ $d->cabang->nama ?? '-' }}</td>
                                        <td class="text-end">{{ number_format($d->jumlah, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            <form action="{{ route('deletePenarikanDana', $d->id) }}" method="POST"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>

                </div>

            </div>
        </div>
    </div>

    {{-- endpenarikanlaba --}}

    <form id="form_add_pengeluaran" method="POST" action="{{ route('addPengeluaran') }}">
        @csrf
        <div class="modal fade" id="modal_add_pengeluaran" tabindex="-1"
            aria-labelledby="modal_add_pengeluaranLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal_add_pengeluaranLabel">Tambah Data Pengeluaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">

                            <input type="hidden" value="2" name="jenis">

                            <div class="col-6 mb-2">
                                <div class="form-group">
                                    <label for="">Tanggal</label>
                                    <input type="date" name="tgl" class="form-control" required>
                                </div>
                            </div>

                            <div class="col-6 mb-2">
                                <div class="form-group">
                                    <label for="">Cabang</label>
                                    <select name="cabang_id" class="form-control" required>
                                        <option value="">Pilih Cabang</option>
                                        @foreach ($cabang as $c)
                                            <option value="{{ $c->id }}">{{ $c->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-6 mb-2">
                                <div class="form-group">
                                    <label for="">Jenis Pembayaran</label>
                                    <select name="pembayaran_id" class="form-control" required>
                                        <option value="">Pilih Pembayaran</option>
                                        <option value="1">Cash</option>
                                        <option value="2">Transfer/QRIS</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-6 mb-2">
                                <div class="form-group">
                                    <label for="">Akun</label>
                                    <select name="akun_id" class="form-control" required>
                                        <option value="">Pilih Akun</option>
                                        @foreach ($akun as $a)
                                            <option value="{{ $a->id }}">{{ $a->nm_akun }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-6 mb-2">
                                <div class="form-group">
                                    <label for="">Jumlah</label>
                                    <input type="number" name="jumlah" class="form-control" required>
                                </div>
                            </div>

                            <div class="col-6 mb-2">
                                <div class="form-group">
                                    <label for="">Keterangan</label>
                                    <input type="text" name="ket" class="form-control" required>
                                </div>
                            </div>


                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm text-center" width="100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Tanggal</th>
                                        <th>Cabang</th>
                                        <th>Akun</th>
                                        <th>Jenis<br>Pembayaran</th>
                                        <th>Jumlah</th>
                                        <th>CABANG</th>
                                        <th>Keterangan</th>
                                        <th>User</th>
                                        <th>Hapus</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $i = 1;
                                        $total = 0;
                                    @endphp
                                    @foreach ($detail_jurnal_keluar as $d)
                                        @php
                                            $total += $d->jumlah;
                                        @endphp
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ date('d/m/Y', strtotime($d->tgl)) }}</td>
                                            <td>{{ $d->cabang->nama }}</td>
                                            <td>{{ $d->akun->nm_akun }}</td>
                                            <td>{{ $d->pembayaran_id == 1 ? 'Cash' : 'Transfer/QRIS' }}</td>
                                            <td>{{ number_format($d->jumlah, 0) }}</td>
                                            <td>{{ $d->ket }}</td>
                                            <td>{{ $d->user->name }}</td>
                                            <td>
                                                <a href="{{ route('deletePengeluaran', $d->id) }}"
                                                    onclick="return confirm('Apakah anda yakin ingin menghapus data?');"
                                                    class="btn btn-sm btn-primary"><i class="bx bx-trash"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4"><b>Total</b></td>
                                        <td><b>{{ number_format($total, 0) }}</b></td>
                                        <td colspan="3"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="btn_add_pengeluaran">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <form id="form_add_pemasukan" method="POST" action="{{ route('addPengeluaran') }}">
        @csrf
        <div class="modal fade" id="modal_add_pemasukan" tabindex="-1" aria-labelledby="modal_add_pemasukanLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal_add_pemasukanLabel">Tambah Data Pemasukan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">

                            <input type="hidden" value="1" name="jenis">

                            <div class="col-6 mb-2">
                                <div class="form-group">
                                    <label for="">Tanggal</label>
                                    <input type="date" name="tgl" class="form-control" required>
                                </div>
                            </div>

                            <div class="col-6 mb-2">
                                <div class="form-group">
                                    <label for="">Cabang</label>
                                    <select name="cabang_id" class="form-control" required>
                                        <option value="">Pilih Cabang</option>
                                        @foreach ($cabang as $c)
                                            <option value="{{ $c->id }}">{{ $c->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-6 mb-2">
                                <div class="form-group">
                                    <label for="">Akun</label>
                                    <select name="akun_id" class="form-control" required>
                                        <option value="">Pilih Akun</option>
                                        @foreach ($akun as $a)
                                            <option value="{{ $a->id }}">{{ $a->nm_akun }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-6 mb-2">
                                <div class="form-group">
                                    <label for="">Jenis Pembayaran</label>
                                    <select name="pembayaran_id" class="form-control" required>
                                        <option value="">Pilih Pembayaran</option>
                                        <option value="1">Cash</option>
                                        <option value="2">Transfer/QRIS</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-6 mb-2">
                                <div class="form-group">
                                    <label for="">Jumlah</label>
                                    <input type="number" name="jumlah" class="form-control" required>
                                </div>
                            </div>

                            <div class="col-6 mb-2">
                                <div class="form-group">
                                    <label for="">Keterangan</label>
                                    <input type="text" name="ket" class="form-control" required>
                                </div>
                            </div>


                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm text-center" width="100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Tanggal</th>
                                        <th>Cabang</th>
                                        <th>Akun</th>
                                        <th>Jenis<br>Pembayaran</th>
                                        <th>Jumlah</th>
                                        <th>CABANG</th>
                                        <th>Keterangan</th>
                                        <th>User</th>
                                        <th>Hapus</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $i = 1;
                                        $total = 0;
                                    @endphp
                                    @foreach ($detail_jurnal_masuk as $d)
                                        @php
                                            $total += $d->jumlah;
                                        @endphp
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ date('d/m/Y', strtotime($d->tgl)) }}</td>
                                            <td>{{ $d->cabang->nama }}</td>
                                            <td>{{ $d->akun->nm_akun }}</td>
                                            <td>{{ $d->pembayaran_id == 1 ? 'Cash' : 'Transfer/QRIS' }}</td>
                                            <td>{{ number_format($d->jumlah, 0) }}</td>
                                            <td>{{ $d->ket }}</td>
                                            <td>{{ $d->user->name }}</td>
                                            <td>
                                                <a href="{{ route('deletePengeluaran', $d->id) }}"
                                                    onclick="return confirm('Apakah anda yakin ingin menghapus data?');"
                                                    class="btn btn-sm btn-primary"><i class="bx bx-trash"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4"><b>Total</b></td>
                                        <td><b>{{ number_format($total, 0) }}</b></td>
                                        <td colspan="3"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="btn_add_pemasukan">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Modal Saldo Masuk Berjalan Operasional -->
    <form id="form_add_saldo_oprasional" method="POST" action="{{ route('storeSaldoOperasional') }}">
        @csrf
        <div class="modal fade" id="modal_add_saldo_oprasional" tabindex="-1"
            aria-labelledby="modal_add_saldo_oprasionalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal_add_saldo_oprasionalLabel">Tambah Saldo Masuk Berjalan
                            (Operasional)</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" value="1" name="jenis">

                            <div class="col-6 mb-2">
                                <div class="form-group">
                                    <label for="">Tanggal</label>
                                    <input type="date" name="tgl" class="form-control"
                                        value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>

                            <div class="col-6 mb-2">
                                <div class="form-group">
                                    <label for="">Cabang</label>
                                    <select name="cabang_id" class="form-control" required>
                                        <option value="">Pilih Cabang</option>
                                        @foreach ($cabang as $c)
                                            <option value="{{ $c->id }}">{{ $c->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-6 mb-2">
                                <div class="form-group">
                                    <label for="">Akun</label>
                                    <select name="akun_id" class="form-control" required>
                                        <option value="">Pilih Akun</option>
                                        @foreach ($akun as $a)
                                            <option value="{{ $a->id }}">{{ $a->nm_akun }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-6 mb-2">
                                <div class="form-group">
                                    <label for="">Jenis Pembayaran</label>
                                    <select name="pembayaran_id" class="form-control" required>
                                        <option value="">Pilih Pembayaran</option>
                                        <option value="1">Cash</option>
                                        <option value="2">Transfer/QRIS</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-6 mb-2">
                                <div class="form-group">
                                    <label for="">Jumlah</label>
                                    <input type="number" name="jumlah" min="1" class="form-control"
                                        required>
                                </div>
                            </div>

                            <div class="col-6 mb-2">
                                <div class="form-group">
                                    <label for="">Keterangan</label>
                                    <input type="text" name="ket" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive mt-3">
                            <table class="table table-sm text-center" width="100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Tanggal</th>
                                        <th>Cabang</th>
                                        <th>Akun</th>
                                        <th>Jenis<br>Pembayaran</th>
                                        <th>Jumlah</th>
                                        <th>CABANG</th>
                                        <th>Keterangan</th>
                                        <th>User</th>
                                        <th>Hapus</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $i = 1;
                                        $totalSaldoOpMasuk = 0;
                                    @endphp
                                    @foreach ($detail_saldo_oprasional_masuk as $d)
                                        @php
                                            $totalSaldoOpMasuk += $d->jumlah;
                                        @endphp
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ date('d/m/Y', strtotime($d->tgl)) }}</td>
                                            <td>{{ $d->cabang->nama ?? '-' }}</td>
                                            <td>{{ $d->akun->nm_akun ?? '-' }}</td>
                                            <td>{{ $d->pembayaran_id == 1 ? 'Cash' : 'Transfer/QRIS' }}</td>
                                            <td>{{ number_format($d->jumlah, 0) }}</td>
                                            <td>{{ $d->ket }}</td>
                                            <td>{{ $d->user->name ?? '-' }}</td>
                                            <td>
                                                <a href="{{ route('deleteSaldoOperasional', $d->id) }}"
                                                    onclick="return confirm('Apakah anda yakin ingin menghapus data?');"
                                                    class="btn btn-sm btn-primary"><i class="bx bx-trash"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="5"><b>Total</b></td>
                                        <td><b>{{ number_format($totalSaldoOpMasuk, 0) }}</b></td>
                                        <td colspan="3"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="btn_add_saldo_oprasional">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Modal Saldo Pengeluaran Berjalan Operasional -->
    <form id="form_add_pengeluaran_oprasional" method="POST" action="{{ route('storeSaldoOperasional') }}">
        @csrf
        <div class="modal fade" id="modal_add_pengeluaran_oprasional" tabindex="-1"
            aria-labelledby="modal_add_pengeluaran_oprasionalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal_add_pengeluaran_oprasionalLabel">Tambah Saldo Pengeluaran
                            Berjalan (Operasional)</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" value="2" name="jenis">

                            <div class="col-6 mb-2">
                                <div class="form-group">
                                    <label for="">Tanggal</label>
                                    <input type="date" name="tgl" class="form-control"
                                        value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>

                            <div class="col-6 mb-2">
                                <div class="form-group">
                                    <label for="">Cabang</label>
                                    <select name="cabang_id" class="form-control" required>
                                        <option value="">Pilih Cabang</option>
                                        @foreach ($cabang as $c)
                                            <option value="{{ $c->id }}">{{ $c->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-6 mb-2">
                                <div class="form-group">
                                    <label for="">Akun</label>
                                    <select name="akun_id" class="form-control" required>
                                        <option value="">Pilih Akun</option>
                                        @foreach ($akun as $a)
                                            <option value="{{ $a->id }}">{{ $a->nm_akun }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-6 mb-2">
                                <div class="form-group">
                                    <label for="">Jenis Pembayaran</label>
                                    <select name="pembayaran_id" class="form-control" required>
                                        <option value="">Pilih Pembayaran</option>
                                        <option value="1">Cash</option>
                                        <option value="2">Transfer/QRIS</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-6 mb-2">
                                <div class="form-group">
                                    <label for="">Jumlah</label>
                                    <input type="number" name="jumlah" min="1" class="form-control"
                                        required>
                                </div>
                            </div>

                            <div class="col-6 mb-2">
                                <div class="form-group">
                                    <label for="">Keterangan</label>
                                    <input type="text" name="ket" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive mt-3">
                            <table class="table table-sm text-center" width="100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Tanggal</th>
                                        <th>Cabang</th>
                                        <th>Akun</th>
                                        <th>Jenis<br>Pembayaran</th>
                                        <th>Jumlah</th>
                                        <th>CABANG</th>
                                        <th>Keterangan</th>
                                        <th>User</th>
                                        <th>Hapus</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $i = 1;
                                        $totalSaldoOpKeluar = 0;
                                    @endphp
                                    @foreach ($detail_saldo_oprasional_keluar as $d)
                                        @php
                                            $totalSaldoOpKeluar += $d->jumlah;
                                        @endphp
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ date('d/m/Y', strtotime($d->tgl)) }}</td>
                                            <td>{{ $d->cabang->nama ?? '-' }}</td>
                                            <td>{{ $d->akun->nm_akun ?? '-' }}</td>
                                            <td>{{ $d->pembayaran_id == 1 ? 'Cash' : 'Transfer/QRIS' }}</td>
                                            <td>{{ number_format($d->jumlah, 0) }}</td>
                                            <td>{{ $d->ket }}</td>
                                            <td>{{ $d->user->name ?? '-' }}</td>
                                            <td>
                                                <a href="{{ route('deleteSaldoOperasional', $d->id) }}"
                                                    onclick="return confirm('Apakah anda yakin ingin menghapus data?');"
                                                    class="btn btn-sm btn-primary"><i class="bx bx-trash"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="5"><b>Total</b></td>
                                        <td><b>{{ number_format($totalSaldoOpKeluar, 0) }}</b></td>
                                        <td colspan="3"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary"
                            id="btn_add_pengeluaran_oprasional">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Modal Saldo Masuk Berjalan Gaji -->
    <form id="form_add_saldo_gaji" method="POST" action="{{ route('storeSaldoGaji') }}">
        @csrf
        <div class="modal fade" id="modal_add_saldo_gaji" tabindex="-1" aria-labelledby="modal_add_saldo_gajiLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal_add_saldo_gajiLabel">Tambah Saldo Masuk Berjalan (Gaji)</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" value="1" name="jenis">

                            <div class="col-6 mb-2">
                                <div class="form-group">
                                    <label for="">Tanggal</label>
                                    <input type="date" name="tgl" class="form-control"
                                        value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>

                            <div class="col-6 mb-2">
                                <div class="form-group">
                                    <label for="">Cabang</label>
                                    <select name="cabang_id" class="form-control" required>
                                        <option value="">Pilih Cabang</option>
                                        @foreach ($cabang as $c)
                                            <option value="{{ $c->id }}">{{ $c->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-6 mb-2">
                                <div class="form-group">
                                    <label for="">Jenis Pembayaran</label>
                                    <select name="pembayaran_id" class="form-control" required>
                                        <option value="">Pilih Pembayaran</option>
                                        <option value="1">Cash</option>
                                        <option value="2">Transfer/QRIS</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-6 mb-2">
                                <div class="form-group">
                                    <label for="">Jumlah</label>
                                    <input type="number" name="jumlah" min="1" class="form-control"
                                        required>
                                </div>
                            </div>

                            <div class="col-12 mb-2">
                                <div class="form-group">
                                    <label for="">Keterangan</label>
                                    <input type="text" name="ket" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive mt-3">
                            <table class="table table-sm text-center" width="100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Tanggal</th>
                                        <th>Cabang</th>
                                        <th>Jenis<br>Pembayaran</th>
                                        <th>Jumlah</th>
                                        <th>CABANG</th>
                                        <th>Keterangan</th>
                                        <th>User</th>
                                        <th>Hapus</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $i = 1;
                                        $totalSaldoGajiMasuk = 0;
                                    @endphp
                                    @foreach ($detail_saldo_gaji_masuk as $d)
                                        @php
                                            $totalSaldoGajiMasuk += $d->jumlah;
                                        @endphp
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ date('d/m/Y', strtotime($d->tgl)) }}</td>
                                            <td>{{ $d->cabang->nama ?? '-' }}</td>
                                            <td>{{ $d->pembayaran_id == 1 ? 'Cash' : 'Transfer/QRIS' }}</td>
                                            <td>{{ number_format($d->jumlah, 0) }}</td>
                                            <td>{{ $d->ket }}</td>
                                            <td>{{ $d->user->name ?? '-' }}</td>
                                            <td>
                                                <a href="{{ route('deleteSaldoGaji', $d->id) }}"
                                                    onclick="return confirm('Apakah anda yakin ingin menghapus data?');"
                                                    class="btn btn-sm btn-primary"><i class="bx bx-trash"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4"><b>Total</b></td>
                                        <td><b>{{ number_format($totalSaldoGajiMasuk, 0) }}</b></td>
                                        <td colspan="3"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="btn_add_saldo_gaji">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Modal Saldo Pengeluaran Berjalan Gaji -->
    <form id="form_add_pengeluaran_gaji" method="POST" action="{{ route('storeSaldoGaji') }}">
        @csrf
        <div class="modal fade" id="modal_add_pengeluaran_gaji" tabindex="-1"
            aria-labelledby="modal_add_pengeluaran_gajiLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal_add_pengeluaran_gajiLabel">Tambah Saldo Pengeluaran Berjalan
                            (Gaji)</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" value="2" name="jenis">

                            <div class="col-6 mb-2">
                                <div class="form-group">
                                    <label for="">Tanggal</label>
                                    <input type="date" name="tgl" class="form-control"
                                        value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>

                            <div class="col-6 mb-2">
                                <div class="form-group">
                                    <label for="">Cabang</label>
                                    <select name="cabang_id" class="form-control" required>
                                        <option value="">Pilih Cabang</option>
                                        @foreach ($cabang as $c)
                                            <option value="{{ $c->id }}">{{ $c->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-6 mb-2">
                                <div class="form-group">
                                    <label for="">Jenis Pembayaran</label>
                                    <select name="pembayaran_id" class="form-control" required>
                                        <option value="">Pilih Pembayaran</option>
                                        <option value="1">Cash</option>
                                        <option value="2">Transfer/QRIS</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-6 mb-2">
                                <div class="form-group">
                                    <label for="">Jumlah</label>
                                    <input type="number" name="jumlah" min="1" class="form-control"
                                        required>
                                </div>
                            </div>

                            <div class="col-12 mb-2">
                                <div class="form-group">
                                    <label for="">Keterangan</label>
                                    <input type="text" name="ket" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive mt-3">
                            <table class="table table-sm text-center" width="100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Tanggal</th>
                                        <th>Cabang</th>
                                        <th>Jenis<br>Pembayaran</th>
                                        <th>Jumlah</th>
                                        <th>CABANG</th>
                                        <th>Keterangan</th>
                                        <th>User</th>
                                        <th>Hapus</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $i = 1;
                                        $totalSaldoGajiKeluar = 0;
                                    @endphp
                                    @foreach ($detail_saldo_gaji_keluar as $d)
                                        @php
                                            $totalSaldoGajiKeluar += $d->jumlah;
                                        @endphp
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ date('d/m/Y', strtotime($d->tgl)) }}</td>
                                            <td>{{ $d->cabang->nama ?? '-' }}</td>
                                            <td>{{ $d->pembayaran_id == 1 ? 'Cash' : 'Transfer/QRIS' }}</td>
                                            <td>{{ number_format($d->jumlah, 0) }}</td>
                                            <td>{{ $d->ket }}</td>
                                            <td>{{ $d->user->name ?? '-' }}</td>
                                            <td>
                                                <a href="{{ route('deleteSaldoGaji', $d->id) }}"
                                                    onclick="return confirm('Apakah anda yakin ingin menghapus data?');"
                                                    class="btn btn-sm btn-primary"><i class="bx bx-trash"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4"><b>Total</b></td>
                                        <td><b>{{ number_format($totalSaldoGajiKeluar, 0) }}</b></td>
                                        <td colspan="3"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="btn_add_pengeluaran_gaji">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Modal Detail Aktual (Reusable) -->
    <div class="modal fade" id="modalDetailAktual" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white py-2">
                    <h5 class="modal-title text-white" id="modalDetailAktualTitle">Detail Perhitungan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-primary py-2 px-3 mb-3 d-flex justify-content-between align-items-center">
                        <div>
                            <strong id="modalDetailItemName" class="fs-6">-</strong>
                            <div class="small text-muted" id="modalDetailPeriod">Periode: -</div>
                        </div>
                        <span class="badge bg-primary fs-6" id="modalDetailBadgeMode">Aktual</span>
                    </div>

                    <!-- Ringkasan Saldo Berjalan & Transaksi Periode Ini -->
                    <div class="row g-2 mb-3">
                        <div class="col-md-4">
                            <div class="card border border-primary h-100 shadow-none">
                                <div class="card-header bg-light py-2 text-center fw-bold text-primary">
                                    <i class="bx bx-history me-1"></i> Saldo Berjalan (Lalu)
                                </div>
                                <div class="card-body p-2 small">
                                    <div class="d-flex justify-content-between py-1 border-bottom">
                                        <span>Cash:</span>
                                        <span class="fw-semibold" id="detailPastCash">Rp 0</span>
                                    </div>
                                    <div class="d-flex justify-content-between py-1 border-bottom">
                                        <span>Transfer:</span>
                                        <span class="fw-semibold" id="detailPastTransfer">Rp 0</span>
                                    </div>
                                    <div class="d-flex justify-content-between pt-1 fw-bold text-primary">
                                        <span>Subtotal:</span>
                                        <span id="detailPastTotal">Rp 0</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card border border-info h-100 shadow-none">
                                <div class="card-header bg-light py-2 text-center fw-bold text-info">
                                    <i class="bx bx-plus-circle me-1"></i> Transaksi Periode Ini
                                </div>
                                <div class="card-body p-2 small">
                                    <div class="d-flex justify-content-between py-1 border-bottom">
                                        <span>Cash:</span>
                                        <span class="fw-semibold" id="detailCurrCash">Rp 0</span>
                                    </div>
                                    <div class="d-flex justify-content-between py-1 border-bottom">
                                        <span>Transfer:</span>
                                        <span class="fw-semibold" id="detailCurrTransfer">Rp 0</span>
                                    </div>
                                    <div class="d-flex justify-content-between pt-1 fw-bold text-info">
                                        <span>Subtotal:</span>
                                        <span id="detailCurrTotal">Rp 0</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card border border-success h-100 shadow-none">
                                <div class="card-header bg-light py-2 text-center fw-bold text-success">
                                    <i class="bx bx-check-double me-1"></i> Total Aktual
                                </div>
                                <div class="card-body p-2 small">
                                    <div class="d-flex justify-content-between py-1 border-bottom">
                                        <span>Total Cash:</span>
                                        <span class="fw-semibold" id="detailFinalCash">Rp 0</span>
                                    </div>
                                    <div class="d-flex justify-content-between py-1 border-bottom">
                                        <span>Total Transfer:</span>
                                        <span class="fw-semibold" id="detailFinalTransfer">Rp 0</span>
                                    </div>
                                    <div class="d-flex justify-content-between pt-1 fw-bold text-success">
                                        <span>Grand Total:</span>
                                        <span id="detailFinalTotal">Rp 0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rincian Item Transaksi Periode Ini -->
                    <h6 class="fw-bold mb-2"><i class="bx bx-list-ul me-1"></i> Rincian Transaksi Periode Ini</h6>
                    <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                        <table class="table table-bordered table-sm align-middle text-nowrap mb-0"
                            id="tableDetailItems">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>CABANG</th>
                                    <th>Keterangan</th>
                                    <th>Metode</th>
                                    <th>Tipe</th>
                                    <th class="text-end">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyDetailItems">
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-2">Tidak ada data transaksi</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    @section('script')
        <script>
            $(document).ready(function() {

                <?php if(session('success')): ?>
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    icon: 'success',
                    title: '<?= session('success') ?>'
                });
                <?php endif; ?>

                <?php if(session('error_kota')): ?>
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    icon: 'error',
                    title: "{{ session('error_kota') }}"
                });
                <?php endif; ?>

                <?php if($errors->any()): ?>
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    icon: 'error',
                    title: ' Ada data yang tidak sesuai, periksa kembali'
                });
                <?php endif; ?>

                $(document).on('click', '.btn-show-detail', function(e) {
                    e.preventDefault();
                    var title = $(this).data('title') || 'Detail Perhitungan';
                    var name = $(this).data('name') || '-';
                    var mode = $(this).data('mode') || 'total';
                    var period =
                        "{{ date('d M Y', strtotime($startDate)) }} s/d {{ date('d M Y', strtotime($endDate)) }}";

                    var pastCash = parseFloat($(this).attr('data-past-cash')) || 0;
                    var pastTransfer = parseFloat($(this).attr('data-past-transfer')) || 0;
                    var pastTotal = pastCash + pastTransfer;

                    var currCash = 0;
                    var currTransfer = 0;
                    if (mode === 'saldo') {
                        currCash = parseFloat($(this).attr('data-curr-saldo-cash')) || 0;
                        currTransfer = parseFloat($(this).attr('data-curr-saldo-transfer')) || 0;
                    } else {
                        var currSaldoCash = parseFloat($(this).attr('data-curr-saldo-cash')) || 0;
                        var currSaldoTransfer = parseFloat($(this).attr('data-curr-saldo-transfer')) || 0;
                        var currHarianCash = parseFloat($(this).attr('data-curr-harian-cash')) || 0;
                        var currHarianTransfer = parseFloat($(this).attr('data-curr-harian-transfer')) || 0;
                        currCash = currSaldoCash + currHarianCash;
                        currTransfer = currSaldoTransfer + currHarianTransfer;
                    }
                    var currTotal = currCash + currTransfer;

                    var finalCash = pastCash + currCash;
                    var finalTransfer = pastTransfer + currTransfer;
                    var finalTotal = finalCash + finalTransfer;

                    function formatRupiah(val) {
                        var prefix = val < 0 ? '-Rp ' : 'Rp ';
                        return prefix + Math.abs(val).toLocaleString('id-ID');
                    }

                    $('#modalDetailAktualTitle').text(title);
                    $('#modalDetailItemName').text(name);
                    $('#modalDetailPeriod').text('Periode: ' + period);
                    $('#modalDetailBadgeMode').text(mode === 'saldo' ? 'Aktual Saldo Berjalan' :
                    'Total Aktual');

                    $('#detailPastCash').text(formatRupiah(pastCash));
                    $('#detailPastTransfer').text(formatRupiah(pastTransfer));
                    $('#detailPastTotal').text(formatRupiah(pastTotal));

                    $('#detailCurrCash').text(formatRupiah(currCash));
                    $('#detailCurrTransfer').text(formatRupiah(currTransfer));
                    $('#detailCurrTotal').text(formatRupiah(currTotal));

                    $('#detailFinalCash').text(formatRupiah(finalCash));
                    $('#detailFinalTransfer').text(formatRupiah(finalTransfer));
                    $('#detailFinalTotal').text(formatRupiah(finalTotal));

                    var rawItems = $(this).attr('data-items');
                    var items = [];
                    if (rawItems) {
                        try {
                            items = typeof rawItems === 'string' ? JSON.parse(rawItems) : rawItems;
                        } catch (err) {
                            console.error(err);
                            items = [];
                        }
                    }

                    var $tbody = $('#tbodyDetailItems');
                    $tbody.empty();
                    if (items && items.length > 0) {
                        items.forEach(function(it) {
                            var badgeBayar = it.pembayaran === 'Transfer' ? 'bg-label-info' :
                                'bg-label-success';
                            var badgeJenis = it.jenis === 'Masuk' ? 'bg-label-primary' :
                                'bg-label-danger';
                            var row = '<tr>' +
                                '<td>' + (it.tgl || '-') + '</td>' +
                                '<td>' + (it.ket || '-') + '</td>' +
                                '<td><span class="badge ' + badgeBayar + '">' + (it.pembayaran ||
                                    'Cash') + '</span></td>' +
                                '<td><span class="badge ' + badgeJenis + '">' + (it.jenis || '-') +
                                '</span></td>' +
                                '<td class="text-end fw-semibold">' + formatRupiah(it.jumlah || 0) +
                                '</td>' +
                                '</tr>';
                            $tbody.append(row);
                        });
                    } else {
                        $tbody.append(
                            '<tr><td colspan="5" class="text-center text-muted py-3">Tidak ada data rincian transaksi periode ini</td></tr>'
                            );
                    }

                    $('#modalDetailAktual').modal('show');
                });

                $(document).on('submit', '#form_add_pengeluaran', function(event) {

                    $('#btn_add_pengeluaran').attr('disabled', true);
                    $('#btn_add_pengeluaran').html('Loading..');

                });

                $(document).on('submit', '#form_add_pemasukan', function(event) {

                    $('#btn_add_pemasukan').attr('disabled', true);
                    $('#btn_add_pemasukan').html('Loading..');

                });

                $(document).on('submit', '#form_add_saldo_oprasional', function(event) {

                    $('#btn_add_saldo_oprasional').attr('disabled', true);
                    $('#btn_add_saldo_oprasional').html('Loading..');

                });

                $(document).on('submit', '#form_add_pengeluaran_oprasional', function(event) {

                    $('#btn_add_pengeluaran_oprasional').attr('disabled', true);
                    $('#btn_add_pengeluaran_oprasional').html('Loading..');

                });

                $(document).on('submit', '#form_add_saldo_gaji', function(event) {

                    $('#btn_add_saldo_gaji').attr('disabled', true);
                    $('#btn_add_saldo_gaji').html('Loading..');

                });

                $(document).on('submit', '#form_add_pengeluaran_gaji', function(event) {

                    $('#btn_add_pengeluaran_gaji').attr('disabled', true);
                    $('#btn_add_pengeluaran_gaji').html('Loading..');

                });

            });
        </script>
        @include('laporan_keuangan.mutasi_kas_modal')
@endsection



    @include('laporan_keuangan.mutasi_kas_modal')
@endsection
