<div class="table-responsive">
    <table class="table table-sm table-striped">
        <thead>
            <tr class="text-center">
                <th>#</th>
                <th>Antrian</th>
                <th>Waktu Selesai</th>
                <th>Invoice</th>
                <th>Customer</th>
                <th>Kapster</th>
                <th>Total</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total = 0;
                $i = 1;
            @endphp
            @foreach ($laporan as $d)
                @php
                    $total += $d->total - $d->diskon;
                @endphp
                <tr class="text-center">
                    <td>{{ $i++ }}</td>
                    <td>{{ $d->no_antrian }}</td>
                    <td>{{ date('d/m/Y H:i', strtotime($d->updated_at)) }}</td>
                    <td>{{ $d->no_invoice }}</td>
                    <td>{{ $d->nm_customer }}</td>
                    <td>
                        @if ($d->penjualanKaryawan)
                            @foreach ($d->penjualanKaryawan as $kr)
                                {{ $kr->karyawan->nama }} <br>
                            @endforeach
                        @endif
                    </td>
                    <td>{{ number_format($d->total - $d->diskon, 0) }}</td>
                    <td>
                        <button type="button" invoice_id="{{ $d->id }}"
                            class="btn btn-sm btn-primary detail_penjualan" data-bs-toggle="modal"
                            data-bs-target="#modal_detail_penjualan"><i class='bx bx-search'></i></button>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5"><b>Total</b></td>
                <td><b>{{ number_format($total, 0) }}</b></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>
