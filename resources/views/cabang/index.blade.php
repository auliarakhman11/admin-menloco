@extends('template.master')

@section('content')


    <!-- Content -->

    <style>


    </style>



    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">

            <div class="col-12 mb-4 order-0">

                @if ($errors->any())
                    @foreach ($errors->all() as $e)
                        <div class="alert alert-danger" role="alert">
                            {{ $e }}
                        </div>
                    @endforeach
                @endif

                <div class="card">
                    <div class="card-header">
                        <h5 class="float-start">Data Cabang</h5>
                        <button type="button" class="btn btn-sm btn-primary float-end" data-bs-toggle="modal"
                            data-bs-target="#modal_add_data"><i class='bx bxs-plus-circle'></i> Tambah Data</button>
                    </div>

                    <div class="card-body">

                        <div class="table-responsive">
                            <table class="table table-sm text-center" width="100%" id="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Cabang</th>
                                        <th>Username</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $i = 1;
                                    @endphp
                                    @foreach ($cabang as $d)
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $d->nama }}</td>
                                            <td>
                                                @if ($d->user)
                                                    @foreach ($d->user as $u)
                                                        {{ $u->username }} <br>
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                    data-bs-target="#modal_edit_data{{ $d->id }}"><i
                                                        class='bx bxs-message-square-edit'></i></button>
                                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                    data-bs-target="#modal_akun_pengeluaran{{ $d->id }}"><i
                                                        class='bx bxs-dollar-circle'></i></button>

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

    <form id="form_add_data" method="POST" action="{{ route('addCabang') }}">
        @csrf
        <div class="modal fade" id="modal_add_data" tabindex="-1" aria-labelledby="modal_add_dataLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered ">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal_add_dataLabel">Tambah Cabang</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">

                            <div class="col-12 mb-2">
                                <div class="form-group">
                                    <label for="">Nama Cabang</label>
                                    <input type="text" name="nama" class="form-control" required>
                                </div>
                            </div>

                            <div class="col-12 mb-2">
                                <div class="form-group">
                                    <label for="">Username</label>
                                    <input type="text" name="username" class="form-control" required>
                                </div>
                            </div>

                            <div class="col-12 mb-2">
                                <div class="form-group">
                                    <label for="">Password</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                            </div>

                            <div class="col-12 mb-2">
                                <div class="form-group">
                                    <label for="">Confirmasi Password</label>
                                    <input type="password" name="password_confirmation" class="form-control" required>
                                </div>
                            </div>


                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="btn_add_data">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @foreach ($cabang as $d)
        <form method="POST" action="{{ route('editCabang') }}">
            @csrf
            @method('patch')
            <div class="modal fade" id="modal_edit_data{{ $d->id }}" tabindex="-1"
                aria-labelledby="modal_edit_dataLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered ">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modal_edit_dataLabel">Tambah Diskon</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">

                                <input type="hidden" name="id" value="{{ $d->id }}">

                                <div class="col-12 mb-2">
                                    <div class="form-group">
                                        <label for="">Nama Cabang</label>
                                        <input type="text" name="nama" value="{{ $d->nama }}"
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

        <form method="POST" action="{{ route('editCabang') }}">
            @csrf
            @method('patch')
            <div class="modal fade" id="modal_akun_pengeluaran{{ $d->id }}" tabindex="-1"
                aria-labelledby="modal_akun_pengeluaranLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modal_akun_pengeluaranLabel">Tambah Pengeluaran Otomatis</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">

                                <input type="hidden" name="id" value="{{ $d->id }}">

                                <div class="col-4 mb-2">
                                    <div class="form-group">
                                        <label for="">Akun</label>
                                    </div>
                                </div>
                                <div class="col-3 mb-2">
                                    <div class="form-group">
                                        <label for="">Jenis</label>
                                    </div>
                                </div>
                                <div class="col-3 mb-2">
                                    <div class="form-group">
                                        <label for="">Jumlah</label>
                                    </div>
                                </div>
                                <div class="col-2 mb-2">
                                    <button type="button" class="btn btn-sm btn-primary btn_tambah_pengeluaran"
                                        id="btn_tambah_pengeluaran{{ $d->id }}"
                                        cabang_id="{{ $d->id }}"><i class="bx bxs-plus-circle"></i></button>
                                </div>

                                @if ($d->pengeluaranAkun->count() > 0)
                                    @foreach ($d->pengeluaranAkun as $p)
                                        <div class="col-4 mb-2">
                                            <div class="form-group">
                                                <select name="akun_id" class="form-control" required>
                                                    @foreach ($akun as $a)
                                                        <option value="{{ $a->id }}"
                                                            {{ $a->id == $p->akun_id ? 'selected' : '' }}>
                                                            {{ $a->nm_akun }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-3 mb-2">
                                            <div class="form-group">
                                                <select name="akun_id" class="form-control" required>
                                                    <option value="1" {{ 1 == $p->jenis ? 'selected' : '' }}>Harian
                                                    </option>
                                                    <option value="2" {{ 2 == $p->jenis ? 'selected' : '' }}>
                                                        Pertransaksi</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-3 mb-2">
                                            <div class="form-group">
                                                <input type="number" class="form-control" value="{{ $p->jumlah }}"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-2 mt-3">
                                            <a href="" class="btn btn-sm btn-primary mt-2"><i
                                                    class="bx bxs-trash"></i></a>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="col-4 mb-2">
                                        <div class="form-group">
                                            <select name="akun_id" class="form-control" required>
                                                @foreach ($akun as $a)
                                                    <option value="{{ $a->id }}">{{ $a->nm_akun }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-3 mb-2">
                                        <div class="form-group">
                                            <select name="akun_id" class="form-control" required>
                                                <option value="1">Harian</option>
                                                <option value="2">Pertransaksi</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-3 mb-2">
                                        <div class="form-group">
                                            <input type="number" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-2 mt-3">

                                    </div>
                                @endif




                            </div>
                            <div id="tambah_table_pengeluaran{{ $d->id }}"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
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

            <?php if(session('error')): ?>
            Swal.fire({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                icon: 'error',
                title: "{{ session('error') }}"
            });
            <?php endif; ?>


            $(document).on('submit', '#form_add_data', function(event) {
                $('#btn_add_data').attr('disabled', true);
                $('#btn_add_data').html('Loading...');

            });

            //pengeluaran
            var count_pengeluaran = 1;
            $(document).on('click', '.btn_tambah_pengeluaran', function() {
                count_pengeluaran = count_pengeluaran + 1;
                var cabang_id = $(this).attr('cabang_id');
                var html_code = '<div class="row" id="row' + count_pengeluaran + '">';

                html_code +=
                    '<div class="col-4 mb-2"><div class="form-group"><select name="akun_id" class="form-control" required>@foreach ($akun as $a)<option value="{{ $a->id }}">{{ $a->nm_akun }}</option>@endforeach</select></div></div>';

                html_code +=
                    '<div class="col-3 mb-2"><div class="form-group"><select name="akun_id" class="form-control" required><option value="1">Harian</option><option value="2">Pertransaksi</option></select></div></div>';

                html_code +=
                    '<div class="col-3 mb-2"><div class="form-group"><input type="number" class="form-control" required></div></div>';

                html_code += '<div class="col-2 mt-3"><button type="button" data-row="row' +
                    count_pengeluaran +
                    '" class="btn btn-primary btn-sm remove_pengeluaran"><i class="bx bx-minus"></i></button></div>';

                html_code += "</div>";

                $('#tambah_table_pengeluaran' + cabang_id).append(html_code);
            });

            $(document).on('click', '.remove_pengeluaran', function() {
                var delete_row = $(this).data("row");
                $('#' + delete_row).remove();
            });
            //end pengeluaran

        });
    </script>
@endsection
@endsection
