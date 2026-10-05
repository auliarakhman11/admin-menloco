<?php
$file = "D:/programming/Laravel/admin-manloco/resources/views/laporan_keuangan/index.blade.php";
$content = file_get_contents($file);

$cabangSelect = '
                        <div class="mb-3 col-md-12">
                            <label class="form-label">Cabang</label>
                            <select name="cabang_id" class="form-select" required>
                                <option value="">Pilih Cabang</option>
                                @foreach ($cabang as $c)
                                    <option value="{{ $c->id }}">{{ $c->nama }}</option>
                                @endforeach
                            </select>
                        </div>';

$cabangSelectRow = '
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Cabang</label>
                            <select name="cabang_id" class="form-select" required>
                                <option value="">Pilih Cabang</option>
                                @foreach ($cabang as $c)
                                    <option value="{{ $c->id }}">{{ $c->nama }}</option>
                                @endforeach
                            </select>
                        </div>';

$modals = [
    'modalProduk',
    'modalTambahPendapatanDll',
    'modalDana',
    'modalDanaKeluar',
    'modalSaldoData',
    'modalSaldoDataKeluar',
    'modalPenarikan',
    'modalSaldoLaba',
    'modalSaldoLabaKeluar'
];

foreach ($modals as $modal) {
    // find <div class="modal fade" id="$modal" ...> and inject right after @csrf
    $pattern = '/(<div class="modal fade" id="'.$modal.'".*?@csrf)/is';
    
    // We can inject $cabangSelect after @csrf. But wait, some forms use `<div class="row g-3">`.
    // It's safer to just inject it before the first `<div class="col-md-` or `<div class="mb-3`
    
    // Let's do string replacement
    // Find the modal block
    if (preg_match('/<div class="modal fade" id="'.$modal.'".*?<\/form>/is', $content, $matches)) {
        $modalBlock = $matches[0];
        // Now replace inside this modal block
        
        // If it hasn't been added yet
        if (strpos($modalBlock, 'name="cabang_id"') === false) {
            // Find @csrf and append the branch select
            if (strpos($modalBlock, '<div class="row g-3">') !== false) {
                // inject inside row
                $newModalBlock = preg_replace('/(<div class="row g-3">)/', "$1\n$cabangSelectRow", $modalBlock, 1);
            } elseif (strpos($modalBlock, '<div class="row">') !== false) {
                // inject inside row
                $newModalBlock = preg_replace('/(<div class="row">)/', "$1\n$cabangSelectRow", $modalBlock, 1);
            } else {
                // inject after @csrf
                $newModalBlock = preg_replace('/(@csrf)/', "$1\n$cabangSelect", $modalBlock, 1);
            }
            $content = str_replace($modalBlock, $newModalBlock, $content);
        }
    }
}

file_put_contents($file, $content);
echo "Done\n";
