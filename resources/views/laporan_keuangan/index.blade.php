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
                        <table class="table table-bordered align-middle">
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
                                    <td class="ps-4">Jasa Layanan</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-end">{{ number_format($jasaLayanan, 0, ',', '.') }}</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-end fw-semibold">{{ number_format($jasaLayanan, 0, ',', '.') }}</td>
                                </tr>

                                <!-- Penjualan Prodak -->
                                <tr>
                                    <td class="ps-4">Penjualan Prodak</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-end">{{ number_format($penjualanProdak, 0, ',', '.') }}</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-end fw-semibold">{{ number_format($penjualanProdak, 0, ',', '.') }}
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
                                    <td class="text-end fw-semibold">{{ number_format($pendapatanDll, 0, ',', '.') }}
                                    </td>
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
                                    <td class="text-end">{{ number_format($gajiCapster, 0, ',', '.') }}</td>
                                    <td class="text-end text-danger">
                                        {{ number_format($pengeluaranGajiCapster, 0, ',', '.') }}</td>
                                    <td class="text-end text-primary fw-bold">
                                        {{ number_format($aktualGajiCapster, 0, ',', '.') }}</td>
                                    <td class="text-end text-success fw-bold">
                                        {{ number_format($totalAktualCapster, 0, ',', '.') }}</td>

                                </tr>

                                <!-- Baris PRODAK -->
                                <tr>
                                    <td class="ps-4 d-flex justify-content-between align-items-center"> <span>Prodak</span>
                                        <button type="button" class="btn btn-sm btn-primary ms-2" data-bs-toggle="modal"
                                            data-bs-target="#modalProduk"><i class="bx bx-plus me-1"></i></button></td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-end">{{ number_format($prodak, 0, ',', '.') }}</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-end fw-semibold">{{ number_format($prodak, 0, ',', '.') }}</td>
                                </tr>

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
                                    <td class="text-end fw-bold">{{ number_format($totalOperasional, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-end fw-bold">{{ number_format($totalOperasional, 0, ',', '.') }}
                                    </td>
                                </tr>

                                <!-- Category Header DIV -->
                                <tr class="table-secondary">
                                    <td class="fw-bold text-dark d-flex justify-content-between align-items-center">
                                        <span>DIV</span>
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#modalDana"><i class="bx bx-plus me-1"></i></button>
                                    </td>
                                    <td colspan="7"></td>
                                </tr>

                                <!-- Tabungan -->
                                <tr>
                                    <td class="ps-4">Tabungan</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-end">{{ number_format($tabungan, 0, ',', '.') }}</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-end fw-semibold">{{ number_format($tabungan, 0, ',', '.') }}</td>
                                </tr>

                                <!-- Cadangan -->
                                <tr>
                                    <td class="ps-4">Cadangan</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-end">{{ number_format($cadangan, 0, ',', '.') }}</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-end fw-semibold">{{ number_format($cadangan, 0, ',', '.') }}</td>
                                </tr>

                                <!-- Dana Sefty -->
                                <tr>
                                    <td class="ps-4">Dana Sefty</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-end">{{ number_format($danaSefty, 0, ',', '.') }}</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-end fw-semibold">{{ number_format($danaSefty, 0, ',', '.') }}</td>
                                </tr>

                                <!-- TOTAL DIV -->
                                <tr class="table-light">
                                    <td class="fw-bold text-end">TOTAL DIV</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-end fw-bold">{{ number_format($totalDiv, 0, ',', '.') }}</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-end fw-bold">{{ number_format($totalDiv, 0, ',', '.') }}</td>
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
                                        <td class="ps-4">{{ $investor->nm_investor }} ({{ $persen }}%)</td>
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
                                        <td class="text-center">
                                            {{ \Carbon\Carbon::parse($item->tgl)->format('d-m-Y') }}</td>
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
                                        <td colspan="5" class="text-center text-muted">Tidak ada data dana
                                            (DIV)
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
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <input type="date" name="tgl" id="tanggal" class="form-control"
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
                                <label for="jumlah" class="form-label">Jumlah (Rp)</label>
                                <input type="number" name="jumlah" id="jumlah" class="form-control"
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
                                        <td>{{ date('d-m-Y', strtotime($item->tanggal)) }}</td>
                                        <td>{{ $item->service->nm_service ?? ($item->service->nama_service ?? '-') }}
                                        </td>
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
                                        <td colspan="6" class="text-center text-muted">Belum ada data pada
                                            periode ini.</td>
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
@endsection
