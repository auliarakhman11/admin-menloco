@extends('template.master')

@section('content')


    <!-- Content -->

    <style>
        /* .blick {
                                                                                                                        background-color: #ed1a3a;
                                                                                                                    }
                                                                                                                    .blink-soft {
                                                                                                                        animation: blinker 1.5s linear infinite;
                                                                                                                    }
                                                                                                                    @keyframes blinker {
                                                                                                                        50% {
                                                                                                                            opacity: 0;
                                                                                                                        }
                                                                                                                    } */

        .blink {
            animation: blink 1.5s linear infinite;
        }

        @keyframes blink {
            0% {
                background-color: red;
                color: white;
            }

            50% {
                background-color: orange;
                color: white;
            }

            100% {
                background-color: rgb(223, 195, 9);
                color: white;
            }
        }
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
                        <h5 class="float-start">Data User</h5>
                        <button type="button" class="btn btn-sm btn-primary float-end" data-bs-toggle="modal"
                            data-bs-target="#modal_add_user"><i class='bx bxs-plus-circle'></i> Tambah Data</button>
                    </div>

                    <div class="card-body">

                        <div class="table-responsive">
                            <table class="table table-sm text-center" width="100%" id="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nama</th>
                                        <th>Username</th>
                                        <th>Jenis</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $i = 1;
                                    @endphp
                                    @foreach ($user as $d)
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $d->name }}</td>
                                            <td>{{ $d->username }}</td>
                                            <td>{{ $d->role_id == 1 ? 'Super User' : 'Admin' }}</td>
                                            <td><button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                    data-bs-target="#modal_edit_user{{ $d->id }}"><i
                                                        class='bx bxs-message-square-edit'></i></button></td>
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

    <form method="POST" action="{{ route('addUser') }}">
        @csrf
        <div class="modal fade" id="modal_add_user" tabindex="-1" aria-labelledby="modal_add_userLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered ">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal_add_userLabel">Tambah User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">

                            <div class="col-12 mb-2">
                                <div class="form-group">
                                    <label for="">Nama</label>
                                    <input type="text" name="name" class="form-control" required>
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
                                    <label for="">Role/</label>
                                    <select name="role_id" class="form-control" required>
                                        <option value="">Pilih Jenis</option>
                                        <option value="1">Super User</option>
                                        <option value="3">Admin</option>
                                    </select>
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

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="">Akses Cabang</label>
                                    <div class="row">
                                        <div class="col-4"><label for="check_all_cabang"><input type="checkbox"
                                                    id="check_all_cabang" value="" name=""> All</label></div>
                                        @foreach ($cabang as $c)
                                            <div class="col-4"><label for="cabang{{ $c->id }}"><input
                                                        type="checkbox" id="cabang{{ $c->id }}" class="cabang"
                                                        value="{{ $c->id }}" name="cabang_id[]">
                                                    {{ $c->nama }}</label></div>
                                        @endforeach

                                    </div>
                                </div>

                            </div>


                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="btn_edit_user">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </form>


    @foreach ($user as $u)
        <form method="POST" action="{{ route('editUser') }}">
            @csrf
            <div class="modal fade" id="modal_edit_user{{ $u->id }}" tabindex="-1"
                aria-labelledby="modal_add_userLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered ">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modal_add_userLabel">Edit User</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">

                                <input type="hidden" name="id" value="{{ $u->id }}">
                                <div class="col-12 mb-2">
                                    <div class="form-group">
                                        <label for="">Nama</label>
                                        <input type="text" name="name" class="form-control"
                                            value="{{ $u->name }}" required>
                                    </div>
                                </div>


                                <div class="col-12 mb-2">
                                    <div class="form-group">
                                        <label for="">Role</label>
                                        <select name="role_id" class="form-control" required>
                                            <option value="1" {{ $u->role_id == 1 ? 'selected' : '' }}>Super User
                                            </option>
                                            <option value="3" {{ $u->role_id == 3 ? 'selected' : '' }}>Admin</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label for="">Akses Cabang</label>
                                    <div class="row">
                                        <div class="col-4">
                                            <label for=""><input type="checkbox" id="check_all_cabang"
                                                    value="" name=""> All</label>
                                        </div>
                                        @if ($u->aksesCabang)
                                            @foreach ($cabang as $k)
                                                @php
                                                    $nilai = 0;
                                                @endphp
                                                @foreach ($u->aksesCabang as $ak)
                                                    @if ($ak->cabang_id == $k->id)
                                                        @php
                                                            $nilai++;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                @if ($nilai)
                                                    <div class="col-4"><label for=""><input type="checkbox"
                                                                class="cabang" value="{{ $k->id }}"
                                                                name="cabang_id[]" checked> {{ $k->nama }}</label>
                                                    </div>
                                                @else
                                                    <div class="col-4"><label for=""><input type="checkbox"
                                                                class="cabang" value="{{ $k->id }}"
                                                                name="cabang_id[]"> {{ $k->nama }}</label></div>
                                                @endif
                                            @endforeach
                                        @else
                                            @foreach ($cabang as $k)
                                                <div class="col-12"><label for=""><input type="checkbox"
                                                            class="cabang" value="{{ $k->id }}"
                                                            name="cabang_id[]">
                                                        {{ $k->nama }}</label></div>
                                            @endforeach
                                        @endif


                                    </div>

                                </div>


                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="btn_edit_user">Save</button>
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

            // <?php if($errors->any()): ?>
            // Swal.fire({
            //     toast: true,
            //     position: 'top-end',
            //     showConfirmButton: false,
            //     timer: 3000,
            //     icon: 'error',
            //     title: ' Ada data yang tidak sesuai, periksa kembali'
            // });
            // <?php endif; ?>

            $(document).on('click', '#check_all_cabang', function() {
                if (this.checked) {
                    $('.cabang').prop('checked', true);
                } else {
                    $('.cabang').prop('checked', false);
                }

            });

            $(document).on('click', '.cabang', function() {

                $('#check_all_cabang').prop('checked', false);


            });



        });
    </script>
@endsection
@endsection
