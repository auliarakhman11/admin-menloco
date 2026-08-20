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
                                <div class="col-md-4">
                                    <label for="start_date" class="form-label">Tanggal Mulai</label>
                                    <input type="date" id="start_date" name="start_date" class="form-control"
                                        value="{{ $startDate }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="end_date" class="form-label">Tanggal Selesai</label>
                                    <input type="date" id="end_date" name="end_date" class="form-control"
                                        value="{{ $endDate }}" required>
                                </div>
                                <div class="col-md-4 d-flex gap-2">
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
                    </div>
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

                                <!-- Jasa Layanan -->
                                <tr>
                                    <td class="ps-4 d-flex justify-content-between align-items-center">
                                        <span>Jasa Layanan</span>
                                    </td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-end"><a href="#modalDetailJasaLayanan"
                                            data-bs-toggle="modal">{{ number_format($jasaLayanan, 0, ',', '.') }}</a></td>
                                    <td class="text-end">{{ number_format($komisiService, 0, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($jasaLayanan - $komisiService, 0, ',', '.') }}
                                    </td>
                                    <td class="text-end fw-semibold">
                                        {{ number_format($jasaLayanan - $komisiService, 0, ',', '.') }}</td>
                                </tr>

                                <!-- Penjualan Prodak -->
                                <tr>
                                    <td class="ps-4 d-flex justify-content-between align-items-center">
                                        <span>Penjualan Prodak</span>
                                        <button type="button" class="btn btn-sm btn-primary ms-2" data-bs-toggle="modal"
                                            data-bs-target="#modalProduk"><i class="bx bx-plus me-1"></i> Pembelian</button>
                                    </td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-end"><a href="#modalDetailPenjualanProdak"
                                            data-bs-toggle="modal">{{ number_format($penjualanProdak, 0, ',', '.') }}</a>
                                    </td>
                                    <td class="text-end">{{ number_format($prodak + $komisiProduk, 0, ',', '.') }}</td>
                                    <td class="text-end">
                                        {{ number_format($penjualanProdak - $prodak - $komisiProduk, 0, ',', '.') }}</td>
                                    <td class="text-end fw-semibold">
                                        {{ number_format($penjualanProdak - $prodak - $komisiProduk, 0, ',', '.') }}
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
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-end">{{ number_format($pendapatanDll, 0, ',', '.') }}</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-end fw-semibold">{{ number_format($pendapatanDll, 0, ',', '.') }}</td>
                                </tr>

                                <!-- TOTAL PEMASUKAN -->
                                <tr class="table-primary fw-bold">
                                    <td>TOTAL PEMASUKAN</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-end">{{ number_format($totalPemasukan, 0, ',', '.') }}</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-end">{{ number_format($totalPemasukan, 0, ',', '.') }}</td>
                                </tr>

                                <!-- Category Header POKOK -->
                                <tr class="table-secondary">
                                    <td colspan="8" class="fw-bold text-dark">POKOK</td>
                                </tr>

                                <!-- Baris GAJI/KOMISI CAPSTER -->
                                <tr>
                                    <td class="fw-bold ps-4">Gaji/Komisi Capster</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
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
                                        {{ number_format($totalAktualCapster, 0, ',', '.') }}</td>
                                </tr>

                                {{-- <!-- Baris PRODAK -->
                                <tr>
                                    <td class="ps-4 d-flex justify-content-between align-items-center">
                                        <span>Prodak</span>
                                        <button type="button" class="btn btn-sm btn-primary ms-2" data-bs-toggle="modal"
                                            data-bs-target="#modalProduk"><i class="bx bx-plus me-1"></i></button>
                                    </td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-end">{{ number_format($prodak, 0, ',', '.') }}</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-end fw-semibold">{{ number_format($prodak, 0, ',', '.') }}</td>
                                </tr> --}}

                                <!-- TOTAL POKOK -->
                                <tr class="table-light">
                                    <td class="fw-bold text-end">TOTAL POKOK</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-end fw-bold">{{ number_format($totalPokok, 0, ',', '.') }}</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-end fw-bold">{{ number_format($totalPokok, 0, ',', '.') }}</td>
                                </tr>

                                <!-- Category Header OPERASIONAL -->
                                <tr class="table-secondary">
                                    <td colspan="8" class="fw-bold text-dark">OPERASIONAL</td>
                                </tr>

                                <!-- Looping Data OPERASIONAL -->
                                @foreach ($operasional as $item)
                                    <tr>
                                        <td class="ps-4">{{ $item->akun->nm_akun ?? 'Tidak Diketahui' }}</td>
                                        <td class="text-center">-</td>
                                        <td class="text-center">-</td>
                                        <td class="text-center">-</td>
                                        <td class="text-end">{{ number_format($item->total_jumlah, 0, ',', '.') }}</td>
                                        <td class="text-center">-</td>
                                        <td class="text-center">-</td>
                                        <td class="text-end fw-semibold">Rp
                                            {{ number_format($item->total_jumlah, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach

                                <!-- TOTAL OPERASIONAL -->
                                <tr class="table-light">
                                    <td class="fw-bold text-end">TOTAL OPERASIONAL</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-end fw-bold">{{ number_format($totalOperasional, 0, ',', '.') }}</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-end fw-bold">{{ number_format($totalOperasional, 0, ',', '.') }}</td>
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
                                        {{ number_format($tabunganSaldoMasuk - $tabunganSaldoKeluar, 0, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($tabungan, 0, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($tabunganKeluar, 0, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($tabungan - $tabunganKeluar, 0, ',', '.') }}
                                    </td>
                                    <td class="text-end fw-semibold">
                                        {{ number_format($tabunganSaldoMasuk + $tabungan - $tabunganKeluar - $tabunganSaldoKeluar, 0, ',', '.') }}
                                    </td>
                                </tr>

                                <!-- Cadangan -->
                                <tr>
                                    <td class="ps-4">Cadangan</td>
                                    <td class="text-end">{{ number_format($cadanganSaldoMasuk, 0, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($cadanganSaldoKeluar, 0, ',', '.') }}</td>
                                    <td class="text-end">
                                        {{ number_format($cadanganSaldoMasuk - $cadanganSaldoKeluar, 0, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($cadangan, 0, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($cadanganKeluar, 0, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($cadangan - $cadanganKeluar, 0, ',', '.') }}
                                    </td>
                                    <td class="text-end fw-semibold">
                                        {{ number_format($cadanganSaldoMasuk + $cadangan - $cadanganKeluar - $cadanganSaldoKeluar, 0, ',', '.') }}
                                    </td>
                                </tr>

                                <!-- Dana Sefty -->
                                <tr>
                                    <td class="ps-4">Dana Sefty</td>
                                    <td class="text-end">{{ number_format($danaSeftySaldoMasuk, 0, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($danaSeftySaldoKeluar, 0, ',', '.') }}</td>
                                    <td class="text-end">
                                        {{ number_format($danaSeftySaldoMasuk - $danaSeftySaldoKeluar, 0, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($danaSefty, 0, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($danaSeftyKeluar, 0, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($danaSefty - $danaSeftyKeluar, 0, ',', '.') }}
                                    </td>
                                    <td class="text-end fw-semibold">
                                        {{ number_format($danaSeftySaldoMasuk + $danaSefty - $danaSeftyKeluar - $danaSeftySaldoKeluar, 0, ',', '.') }}
                                    </td>
                                </tr>

                                <!-- TOTAL DIV -->
                                <tr class="table-light">
                                    <td class="fw-bold text-end">TOTAL DIV</td>
                                    <td class="text-end fw-bold">{{ number_format($totalDivSaldoMasuk, 0, ',', '.') }}
                                    </td>
                                    <td class="text-end fw-bold">{{ number_format($totalDivSaldoKeluar, 0, ',', '.') }}
                                    </td>
                                    <td class="text-end fw-bold">
                                        {{ number_format($totalDivSaldoMasuk - $totalDivSaldoKeluar, 0, ',', '.') }}</td>
                                    <td class="text-end fw-bold">{{ number_format($totalDiv, 0, ',', '.') }}</td>
                                    <td class="text-end fw-bold">{{ number_format($totalDivKeluar, 0, ',', '.') }}</td>
                                    <td class="text-end fw-bold">
                                        {{ number_format($totalDiv - $totalDivKeluar, 0, ',', '.') }}</td>
                                    <td class="text-end fw-bold">
                                        {{ number_format($totalDivSaldoMasuk + $totalDiv - $totalDivKeluar - $totalDivSaldoKeluar, 0, ',', '.') }}
                                    </td>
                                </tr>

                                <!-- Category Header LABA -->
                                <tr class="table-success">
                                    <td colspan="8" class="fw-bold text-dark">LABA</td>
                                </tr>

                                <!-- TOTAL LABA BERSIH -->
                                <tr class="table-success fw-bold">
                                    <td>TOTAL LABA BERSIH</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-end">{{ number_format($totalLaba, 0, ',', '.') }}</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-end">{{ number_format($totalLaba, 0, ',', '.') }}</td>
                                </tr>

                                <!-- Looping Pembagian Investor -->
                                @foreach ($investorData as $investor)
                                    @php
                                        $persen = $investor->persenInvestor->sum('persen') ?? 0;
                                        $bagianLaba = ($persen / 100) * $totalLaba;
                                    @endphp
                                    <tr>
                                        <td class="ps-4">
                                            {{ $investor->nm_investor }} ({{ $persen }}%)
                                            <button type="button" data-bs-toggle="modal"
                                                data-bs-target="#modalPenarikan" class="btn btn-sm btn-success btn-tarik"
                                                investor_id="{{ $investor->id }}" nama="{{ $investor->nama }}">
                                                + Tarik Laba
                                            </button>
                                        </td>
                                        <td class="text-center">-</td>
                                        <td class="text-center">-</td>
                                        <td class="text-center">-</td>
                                        <td class="text-end">{{ number_format($bagianLaba, 0, ',', '.') }}</td>
                                        <td class="text-center">-</td>
                                        <td class="text-center">-</td>
                                        <td class="text-end fw-semibold">{{ number_format($bagianLaba, 0, ',', '.') }}
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
                        <div class="row g-3">
                            <div class="col-md-4 mb-3">
                                <label for="tanggal_dana" class="form-label">Tanggal</label>
                                <input type="date" id="tanggal_dana" name="tanggal" class="form-control"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="jenis_dana" class="form-label">Jenis Dana</label>
                                <select id="jenis_dana" name="jenis" class="form-select" required>
                                    <option value="" disabled selected>Pilih Jenis...</option>
                                    <option value="TABUNGAN">TABUNGAN</option>
                                    <option value="CADANGAN">CADANGAN</option>
                                    <option value="DANA SEFTY">DANA SEFTY</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
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
                                    <th>Jumlah</th>
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
                                        <td class="text-end fw-semibold">Rp
                                            {{ number_format($item->jumlah, 0, ',', '.') }}</td>
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
                    <form action="{{ route('storeDanaKeluar') }}" method="POST">
                        @csrf
                        <h6 class="fw-semibold mb-3">Tambah Data Dana (DIV)</h6>
                        <div class="row g-3">
                            <div class="col-md-4 mb-3">
                                <label for="tanggal_dana_keluar" class="form-label">Tanggal</label>
                                <input type="date" id="tanggal_dana_keluar" name="tanggal" class="form-control"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="jenis_dana_keluar" class="form-label">Jenis Dana</label>
                                <select id="jenis_dana_keluar" name="jenis" class="form-select" required>
                                    <option value="" disabled selected>Pilih Jenis...</option>
                                    <option value="TABUNGAN">TABUNGAN</option>
                                    <option value="CADANGAN">CADANGAN</option>
                                    <option value="DANA SEFTY">DANA SEFTY</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
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
                                    <th>Jumlah</th>
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
                                        <td class="text-end fw-semibold">Rp
                                            {{ number_format($item->jumlah, 0, ',', '.') }}</td>
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
                    <form action="{{ route('storeSaldoDana') }}" method="POST">
                        @csrf
                        <h6 class="fw-semibold mb-3">Tambah Saldo (DIV)</h6>
                        <div class="row g-3">
                            <div class="col-md-4 mb-3">
                                <label for="tanggal_dana_keluar" class="form-label">Tanggal</label>
                                <input type="date" id="tanggal_dana_keluar" name="tanggal" class="form-control"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="jenis_dana_keluar" class="form-label">Jenis Dana</label>
                                <select id="jenis_dana_keluar" name="jenis" class="form-select" required>
                                    <option value="" disabled selected>Pilih Jenis...</option>
                                    <option value="TABUNGAN">TABUNGAN</option>
                                    <option value="CADANGAN">CADANGAN</option>
                                    <option value="DANA SEFTY">DANA SEFTY</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
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
                                    <th>Jumlah</th>
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
                                        <td class="text-end fw-semibold">Rp
                                            {{ number_format($item->jumlah, 0, ',', '.') }}</td>
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
                    <form action="{{ route('storeSaldoDanaKeluar') }}" method="POST">
                        @csrf
                        <h6 class="fw-semibold mb-3">Tambah Saldo (DIV)</h6>
                        <div class="row g-3">
                            <div class="col-md-4 mb-3">
                                <label for="tanggal_dana_keluar" class="form-label">Tanggal</label>
                                <input type="date" id="tanggal_dana_keluar" name="tanggal" class="form-control"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="jenis_dana_keluar" class="form-label">Jenis Dana</label>
                                <select id="jenis_dana_keluar" name="jenis" class="form-select" required>
                                    <option value="" disabled selected>Pilih Jenis...</option>
                                    <option value="TABUNGAN">TABUNGAN</option>
                                    <option value="CADANGAN">CADANGAN</option>
                                    <option value="DANA SEFTY">DANA SEFTY</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
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
                                    <th>Jumlah</th>
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
                                        <td class="text-end fw-semibold">Rp
                                            {{ number_format($item->jumlah, 0, ',', '.') }}</td>
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
                                <input type="number" name="qty" id="qty" class="form-control" min="1"
                                    value="1" required>
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
                                        <td colspan="6" class="text-center text-muted">Belum ada data pada periode ini.
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
                                        <td colspan="4" class="text-center text-muted">Tidak ada data Jasa Layanan pada
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
                                        <td colspan="4" class="text-center text-muted">Tidak ada data Penjualan Produk
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
    <div class="modal fade" id="modalPenarikan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('penarikan.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Penarikan Laba - <span id="namaInvestorModal"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="investor_id" id="modal_investor_id">

                        <div class="mb-3">
                            <label for="tgl" class="form-label">Tanggal Penarikan</label>
                            <input type="date" class="form-control" name="tgl" id="tgl"
                                value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="jumlah" class="form-label">Jumlah Penarikan (Rp)</label>
                            <input type="number" class="form-control" name="jumlah" id="jumlah" min="1"
                                placeholder="Masukkan nominal" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Penarikan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @section('script')
        <script>
            $(document).ready(function() {

                $(document).on('click', '.btn-tarik', function() {
                    let investorId = $(this).attr('investor_id');
                    let investorNama = $(this).attr('nama');

                    $('#modal_investor_id').val(investorId);
                    $('#namaInvestorModal').text(investorNama);
                    $('#modalPenarikan').modal('show');
                });

            });
        </script>
    @endsection



@endsection
