const fs = require('fs');

const filePath = "D:/programming/Laravel/admin-manloco/resources/views/laporan_keuangan/index.blade.php";
let content = fs.readFileSync(filePath, 'utf8');

const filterHtml = `
                                <div class="col-md-3">
                                    <label for="cabang_id" class="form-label">Cabang</label>
                                    <select name="cabang_id" id="cabang_id" class="form-select">
                                        <option value="all" {{ request('cabang_id') == 'all' ? 'selected' : '' }}>Semua Cabang</option>
                                        @foreach ($cabang as $c)
                                            <option value="{{ $c->id }}" {{ request('cabang_id') == $c->id ? 'selected' : '' }}>{{ $c->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>`;

content = content.replace(/(<div class="col-md-4">\s*<label for="start_date" class="form-label">Tanggal Mulai<\/label>)/, filterHtml + '\n                                $1');
content = content.replace(/<div class="col-md-4"([^>]*>\s*<label for="start_date")/, '<div class="col-md-3"$1');
content = content.replace(/<div class="col-md-4"([^>]*>\s*<label for="end_date")/, '<div class="col-md-3"$1');
content = content.replace(/<div class="col-md-4 d-flex gap-2">/, '<div class="col-md-3 d-flex gap-2">');

const cabangSelect = `
                        <div class="mb-3">
                            <label class="form-label">Cabang</label>
                            <select name="cabang_id" class="form-select" required>
                                <option value="">Pilih Cabang</option>
                                @foreach ($cabang as $c)
                                    <option value="{{ $c->id }}">{{ $c->nama }}</option>
                                @endforeach
                            </select>
                        </div>`;

const modals = [
    'modalDana',
    'modalDanaKeluar',
    'modalSaldoData',
    'modalSaldoDataKeluar',
    'modalTambahPendapatanDll',
    'modalProduk',
    'modalPenarikan'
];

for (const modal of modals) {
    const regex = new RegExp(`(<div class="modal fade" id="${modal}".*?<form.*?(?:<div class="modal-body">))(\\s*<div class="mb-3">)`, 'is');
    content = content.replace(regex, `$1${cabangSelect}$2`);
}

content = content.replace(/<th>(Keterangan|KETERANGAN)<\/th>/g, '<th>CABANG</th>\n                                                    <th>$1</th>');

content = content.replace(/(<td>{{\s*\$p->ket\s*}}<\/td>)/g, `<td>{{ $p->cabang->nama ?? '-' }}</td>\n                                                        $1`);
content = content.replace(/(<td>{{\s*\$dn->ket\s*}}<\/td>)/g, `<td>{{ $dn->cabang->nama ?? '-' }}</td>\n                                                        $1`);
content = content.replace(/(<td>{{\s*\$item->service->nm_service\s*}}<\/td>)/g, `<td>{{ $item->cabang->nama ?? '-' }}</td>\n                                                        $1`);
content = content.replace(/(<td>{{\s*\$pl->investor->nama\s*}}<\/td>)/g, `<td>{{ $pl->cabang->nama ?? '-' }}</td>\n                                                        $1`);

fs.writeFileSync(filePath, content, 'utf8');
console.log('Done');
