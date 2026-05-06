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
                        {{-- <form action="" method="get"> --}}
                        <div class="row">
                            <div class="col-12 col-md-4">
                                <h5 class="float-start">Laporan Permintaan Refund</h5>
                            </div>
                            {{-- <div class="col-4 col-md-3">
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
                                </div> --}}

                            {{-- <div class="col-4 col-md-2">
                                    <button type="submit" class="btn btn-sm btn-primary mt-4"><i class='bx bx-search'></i>
                                    </button>
                                </div> --}}
                        </div>
                        {{-- </form> --}}


                    </div>

                    <div class="card-body">

                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr class="text-center">
                                        <th>#</th>
                                        <th>Cabang</th>
                                        <th>Antrian</th>
                                        <th>Waktu Selesai</th>
                                        <th>Invoice</th>
                                        <th>Customer</th>
                                        <th>Total</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $total = 0;
                                        $i = 1;
                                    @endphp
                                    @foreach ($permintaan as $d)
                                        @php
                                            $total += $d->total;
                                        @endphp
                                        <tr class="text-center">
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $d->cabang->nama }}</td>
                                            <td>{{ $d->no_antrian }}</td>
                                            <td>{{ date('d/m/Y H:i', strtotime($d->updated_at)) }}</td>
                                            <td>{{ $d->no_invoice }}</td>
                                            <td>{{ $d->nm_customer }}</td>
                                            <td>{{ number_format($d->total, 0) }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                    data-bs-target="#modal_detail_penjualan{{ $d->id }}"><i
                                                        class='bx bx-search'></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                {{-- <tfoot>
                                    <tr>
                                        <td colspan="5"><b>Total</b></td>
                                        <td><b>{{ number_format($total, 0) }}</b></td>
                                        <td></td>
                                    </tr>
                                </tfoot> --}}
                            </table>
                        </div>

                    </div>

                </div>

                <div class="card mt-2">
                    <div class="card-header">
                        <form action="" method="get">
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="float-start">Laporan Refund</h5>
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

                        <div class="table-responsive">
                            <table class="table table-sm table-striped" id="table">
                                <thead>
                                    <tr class="text-center">
                                        <th>#</th>
                                        <th>Cabang</th>
                                        <th>Antrian</th>
                                        <th>Waktu Selesai</th>
                                        <th>Invoice</th>
                                        <th>Customer</th>
                                        <th>Total</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $total = 0;
                                        $i = 1;
                                    @endphp
                                    @foreach ($refund as $d)
                                        @php
                                            $total += $d->total;
                                        @endphp
                                        <tr class="text-center">
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $d->cabang->nama }}</td>
                                            <td>{{ $d->no_antrian }}</td>
                                            <td>{{ date('d/m/Y H:i', strtotime($d->updated_at)) }}</td>
                                            <td>{{ $d->no_invoice }}</td>
                                            <td>{{ $d->nm_customer }}</td>
                                            <td>{{ number_format($d->total, 0) }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                    data-bs-target="#modal_detail_penjualan_refund{{ $d->id }}"><i
                                                        class='bx bx-search'></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                {{-- <tfoot>
                                    <tr>
                                        <td colspan="5"><b>Total</b></td>
                                        <td><b>{{ number_format($total, 0) }}</b></td>
                                        <td></td>
                                    </tr>
                                </tfoot> --}}
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

    @foreach ($permintaan as $d)
        <div class="modal fade" id="modal_detail_penjualan{{ $d->id }}" tabindex="-1"
            aria-labelledby="modal_detail_penjualan{{ $d->id }}Label" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal_detail_penjualan{{ $d->id }}Label">Detail Laporan
                            Penjualan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>#Invoice</th>
                                    <th>: {{ $d->no_invoice }}</th>
                                </tr>
                                <tr>
                                    <th>Waktu</th>
                                    <th>: {{ date('d/m/Y H:i', strtotime($d->updated_at)) }}</th>
                                </tr>
                                <tr>
                                    <th>Dilayani oleh</th>
                                    <th>:
                                        @if ($d->penjualanKaryawan)
                                            @foreach ($d->penjualanKaryawan as $k)
                                                {{ $k->karyawan->nama }},
                                            @endforeach
                                        @endif
                                    </th>
                                </tr>
                                <tr>
                                    <th>Customer/No WA</th>
                                    <th>: {{ $d->nm_customer }}/{{ $d->no_tlp }}</th>
                                </tr>
                                <tr>
                                    <th>Alasan Refund</th>
                                    <th>: {{ $d->ket_refund }}</th>
                                </tr>
                            </thead>
                        </table>
                        <table class="table table-sm" mt-2>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Service</th>
                                    <th>Harga</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($d->penjualan)
                                    @php
                                        $i = 1;
                                        $grand_total = 0;
                                    @endphp
                                    @foreach ($d->penjualan as $p)
                                        @php
                                            $grand_total += $p->qty * $p->harga;
                                        @endphp
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $p->qty }} X {{ $p->service->nm_service }}</td>
                                            <td>{{ number_format($p->harga, 0) }}</td>
                                            <td>{{ number_format($p->qty * $p->harga, 0) }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                            <tbody>
                                <tr>
                                    <td colspan="3"><b>Diskon</b></td>
                                    <td><b>{{ number_format($d->diskon, 0) }}</b></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3"><b>Total</b></td>
                                    <td><b>{{ number_format($grand_total - $d->diskon, 0) }}</b></td>
                                </tr>
                            </tfoot>
                        </table>

                    </div>
                    <div class="modal-footer">
                        <a href="{{ route('tolakRefund', $d->id) }}" class="btn btn-danger">Tolak</a>
                        <a href="{{ route('TerimaRefund', $d->id) }}" class="btn btn-primary">Terima</a>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    @foreach ($refund as $d)
        <div class="modal fade" id="modal_detail_penjualan_refund{{ $d->id }}" tabindex="-1"
            aria-labelledby="modal_detail_penjualan_refund{{ $d->id }}Label" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal_detail_penjualan_refund{{ $d->id }}Label">Detail Laporan
                            Penjualan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>#Invoice</th>
                                    <th>: {{ $d->no_invoice }}</th>
                                </tr>
                                <tr>
                                    <th>Waktu</th>
                                    <th>: {{ date('d/m/Y H:i', strtotime($d->updated_at)) }}</th>
                                </tr>
                                <tr>
                                    <th>Dilayani oleh</th>
                                    <th>:
                                        @if ($d->penjualanKaryawan)
                                            @foreach ($d->penjualanKaryawan as $k)
                                                {{ $k->karyawan->nama }},
                                            @endforeach
                                        @endif
                                    </th>
                                </tr>
                                <tr>
                                    <th>Customer/No WA</th>
                                    <th>: {{ $d->nm_customer }}/{{ $d->no_tlp }}</th>
                                </tr>
                                <tr>
                                    <th>Alasan Refund</th>
                                    <th>: {{ $d->ket_refund }}</th>
                                </tr>
                            </thead>
                        </table>
                        <table class="table table-sm" mt-2>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Service</th>
                                    <th>Harga</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($d->penjualan)
                                    @php
                                        $i = 1;
                                        $grand_total = 0;
                                    @endphp
                                    @foreach ($d->penjualan as $p)
                                        @php
                                            $grand_total += $p->qty * $p->harga;
                                        @endphp
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $p->qty }} X {{ $p->service->nm_service }}</td>
                                            <td>{{ number_format($p->harga, 0) }}</td>
                                            <td>{{ number_format($p->qty * $p->harga, 0) }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                            <tbody>
                                <tr>
                                    <td colspan="3"><b>Diskon</b></td>
                                    <td><b>{{ number_format($d->diskon, 0) }}</b></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3"><b>Total</b></td>
                                    <td><b>{{ number_format($grand_total - $d->diskon, 0) }}</b></td>
                                </tr>
                            </tfoot>
                        </table>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach






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
