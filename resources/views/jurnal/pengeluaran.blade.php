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
                        <div class="row">
                            <div class="col-12"><button type="button" class="btn btn-sm btn-primary float-end"
                                    data-bs-toggle="modal" data-bs-target="#modal_add_pengeluaran"><i
                                        class='bx bxs-plus-circle'></i> Tambah Data</button></div>
                        </div>


                    </div>

                    <div class="card-body">

                        <div class="table-responsive">
                            <table class="table table-sm text-center" width="100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Tanggal</th>
                                        <th>Akun</th>
                                        <th>Jumlah</th>
                                        <th>Keterangan</th>
                                        <th>User</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $i = 1;
                                        $total = 0;
                                    @endphp
                                    @foreach ($jurnal as $d)
                                        @php
                                            $total += $d->jumlah;
                                        @endphp
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ date('d/m/Y', strtotime($d->tgl)) }}</td>
                                            <td>{{ $d->akun->nm_akun }}</td>
                                            <td>{{ number_format($d->jumlah, 0) }}</td>
                                            <td>{{ $d->ket }}</td>
                                            <td>{{ $d->user->name }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                    data-bs-target="#modal_edit_pengeluaran{{ $d->id }}"
                                                    tgl="{{ $d->tgl }}"><i class="bx bx-edit"></i>
                                                </button>
                                                <a href="{{ route('deletePengeluaran', $d->id) }}"
                                                    onclick="return confirm('Apakah anda yakin ingin menghapus data?');"
                                                    class="btn btn-sm btn-primary"><i class="bx bx-trash"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3"><b>Total</b></td>
                                        <td><b>{{ number_format($total, 0) }}</b></td>
                                        <td colspan="3"></td>
                                    </tr>
                                </tfoot>
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


    <form id="form_add_pengeluraan" method="POST" action="{{ route('addPengeluaran') }}">
        @csrf
        <div class="modal fade" id="modal_add_pengeluaran" tabindex="-1" aria-labelledby="modal_add_pengeluaranLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal_add_pengeluaranLabel">Tambah Data</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">

                            <div class="col-12 mb-2">
                                <div class="form-group">
                                    <label for="">Tanggal</label>
                                    <input type="date" name="tgl" class="form-control" required>
                                </div>
                            </div>

                            <div class="col-12 mb-2">
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


                            <div class="col-12 mb-2">
                                <div class="form-group">
                                    <label for="">Jumlah</label>
                                    <input type="number" name="jumlah" class="form-control" required>
                                </div>
                            </div>

                            <div class="col-12 mb-2">
                                <div class="form-group">
                                    <label for="">Keterangan</label>
                                    <input type="text" name="ket" class="form-control" required>
                                </div>
                            </div>


                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="btn_add_karyawan">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @foreach ($jurnal as $d)
        <form method="POST" action="{{ route('editPengeluaran') }}">
            @csrf
            @method('patch')
            <div class="modal fade" id="modal_edit_pengeluaran{{ $d->id }}" tabindex="-1"
                aria-labelledby="modal_add_pengeluaranLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Data</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">

                                <input type="hidden" name="id" value="{{ $d->id }}">

                                <div class="col-12 mb-2">
                                    <div class="form-group">
                                        <label for="">Tanggal</label>
                                        <input type="date" name="tgl" value="{{ $d->tgl }}"
                                            class="form-control" required>
                                    </div>
                                </div>

                                <div class="col-12 mb-2">
                                    <div class="form-group">
                                        <label for="">Akun</label>
                                        <select name="akun_id" class="form-control" required>
                                            @foreach ($akun as $a)
                                                <option value="{{ $a->id }}"
                                                    {{ $a->id == $d->akun_id ? 'selected' : '' }}>{{ $a->nm_akun }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>


                                <div class="col-12 mb-2">
                                    <div class="form-group">
                                        <label for="">Jumlah</label>
                                        <input type="number" name="jumlah" value="{{ $d->jumlah }}"
                                            class="form-control" required>
                                    </div>
                                </div>

                                <div class="col-12 mb-2">
                                    <div class="form-group">
                                        <label for="">Keterangan</label>
                                        <input type="text" name="ket" value="{{ $d->ket }}"
                                            class="form-control" required>
                                    </div>
                                </div>


                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Edit</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
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

            $(document).on('submit', '#form_add_pengeluraan', function(event) {

                $('#btn_add_karyawan').attr('disabled', true);
                $('#btn_add_karyawan').html('Loading..');


            });




        });
    </script>
@endsection
@endsection
