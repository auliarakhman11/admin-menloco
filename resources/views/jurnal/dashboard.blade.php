@extends('template.master')
@section('chart')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.6.2/chart.min.js"
        integrity="sha512-tMabqarPtykgDtdtSqCL3uLVM0gS1ZkUAVhRFu1vSEFgvB73niFQWJuvviDyBGBH22Lcau4rHB5p2K2T0Xvr6Q=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endsection
@section('content')


    <!-- Content -->

    <style>


    </style>



    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">

            <div class="col-12 mb-4 order-0">

                <div class="card">
                    <div class="card-header">
                        <form action="" method="get">
                            <div class="row">
                                <div class="col-12 col-md-4">
                                    <h5 class="float-start">Laporan Pengeluaran</h5>
                                </div>
                                <div class="col-4 col-md-3">
                                    <div class="form-group">
                                        <label for="">Dari</label>
                                        <input type="date" class="form-control" name="tgl1"
                                            value="{{ $tgl1 }}" required>
                                    </div>
                                </div>
                                <div class="col-4 col-md-3">
                                    <div class="form-group">
                                        <label for="">Sampai</label>
                                        <input type="date" class="form-control" name="tgl2"
                                            value="{{ $tgl2 }}" required>
                                    </div>
                                </div>

                                <div class="col-4 col-md-2">
                                    <button type="submit" class="btn btn-sm btn-primary mt-4 float-end"><i
                                            class='bx bx-search'></i>
                                    </button>
                                </div>
                            </div>
                        </form>

                        @php
                            $ttl_kapster = 0;
                            foreach ($dt_kepster as $d) {
                                $ttl_kapster += $d['ttl_kapster'];
                            }
                        @endphp

                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mt-3">
                                <thead>
                                    <tr class="text-center">
                                        <th>Pemasukan</th>
                                        <th>Pengeluaran</th>
                                        <th>Saldo Kas</th>
                                        <th>Men Loco</th>
                                        <th>Kapster</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="text-center">
                                        <td>{{ number_format($total_penjualan, 0) }}</td>
                                        <td>{{ number_format($jurnal ? $jurnal->jml_pengeluaran : 0, 0) }}</td>
                                        <td>{{ number_format($total_penjualan - ($jurnal ? $jurnal->jml_pengeluaran : 0), 0) }}
                                        </td>
                                        <td>{{ number_format($total_penjualan - ($jurnal ? $jurnal->jml_pengeluaran : 0) - $ttl_kapster, 0) }}
                                        </td>
                                        <td>{{ number_format($ttl_kapster, 0) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>


                    </div>

                    <div class="card-body">

                        <canvas id="grafik_penjualan" width="400" height="180" class="bg-light"></canvas>

                    </div>

                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th colspan="3">Persentasi Pemasukan</th>
                                        <th>Men Loco</th>
                                        <th>Kapster</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $ttl_penjualan = 0;
                                        $ttl_menloco = 0;
                                        $ttl_kapster = 0;
                                        $ttl_persen = 0;
                                    @endphp
                                    @foreach ($dt_kepster as $d)
                                        @php
                                            $ttl_penjualan += $d['ttl_penjualan'] - $d['ttl_diskon'];
                                            $ttl_menloco += $d['ttl_penjualan'] - $d['ttl_diskon'] - $d['ttl_kapster'];
                                            $ttl_kapster += $d['ttl_kapster'];
                                        @endphp
                                        <tr>
                                            <td>{{ $d['nm_karyawan'] }}</td>
                                            <td>{{ number_format($d['ttl_penjualan'] - $d['ttl_diskon'], 0) }}</td>
                                            @php
                                                $persen =
                                                    $total_penjualan > 0 && $d['ttl_penjualan'] - $d['ttl_diskon'] > 0
                                                        ? (($d['ttl_penjualan'] - $d['ttl_diskon']) /
                                                                $total_penjualan) *
                                                            100
                                                        : 0;
                                                $ttl_persen += $persen;
                                            @endphp
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar"
                                                        style="width: {{ number_format($persen, 2) }}%;"
                                                        aria-valuenow="{{ number_format($persen, 2) }}" aria-valuemin="0"
                                                        aria-valuemax="100">{{ number_format($persen, 2) }}%</div>
                                                </div>
                                            </td>
                                            <td>{{ number_format($d['ttl_penjualan'] - $d['ttl_diskon'] - $d['ttl_kapster'], 0) }}
                                            </td>
                                            <td>{{ number_format($d['ttl_kapster'], 0) }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td>Produk</td>
                                        <td>{{ number_format($ttl_penjualan_produk, 0) }}</td>
                                        @php
                                            $persen_barang =
                                                $ttl_penjualan_produk > 0 && $total_penjualan > 0
                                                    ? ($ttl_penjualan_produk / $total_penjualan) * 100
                                                    : 0;
                                            $ttl_persen += $persen_barang;
                                        @endphp
                                        <td>
                                            <div class="progress">
                                                <div class="progress-bar" role="progressbar"
                                                    style="width: {{ number_format($persen_barang, 2) }}%;"
                                                    aria-valuenow="{{ number_format($persen_barang, 2) }}"
                                                    aria-valuemin="0" aria-valuemax="100">
                                                    {{ number_format($persen_barang, 2) }}%</div>
                                            </div>
                                        </td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td><b>Total</b></td>
                                        <td><b>{{ number_format($ttl_penjualan + $ttl_penjualan_produk, 0) }}</b></td>
                                        <td>
                                            <div class="progress">
                                                <div class="progress-bar" role="progressbar"
                                                    style="width: {{ number_format($ttl_persen, 2) }}%;"
                                                    aria-valuenow="{{ number_format($ttl_persen, 2) }}" aria-valuemin="0"
                                                    aria-valuemax="100">{{ number_format($ttl_persen, 2) }}%</div>
                                            </div>
                                        </td>
                                        <td><b>{{ number_format($ttl_menloco, 0) }}</b></td>
                                        <td><b>{{ number_format($ttl_kapster, 0) }}</b></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>Kapster</th>
                                        <th>Pendapatan</th>
                                        <th>Piutang</th>
                                        <th>Sudah Diserahkan</th>
                                        <th>Saldo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $tot_pendapatan = 0;
                                        $tot_kasbon = 0;
                                        $tot_ambil_gaji = 0;
                                        $tot_saldo = 0;
                                    @endphp
                                    @foreach ($saldo_karyawan as $d)
                                        @php
                                            $tot_pendapatan += $d->ttl_harga;
                                            $tot_kasbon += $d->ttl_kasbon;
                                            $tot_ambil_gaji += $d->ttl_ambil_gaji;
                                            $tot_saldo += $d->ttl_harga - $d->ttl_kasbon - $d->ttl_ambil_gaji;
                                        @endphp
                                        <tr>
                                            <td>{{ $d->nama }}</td>
                                            <td>{{ number_format($d->ttl_harga, 0) }}</td>
                                            <td>{{ number_format($d->ttl_kasbon, 0) }}</td>
                                            <td>{{ number_format($d->ttl_ambil_gaji, 0) }}</td>
                                            <td>{{ number_format($d->ttl_harga - $d->ttl_kasbon - $d->ttl_ambil_gaji, 0) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td><b>Total</b></td>
                                        <td><b>{{ number_format($tot_pendapatan, 0) }}</b></td>
                                        <td><b>{{ number_format($tot_kasbon, 0) }}</b></td>
                                        <td><b>{{ number_format($tot_ambil_gaji, 0) }}</b></td>
                                        <td><b>{{ number_format($tot_saldo, 0) }}</b></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>Service</th>
                                        @foreach ($karyawan as $k)
                                            <th>{{ $k->karyawan->nama }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dt_service_kepster as $d)
                                        <tr>
                                            <td>{{ $d['nm_service'] }}</td>
                                            @foreach ($d['kapster'] as $k)
                                                <td>{{ $k }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>





                {{-- <div class="card mt-3">
          <div class="card-header">
              <h5 class="float-start">Kirim Berkas</h5>
              
          </div>
          <div class="card-body" id="cart">

          </div>
          <div class="card-footer">
            <button type="button" id="btn_input_data" class="btn btn-sm btn-primary float-end"><i class='bx bx-send'></i> Kirim</button>
          </div>
        </div> --}}


            </div>

            <!-- Total Revenue -->

            <!--/ Total Revenue -->

        </div>

    </div>
    <!-- / Content -->



    <!-- Modal -->



@section('script')
    <script>
        var cData = JSON.parse(`<?php echo $chart; ?>`);
        var periode = JSON.parse(`<?php echo $periode; ?>`);
        const ctx = document.getElementById('grafik_penjualan');
        const myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: periode,
                datasets: cData
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });

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






        });
    </script>
@endsection
@endsection
