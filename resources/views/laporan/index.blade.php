@extends('template.master')

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
                                <div class="col-12">
                                    <h5 class="float-start">Laporan Penjualan</h5>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="form-group">
                                        <label for="">Cabang</label>
                                        <select class="form-control"
                                            @if ($cabang_id !== null) name="cabang_id" @endif required>
                                            <option value="">Pilih Cabang</option>
                                            @foreach ($cabang as $c)
                                                <option value="{{ $c->id }}"
                                                    {{ $cabang_id == $c->id ? 'selected' : '' }}>{{ $c->nama }}
                                                </option>
                                            @endforeach
                                            @if (Auth::user()->role_id == 1)
                                                <option value="all" {{ $cabang_id == 'all' ? 'selected' : '' }}>Semua
                                                    Cabang</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="form-group">
                                        <label for="">Dari</label>
                                        <input type="date" class="form-control" name="tgl1"
                                            value="{{ $tgl1 }}" required>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="form-group">
                                        <label for="">Sampai</label>
                                        <input type="date" class="form-control" name="tgl2"
                                            value="{{ $tgl2 }}" required>
                                    </div>
                                </div>

                                <div class="col-6 col-md-2">
                                    <button type="submit" class="btn btn-sm btn-primary mt-4"><i class='bx bx-search'></i>
                                    </button>
                                </div>
                            </div>
                        </form>


                    </div>

                    <div class="card-body">

                        @if (!empty($laporan))
                            <div class="table-responsive">
                                <table class="table table-sm text-center" width="100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Tanggal</th>
                                            <th>Jumlah Customer</th>
                                            <th>Total Penjualan</th>
                                            <th>Detail</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $i = 1;
                                            $total = 0;
                                        @endphp
                                        @foreach ($laporan as $d)
                                            @php
                                                $total += $d->ttl_penjualan - $d->ttl_diskon;
                                            @endphp
                                            <tr>
                                                <td>{{ $i++ }}</td>
                                                <td>{{ date('d/m/Y', strtotime($d->tgl)) }}</td>
                                                <td>{{ $d->jml_pelanggan }}</td>
                                                <td>{{ number_format($d->ttl_penjualan - $d->ttl_diskon, 0) }}</td>
                                                <td>
                                                    <button type="button"
                                                        class="btn btn-sm btn-primary detail_laporan_penjualan"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modal_detail_laporan_penjualan"
                                                        tgl="{{ $d->tgl }}"><i class='bx bx-search'></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3"><b>Total</b></td>
                                            <td><b>{{ number_format($total, 0) }}</b></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @endif



                    </div>

                </div>

                @if (!empty($laporan))
                    <div class="card mt-2">
                        <div class="card-header">

                            <div class="row">
                                <div class="col-12">
                                    <h5 class="float-start">Laporan Per Service</h5>
                                </div>

                            </div>



                        </div>

                        <div class="card-body">

                            <div class="table-responsive">
                                <table class="table table-sm text-center" width="100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Service</th>
                                            <th>Jumlah Service</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $i = 1;
                                            $total = 0;
                                        @endphp
                                        @foreach ($perlayanan as $d)
                                            @php
                                                $total += $d->ttl_penjualan;
                                            @endphp
                                            <tr>
                                                <td>{{ $i++ }}</td>
                                                <td>{{ $d->service->nm_service }}</td>
                                                <td>{{ $d->jml_service }}</td>
                                                <td>{{ number_format($d->ttl_penjualan, 0) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                        </div>

                    </div>

                    <div class="card mt-2">
                        <div class="card-header">

                            <div class="row">
                                <div class="col-12">
                                    <h5 class="float-start">Laporan Pembagian</h5>
                                </div>

                            </div>


                        </div>

                        <div class="card-body">

                            <div class="table-responsive">
                                <table class="table table-sm text-center" width="100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Karyawan</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $i = 1;
                                            $total = 0;
                                        @endphp
                                        @foreach ($pembagian as $d)
                                            @php
                                                $total += $d->ttl_pembagian;
                                            @endphp
                                            <tr>
                                                <td>{{ $i++ }}</td>
                                                <td>{{ $d->karyawan->nama }}</td>
                                                <td>{{ number_format($d->ttl_pembagian, 0) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2"><b>Total</b></td>
                                            <td><b>{{ number_format($total, 0) }}</b></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                        </div>

                    </div>
                @endif



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


    <div class="modal fade" id="modal_detail_laporan_penjualan" tabindex="-1"
        aria-labelledby="modal_detail_laporan_penjualanLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal_detail_laporan_penjualanLabel">Detail Laporan Penjualan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="table_detail_laporan_penjualan">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_detail_penjualan" tabindex="-1" aria-labelledby="modal_detail_penjualanLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal_detail_penjualanLabel">Detail Laporan Penjualan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="table_detail_penjualan">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>



@section('script')
    <script src="{{ asset('js') }}/qrcode.js" type="text/javascript"></script>

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


            $(document).on('click', '.detail_laporan_penjualan', function() {

                $('#table_detail_laporan_penjualan').html(
                    '<div class="spinner-border text-secondary" role="status"><span class="visually-hidden">Loading...</span></div>'
                );
                var tgl = $(this).attr('tgl');
                $.get('detailLaporanPenjualan/' + tgl, function(data) {
                    $('#table_detail_laporan_penjualan').html(data);
                });

            });

            $(document).on('click', '.detail_penjualan', function() {

                $('#table_detail_penjualan').html(
                    '<div class="spinner-border text-secondary" role="status"><span class="visually-hidden">Loading...</span></div>'
                );
                var invoice_id = $(this).attr('invoice_id');
                $.get('getDeatailPesanan/' + invoice_id, function(data) {
                    $('#table_detail_penjualan').html(data);
                });

            });

        });
    </script>
@endsection
@endsection
