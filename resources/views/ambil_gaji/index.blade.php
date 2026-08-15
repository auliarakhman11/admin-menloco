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
                                    <h5 class="float-start">Laporan Pendapatan Capster</h5>
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
                                    <button type="submit" class="btn btn-sm btn-primary mt-4 float-end"><i
                                            class='bx bx-search'></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                        <div class="row">
                            <div class="col-12"><button type="button" class="btn btn-sm btn-primary float-end"
                                    id="btn_tambah_data" data-bs-toggle="modal" data-bs-target="#modal_add"><i
                                        class='bx bxs-plus-circle'></i>
                                    Tambah Data</button></div>
                        </div>


                    </div>

                    <div class="card-body">

                        <div class="table-responsive">
                            <table class="table table-sm text-center" width="100%" id="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Tanggal</th>
                                        <th>Karyawan</th>
                                        <th>Cabang</th>
                                        <th>Jumlah</th>
                                        <th>User</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $i = 1;
                                        $total = 0;
                                    @endphp
                                    @foreach ($ambil_gaji as $d)
                                        @php
                                            $total += $d->jumlah;
                                        @endphp
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ date('d/m/Y', strtotime($d->tgl)) }}</td>
                                            <td>{{ $d->karyawan->nama }}</td>
                                            <td>{{ $d->cabang->nama }}</td>
                                            <td>{{ number_format($d->jumlah, 0) }}</td>
                                            <td>{{ $d->user->name }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                    data-bs-target="#modal_edit{{ $d->id }}"
                                                    tgl="{{ $d->tgl }}"><i class="bx bx-edit"></i>
                                                </button>
                                                <a href="{{ route('deleteAmbilGaji', $d->id) }}"
                                                    onclick="return confirm('Apakah anda yakin ingin menghapus data?');"
                                                    class="btn btn-sm btn-primary"><i class="bx bx-trash"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4"><b>Total</b></td>
                                        <td><b>{{ number_format($total, 0) }}</b></td>
                                        <td colspan="2"></td>
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


    <form id="form_add" method="POST" action="{{ route('addAmbilGaji') }}">
        @csrf
        <div class="modal fade" id="modal_add" tabindex="-1" aria-labelledby="modal_addLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal_addLabel">Tambah Data</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">

                            <div class="col-12 mb-2">
                                <div class="form-group">
                                    <label for="">Tanggal</label>
                                    <input type="date" name="tgl" id="tanggal" class="form-control" required>
                                </div>
                            </div>

                            <div class="col-12 mb-2">
                                <div class="form-group">
                                    <label for="">Karyawan</label>
                                    <select name="karyawan_id" id="karyawan_id" class="form-control" required>
                                        <option value="">Pilih Karyawan</option>
                                        @foreach ($karyawan as $d)
                                            <option value="{{ $d->id }}">{{ $d->nama }}
                                                ({{ $d->cabang->nama }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 mb-2 d-none" id="info_gaji_container">
                                <div class="alert alert-info mb-0 py-2">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Total Pendapatan:</span>
                                        <strong id="info_pendapatan">Rp 0</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Total Kasbon:</span>
                                        <strong id="info_kasbon">Rp 0</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Sudah Diambil:</span>
                                        <strong id="info_sudah_diambil">Rp 0</strong>
                                    </div>
                                    <hr class="my-2">
                                    <div class="d-flex justify-content-between">
                                        <span>Sisa Maksimal:</span>
                                        <strong class="text-primary" id="info_sisa_maksimal">Rp 0</strong>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 mb-2">
                                <div class="form-group">
                                    <label for="">Jumlah</label>
                                    <input type="number" name="jumlah" id="jumlah" class="form-control"
                                        min="0" required>
                                </div>
                            </div>


                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="btn_add">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @foreach ($ambil_gaji as $d)
        <form method="POST" action="{{ route('editAmbilGaji') }}">
            @csrf
            @method('patch')
            <div class="modal fade" id="modal_edit{{ $d->id }}" tabindex="-1" aria-labelledby="modal_addLabel"
                aria-hidden="true">
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
                                        <label for="">Karyawan</label>
                                        <select name="karyawan_id" class="form-control" required>
                                            @foreach ($karyawan as $k)
                                                <option value="{{ $k->id }}"
                                                    {{ $k->id == $d->karyawan_id ? 'selected' : '' }}>{{ $k->nama }}
                                                    ({{ $k->cabang->nama }})
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
        // Format angka ke Rupiah
        function formatRupiah(angka) {
            return 'Rp ' + parseInt(angka || 0).toLocaleString('id-ID');
        }

        var sisaMaksimalGaji = 0;

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

            // Reset form tambah data untuk transaksi baru
            function resetFormAdd() {
                $('#form_add')[0].reset();
                $('#info_gaji_container').addClass('d-none');
                $('#jumlah').prop('disabled', false).removeAttr('max').removeData('sisa-maksimal');
                sisaMaksimalGaji = 0;
            }

            // Ambil dan tampilkan detail kalkulasi gaji berdasarkan tanggal & karyawan
            function loadDetailGaji() {
                var tanggal = $('#tanggal').val();
                var karyawanId = $('#karyawan_id').val();

                if (!tanggal || !karyawanId) {
                    $('#info_gaji_container').addClass('d-none');
                    $('#jumlah').prop('disabled', false).removeAttr('max').removeData('sisa-maksimal');
                    sisaMaksimalGaji = 0;
                    return;
                }

                $('#jumlah').prop('disabled', true);

                $.ajax({
                    url: "{{ route('ambil-gaji.get-data-karyawan') }}",
                    type: 'GET',
                    data: {
                        tanggal: tanggal,
                        karyawan_id: karyawanId
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            var data = response.data;
                            sisaMaksimalGaji = parseFloat(data.sisa_maksimal) || 0;

                            $('#info_pendapatan').text(formatRupiah(data.total_pendapatan));
                            $('#info_kasbon').text(formatRupiah(data.total_kasbon));
                            $('#info_sudah_diambil').text(formatRupiah(data.total_sudah_diambil));
                            $('#info_sisa_maksimal').text(formatRupiah(data.sisa_maksimal));

                            $('#info_gaji_container').removeClass('d-none');
                            $('#jumlah').prop('disabled', false).attr('max', sisaMaksimalGaji).data(
                                'sisa-maksimal', sisaMaksimalGaji);
                        }
                    },
                    error: function(xhr) {
                        $('#info_gaji_container').addClass('d-none');
                        $('#jumlah').prop('disabled', false).removeAttr('max').removeData(
                            'sisa-maksimal');
                        sisaMaksimalGaji = 0;

                        var message = 'Gagal mengambil data kalkulasi gaji.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            message = Object.values(xhr.responseJSON.errors).flat().join(', ');
                        }

                        Swal.fire({
                            icon: 'error',
                            position: 'top-end',
                            title: 'Error',
                            text: message
                        });
                    }
                });
            }

            // Reset form saat tombol Tambah Data diklik (transaksi baru)
            $('#btn_tambah_data').on('click', function() {
                resetFormAdd();
            });

            // Muat ulang detail gaji saat modal dibuka kembali dengan nilai yang sudah terisi
            $('#modal_add').on('shown.bs.modal', function() {
                loadDetailGaji();
            });

            // Kembalikan state tombol submit saat modal ditutup
            $('#modal_add').on('hidden.bs.modal', function() {
                $('#btn_add').attr('disabled', false).html('Save');
            });

            // Ambil data kalkulasi gaji saat tanggal atau karyawan berubah
            $('#modal_add').on('change', '#tanggal, #karyawan_id', function() {
                loadDetailGaji();
            });

            // Validasi nominal tidak melebihi sisa maksimal
            function validasiJumlahGaji(showAlert) {
                var jumlah = parseFloat($('#jumlah').val()) || 0;
                var batas = sisaMaksimalGaji;

                if (batas > 0 && jumlah > batas) {
                    if (showAlert) {
                        Swal.fire({
                            icon: 'warning',
                            position: 'top-end',
                            title: 'Peringatan',
                            text: 'Nominal melebihi sisa batas maksimal gaji yang bisa diambil (' +
                                formatRupiah(batas) + ')'
                        });
                    }
                    $('#jumlah').val(batas);
                    return false;
                }

                return true;
            }

            $('#jumlah').on('input keyup change', function() {
                validasiJumlahGaji(true);
            });

            $(document).on('submit', '#form_add', function(event) {
                if (!validasiJumlahGaji(true)) {
                    event.preventDefault();
                    return false;
                }

                $('#btn_add').attr('disabled', true);
                $('#btn_add').html('Loading..');
            });




        });
    </script>
@endsection
@endsection
