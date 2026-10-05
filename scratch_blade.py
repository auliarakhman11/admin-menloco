import re

file_path = "D:/programming/Laravel/admin-manloco/resources/views/laporan_keuangan/index.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

filter_html = '''
                                <div class="col-md-3">
                                    <label for="cabang_id" class="form-label">Cabang</label>
                                    <select name="cabang_id" id="cabang_id" class="form-select">
                                        <option value="all" {{ request('cabang_id') == 'all' ? 'selected' : '' }}>Semua Cabang</option>
                                        @foreach ($cabang as $c)
                                            <option value="{{ $c->id }}" {{ request('cabang_id') == $c->id ? 'selected' : '' }}>{{ $c->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>'''

# 1. Filter
content = re.sub(r'(<div class="col-md-4">\s*<label for="start_date" class="form-label">Tanggal Mulai</label>)', filter_html + r'\n                                \1', content, count=1)
content = re.sub(r'<div class="col-md-4"([^>]*>\s*<label for="start_date")', r'<div class="col-md-3"\1', content, count=1)
content = re.sub(r'<div class="col-md-4"([^>]*>\s*<label for="end_date")', r'<div class="col-md-3"\1', content, count=1)
content = re.sub(r'<div class="col-md-4 d-flex gap-2">', r'<div class="col-md-3 d-flex gap-2">', content, count=1)

# 2. Modals
cabang_select = '''
                        <div class="mb-3">
                            <label class="form-label">Cabang</label>
                            <select name="cabang_id" class="form-select" required>
                                <option value="">Pilih Cabang</option>
                                @foreach ($cabang as $c)
                                    <option value="{{ $c->id }}">{{ $c->nama }}</option>
                                @endforeach
                            </select>
                        </div>'''

modals = [
    'modalDana',
    'modalDanaKeluar',
    'modalSaldoData',
    'modalSaldoDataKeluar',
    'modalTambahPendapatanDll',
    'modalProduk',
    'modalPenarikan'
]

for modal in modals:
    # We find <div class="modal fade" id="modalDana"... then find the first <div class="mb-3"> inside the form/modal-body
    content = re.sub(r'(<div class="modal fade" id="' + modal + r'".*?<form.*?(?:<div class="modal-body">))(\s*<div class="mb-3">)', r'\1' + cabang_select + r'\2', content, count=1, flags=re.DOTALL)


# 3. Table Headers
content = re.sub(r'<th>(Keterangan|KETERANGAN)</th>', r'<th>CABANG</th>\n                                                    <th>\1</th>', content)

# 4. Table Cells
content = re.sub(r'(<td>{{\s*\$p->ket\s*}}</td>)', r"<td>{{ $p->cabang->nama ?? '-' }}</td>\n                                                        \1", content)
content = re.sub(r'(<td>{{\s*\$dn->ket\s*}}</td>)', r"<td>{{ $dn->cabang->nama ?? '-' }}</td>\n                                                        \1", content)
content = re.sub(r'(<td>{{\s*\$item->service->nm_service\s*}}</td>)', r"<td>{{ $item->cabang->nama ?? '-' }}</td>\n                                                        \1", content)
content = re.sub(r'(<td>{{\s*\$pl->investor->nama\s*}}</td>)', r"<td>{{ $pl->cabang->nama ?? '-' }}</td>\n                                                        \1", content)


with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)

print("Done")
