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
                        <h5 class="float-start">Data Karyawan</h5>
                        <button type="button" class="btn btn-sm btn-primary float-end" data-bs-toggle="modal"
                            data-bs-target="#modal_add_karyawan"><i class='bx bxs-plus-circle'></i> Tambah Data</button>
                    </div>

                    <div class="card-body">

                        <div class="table-responsive">
                            <table class="table table-sm text-center" width="100%" id="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Karyawan</th>
                                        <th>No Telepon</th>
                                        <th>Alamat</th>
                                        <th>Tanggal Masuk</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $i = 1;
                                    @endphp
                                    @foreach ($karyawan as $d)
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $d->nama }}</td>
                                            <td>{{ $d->no_tlp }}</td>
                                            <td>{{ $d->alamat }}</td>
                                            <td>{{ $d->tgl_masuk ? date('d/m/Y', strtotime($d->tgl_masuk)) : '' }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                    data-bs-target="#modal_edit_karyawan{{ $d->id }}"><i
                                                        class='bx bxs-message-square-edit'></i></button>
                                                <a href="{{ route('deleteKaryawan', $d->id) }}"
                                                    onclick="return confirm('Apakah anda yakin?');"
                                                    class="btn btn-sm btn-primary"><i class='bx bxs-trash'></i></a>
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

    <form id="form_add_karyawan" method="POST" action="{{ route('addKaryawan') }}">
        @csrf
        <div class="modal fade" id="modal_add_karyawan" tabindex="-1" aria-labelledby="modal_add_karyawanLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered ">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal_add_karyawanLabel">Tambah Karyawan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">

                            <div class="col-12 mb-2">
                                <div class="form-group">
                                    <label for="">Nama Karyawan</label>
                                    <input type="text" name="nama" class="form-control" required>
                                </div>
                            </div>

                            <div class="col-12 mb-2">
                                <div class="form-group">
                                    <label for="">Nomor Telepon</label>
                                    <input type="number" name="no_tlp" class="form-control">
                                </div>
                            </div>

                            <div class="col-12 mb-2">
                                <div class="form-group">
                                    <label for="">Alamat</label>
                                    <textarea class="form-control" name="alamat" cols="30" rows="10"></textarea>
                                </div>
                            </div>

                            <div class="col-12 mb-2">
                                <div class="form-group">
                                    <label for="">Tanggal Masuk</label>
                                    <input type="date" name="tgl_masuk" class="form-control">
                                </div>
                            </div>

                            {{-- <div class="col-12 mb-2">
                                <div class="form-group">
                                    <label for="">Pembagian Hasil</label>
                                    <input type="text" name="pembagian" class="form-control" value="0" required>
                                </div>
                            </div> --}}


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

    @foreach ($karyawan as $d)
        <form method="POST" action="{{ route('editKaryawan') }}">
            @csrf
            @method('patch')
            <div class="modal fade" id="modal_edit_karyawan{{ $d->id }}" tabindex="-1"
                aria-labelledby="modal_edit_karyawanLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered ">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modal_edit_karyawanLabel">Tambah Karyawan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">

                                <input type="hidden" name="id" value="{{ $d->id }}">

                                <div class="col-12 mb-2">
                                    <div class="form-group">
                                        <label for="">Nama Karyawan</label>
                                        <input type="text" name="nama" class="form-control"
                                            value="{{ $d->nama }}" required>
                                    </div>
                                </div>

                                <div class="col-12 mb-2">
                                    <div class="form-group">
                                        <label for="">Nomor Telepon</label>
                                        <input type="number" name="no_tlp" class="form-control"
                                            value="{{ $d->no_tlp }}">
                                    </div>
                                </div>

                                <div class="col-12 mb-2">
                                    <div class="form-group">
                                        <label for="">Alamat</label>
                                        <textarea class="form-control" name="alamat" cols="30" rows="10">{{ $d->alamat }}</textarea>
                                    </div>
                                </div>

                                <div class="col-12 mb-2">
                                    <div class="form-group">
                                        <label for="">Tanggal Masuk</label>
                                        <input type="date" name="tgl_masuk" class="form-control"
                                            value="{{ $d->tgl_masuk }}">
                                    </div>
                                </div>

                                {{-- <div class="col-12 mb-2">
                                    <div class="form-group">
                                        <label for="">Pembagian Hasil</label>
                                        <input type="text" name="pembagian" class="form-control"
                                            value="{{ $d->pembagian }}" required>
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


            $(document).on('submit', '#form_add_karyawan', function(event) {
                $('#btn_add_karyawan').attr('disabled', true);
                $('#btn_add_karyawan').html('Loading...');

            });

        });
    </script>
@endsection
@endsection
