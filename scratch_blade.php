<?php
$file = "D:/programming/Laravel/admin-manloco/resources/views/laporan_keuangan/index.blade.php";
$content = file_get_contents($file);

// 1. Add Filter Cabang in the header
$filterHtml = '
                                <div class="col-md-3">
                                    <label for="cabang_id" class="form-label">Cabang</label>
                                    <select name="cabang_id" id="cabang_id" class="form-select">
                                        <option value="all" {{ request(\'cabang_id\') == \'all\' ? \'selected\' : \'\' }}>Semua Cabang</option>
                                        @foreach ($cabang as $c)
                                            <option value="{{ $c->id }}" {{ request(\'cabang_id\') == $c->id ? \'selected\' : \'\' }}>{{ $c->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>';

$content = preg_replace('/(<div class="col-md-4">[\s]*<label for="start_date" class="form-label">Tanggal Mulai<\/label>)/', $filterHtml . "\n                                " . '$1', $content);
$content = preg_replace('/<div class="col-md-4"([^>]*>[\s]*<label for="start_date")/', '<div class="col-md-3"$1', $content);
$content = preg_replace('/<div class="col-md-4"([^>]*>[\s]*<label for="end_date")/', '<div class="col-md-3"$1', $content);
$content = preg_replace('/<div class="col-md-4 d-flex gap-2">/', '<div class="col-md-3 d-flex gap-2">', $content);

// 2. Add Cabang input to modals
$cabangSelect = '
                        <div class="mb-3">
                            <label class="form-label">Cabang</label>
                            <select name="cabang_id" class="form-select" required>
                                <option value="">Pilih Cabang</option>
                                @foreach ($cabang as $c)
                                    <option value="{{ $c->id }}">{{ $c->nama }}</option>
                                @endforeach
                            </select>
                        </div>';

// Adding to specific modals by injecting before <div class="mb-3"> <label class="form-label">Tanggal</label> or similar
$modalsToInject = [
    'modalDana',
    'modalDanaKeluar',
    'modalSaldoData',
    'modalSaldoDataKeluar',
    'modalTambahPendapatanDll',
    'modalProduk',
    'modalPenarikan'
];

foreach ($modalsToInject as $modal) {
    $content = preg_replace('/(<div class="modal fade" id="'.$modal.'".*?<form.*?>.*?(?:<div class="modal-body">))([\s]*<div class="mb-3">)/is', '$1' . $cabangSelect . '$2', $content);
}

// 3. Add Cabang Name to detail and delete tables
// It's harder with regex without breaking things. We'll do our best.
// Search for th "Keterangan", inject "Cabang"
$content = preg_replace('/<th>(Keterangan|KETERANGAN)<\/th>/', "<th>CABANG</th>\n                                                    <th>$1</th>", $content);

// Inside td loops we need to inject the cabang name. Since we don't have the explicit model data loop easily identifiable,
// we will just do a str_replace for known variables like $dn, $p, $j, $so, $sg
// Wait, the detail tables are rendered dynamically via JS from $detailDana, etc? Or are they hardcoded in the modals?
// Let's inspect the blade file in the next step to see how table bodies are built.

file_put_contents($file, $content);
echo "Done\n";
