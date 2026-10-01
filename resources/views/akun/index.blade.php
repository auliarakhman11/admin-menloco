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
                        <h5 class="float-start">Data Akun</h5>
                        <button type="button" class="btn btn-sm btn-primary float-end" data-bs-toggle="modal"
                            data-bs-target="#modal_add_akun"><i class='bx bxs-plus-circle'></i> Tambah Akun</button>
                    </div>

                    <div class="card-body">

                        <div class="table-responsive">
                            <table class="table table-sm text-center" width="100%" id="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nama Akun</th>
                                        {{-- <th>Pengeluaran</th> --}}
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $i = 1;
                                    @endphp
                                    @foreach ($akun as $d)
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $d->nm_akun }}</td>
                                            {{-- <td>
                                                @if ($d->jml_pengeluaran > 100)
                                                    Rp. {{ number_format($d->jml_pengeluaran, 0) }}
                                                @else
                                                    {{ $d->jml_pengeluaran }}%
                                                @endif
                                            </td> --}}
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                    data-bs-target="#modal_edit_akun{{ $d->id }}"><i
                                                        class='bx bxs-message-square-edit'></i></button>
                                                <a href="{{ route('deleteAkun', $d->id) }}"
                                                    onclick="return confirm('Apakah anda yakin ingin menghapus akun ini? Data jurnal dan pengeluaran terkait juga akan ikut terhapus.');"
                                                    class="btn btn-sm btn-danger"><i class='bx bxs-trash'></i></a>
                                            </td>
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

    <form id="form_add_akun" method="POST" action="{{ route('addAkun') }}">
        @csrf
        <div class="modal fade" id="modal_add_akun" tabindex="-1" aria-labelledby="modal_add_akunLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered ">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal_add_akunLabel">Tambah Akun</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">

                            <div class="col-12 mb-2">
                                <div class="form-group">
                                    <label for="">Nama Akun</label>
                                    <input type="text" name="nm_akun" class="form-control" required>
                                </div>
                            </div>

                            {{-- <div class="col-12 mb-2">
                                <div class="form-group">
                                    <label for="">Jumlah Pengeluaran</label>
                                    <input type="text" name="jml_pengeluaran" class="form-control" required>
                                </div>
                            </div> --}}


                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="btn_add_diskon">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @foreach ($akun as $d)
        <form method="POST" action="{{ route('editAkun') }}">
            @csrf
            @method('patch')
            <div class="modal fade" id="modal_edit_akun{{ $d->id }}" tabindex="-1"
                aria-labelledby="modal_edit_akunLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered ">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modal_edit_akunLabel">Edit Akun</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">

                                <input type="hidden" name="id" value="{{ $d->id }}">

                                <div class="col-12 mb-2">
                                    <div class="form-group">
                                        <label for="">Nama Akun</label>
                                        <input type="text" name="nm_akun" class="form-control"
                                            value="{{ $d->nm_akun }}" required>
                                    </div>
                                </div>

                                {{-- <div class="col-12 mb-2">
                                    <div class="form-group">
                                        <label for="">Jumlah Pengeluaran</label>
                                        <input type="text" name="jml_pengeluaran" class="form-control"
                                            value="{{ $d->jml_pengeluaran }}" required>
                                    </div>
                                </div> --}}


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


            $(document).on('submit', '#form_add_akun', function(event) {
                $('#btn_add_diskon').attr('disabled', true);
                $('#btn_add_diskon').html('Loading...');

            });

        });
    </script>
@endsection
@endsection
