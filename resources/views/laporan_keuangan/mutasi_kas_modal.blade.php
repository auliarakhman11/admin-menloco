<!-- Modal Mutasi Kas -->
<div class="modal fade" id="modalMutasiKas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title text-white">Mutasi Kas / Pindah Saldo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Form Input -->
                <form action="{{ route('storeMutasiKas') }}" method="POST">
                    @csrf
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="tgl" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Cabang</label>
                            <select name="cabang_id" class="form-select" required>
                                @foreach ($cabang as $c)
                                    <option value="{{ $c->id }}" {{ request('cabang_id') == $c->id ? 'selected' : '' }}>{{ $c->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Transfer DARI</label>
                            <select name="sumber_pembayaran_id" class="form-select" required>
                                <option value="1">Cash</option>
                                <option value="2">Transfer / QRIS</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Transfer KE</label>
                            <select name="tujuan_pembayaran_id" class="form-select" required>
                                <option value="2">Transfer / QRIS</option>
                                <option value="1">Cash</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nominal (Rp)</label>
                            <input type="number" name="jumlah" class="form-control" min="1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Keterangan</label>
                            <input type="text" name="ket" class="form-control" placeholder="Contoh: Setor tunai ke BCA" required>
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-primary">Simpan Mutasi</button>
                        </div>
                    </div>
                </form>

                <hr>

                <!-- List Aktif Bulan Ini -->
                <h6 class="mt-4 mb-3">Riwayat Mutasi Kas (Periode Ini)</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm text-center">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Keterangan</th>
                                <th>Dari</th>
                                <th>Ke</th>
                                <th>Nominal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dataMutasiKas->list as $mk)
                                <tr>
                                    <td>{{ date('d-m-Y', strtotime($mk->tgl)) }}</td>
                                    <td>{{ $mk->ket }}</td>
                                    <td><span class="badge bg-danger">{{ $mk->sumber_pembayaran_id == 1 ? 'Cash' : 'Transfer' }}</span></td>
                                    <td><span class="badge bg-success">{{ $mk->tujuan_pembayaran_id == 1 ? 'Cash' : 'Transfer' }}</span></td>
                                    <td class="text-end">Rp {{ number_format($mk->jumlah, 0, ',', '.') }}</td>
                                    <td>
                                        <a href="{{ route('voidMutasiKas', $mk->id) }}" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus / membatalkan mutasi ini?')">
                                            <i class="bx bx-trash"></i> Hapus
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-muted">Tidak ada mutasi kas di periode ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
