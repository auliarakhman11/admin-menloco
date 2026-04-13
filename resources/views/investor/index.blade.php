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
                        <h5 class="float-start">Data Investor</h5>
                        <button type="button" class="btn btn-sm btn-primary float-end" data-bs-toggle="modal"
                            data-bs-target="#modal_tambah"><i class='bx bxs-plus-circle'></i> Tambah Data</button>
                    </div>

                    <div class="card-body">

                        <div class="table-responsive">
                            <table class="table table-sm text-center" width="100%" id="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nama Investor</th>
                                        <th>Persen</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $i = 1;
                                    @endphp
                                    @foreach ($investor as $d)
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $d->nm_investor }}</td>
                                            <td>
                                                @if ($d->persenInvestor)
                                                    @foreach ($d->persenInvestor as $pi)
                                                        {{ $pi->cabang->nama }} / {{ $pi->persen }}%
                                                    @endforeach
                                                @endif
                                            </td>

                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                    data-bs-target="#modal_edit{{ $d->id }}"><i
                                                        class='bx bxs-message-square-edit'></i></button>

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

    <form id="form_add" method="POST" action="{{ route('addInvestor') }}">
        @csrf
        <div class="modal fade" id="modal_tambah" aria-labelledby="modal_tambahLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered ">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal_tambahLabel">Tambah Investor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <div class="row">

                            <div class="col-12">
                                <label>Nama</label>
                                <input type="text" name="nm_investor" class="form-control" placeholder="Masukan nama"
                                    required>
                            </div>

                            {{-- <div class="col-12 col-md-6">
                                <label>Username</label>
                                <input type="text" name="username" class="form-control" placeholder="Masukan username"
                                    required>
                            </div>

                            <div class="col-12 col-md-6">
                                <label>Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Masukan password"
                                    required>
                            </div>

                            <div class="col-12 col-md-6">
                                <label>Password</label>
                                <input type="password" name="password_confirmation" class="form-control"
                                    placeholder="Ulangi password" required>
                            </div> --}}

                            <div class="col-6">
                                <label>Outlet</label>
                                <select name="cabang_id[]" class="form-control" required>
                                    <option value="">-Pilih Outlet-</option>
                                    @foreach ($cabang as $o)
                                        <option value="{{ $o->id }}">{{ $o->nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-5">
                                <label>Persen</label>
                                <input type="text" class="form-control" name="persen[]" required>
                            </div>



                        </div>

                        <div id="tambah_persen"></div>

                        <button type="button" class="btn btn-sm btn-primary float-right mt-2"
                            id="btn_tambah_persen">+</button>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="btn_add">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @foreach ($investor as $d)
        <form method="POST" action="{{ route('editInvestor') }}">
            @csrf
            @method('patch')
            <div class="modal fade" id="modal_edit{{ $d->id }}" tabindex="-1" aria-labelledby="modal_editLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered ">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modal_editLabel">Edit Investor</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">

                                <input type="hidden" name="id" value="{{ $d->id }}">

                                <div class="col-12 col-md-12">
                                    <label>Nama</label>
                                    <input type="text" name="nm_investor" class="form-control"
                                        value="{{ $d->nm_investor }}" required>
                                </div>


                                <div class="col-6">
                                    <label>Outlet</label>
                                </div>

                                <div class="col-5">
                                    <label>Persen</label>

                                </div>

                                <div class="col-1">
                                    Aksi
                                </div>

                                @foreach ($d->persenInvestor as $p)
                                    <div class="col-6">
                                        <input type="hidden" name="persen_id[]" value="{{ $p->id }}">
                                        <input type="hidden" name="cabang_id_old[]" value="{{ $p->cabang_id }}">
                                        <select name="cabang_id[]" class="form-control" required>
                                            @foreach ($cabang as $o)
                                                <option {{ $p->cabang_id == $o->id ? 'selected' : '' }}
                                                    value="{{ $o->id }}">
                                                    {{ $o->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-5">
                                        <input type="text" class="form-control" name="persen[]"
                                            value="{{ $p->persen }}" required>
                                    </div>

                                    <div class="col-1">
                                        <a href="{{ route('deletePersenInvestor', $p->id) }}"
                                            onclick="return confirm('Apakah anda yakin?');"
                                            class="btn btn-xs btn-primary mt-2"><i class="bx bx-trash"></i></a>
                                    </div>
                                @endforeach


                            </div>

                            <div id="tambah_edit_persen{{ $d->id }}"></div>

                            <button type="button" class="btn btn-sm btn-primary float-right mt-2 btn_tambah_edit_persen"
                                investor_id="{{ $d->id }}">+</button>
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


            $(document).on('submit', '#form_add', function(event) {
                $('#btn_add').attr('disabled', true);
                $('#btn_add').html('Loading...');

            });

            var count_persen = 1;
            $(document).on('click', '#btn_tambah_persen', function() {
                count_persen = count_persen + 1;
                var html_code = '<div class="row" id="row_persen' + count_persen + '">';

                html_code +=
                    '<div class="col-6"><select name="cabang_id[]" class="form-control" required><option value="" >-Pilih Outlet-</option>@foreach ($cabang as $o)<option value="{{ $o->id }}" >{{ $o->nama }}</option>@endforeach</select></div>';

                html_code +=
                    '<div class="col-5"><input type="text" class="form-control" name="persen[]" required></div>';

                html_code += '<div class="col-1"><button type="button" data-row="row_persen' +
                    count_persen + '" class="btn btn-primary btn-sm remove_persen">-</button></div>';

                html_code += "</div>";

                $('#tambah_persen').append(html_code);
                // $('.select2bs4').select2({
                //     theme: 'bootstrap4'
                // });
            });

            $(document).on('click', '.remove_persen', function() {
                var delete_row = $(this).data("row");
                $('#' + delete_row).remove();
            });


            var count_edit_persen = 1;
            $(document).on('click', '.btn_tambah_edit_persen', function() {

                var investor_id = $(this).attr('investor_id');

                count_edit_persen = count_edit_persen + 1;
                var html_code = '<div class="row" id="row_persen_edit' + count_edit_persen + investor_id +
                    '">';

                html_code +=
                    '<div class="col-6"><select name="cabang_id_edit[]" class="form-control" required><option value="" >-Pilih Outlet-</option>@foreach ($cabang as $o)<option value="{{ $o->id }}" >{{ $o->nama }}</option>@endforeach</select></div>';

                html_code +=
                    '<div class="col-5"><input type="text" class="form-control" name="persen_edit[]" required></div>';

                html_code += '<div class="col-1"><button type="button" data-row="row_persen_edit' +
                    count_edit_persen + investor_id +
                    '" class="btn btn-primary btn-sm remove_edit_persen">-</button></div>';

                html_code += "</div>";

                $('#tambah_edit_persen' + investor_id).append(html_code);
                // $('.select2bs4').select2({
                //     theme: 'bootstrap4'
                // });
            });

            $(document).on('click', '.remove_edit_persen', function() {
                var delete_row = $(this).data("row");
                $('#' + delete_row).remove();
            });


        });
    </script>
@endsection
@endsection
