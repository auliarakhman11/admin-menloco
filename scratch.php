<?php
$file = "D:/programming/Laravel/admin-manloco/app/Http/Controllers/LaporanKeuanganController.php";
$content = file_get_contents($file);

$replace = function($model) use (&$content) {
    // Regex explanation:
    // Match ModelName::followed by one of the query starting methods (where, with, whereBetween, whereNotNull, whereIn)
    // Then match its parentheses and contents.
    $content = preg_replace('/('.$model.'::(where|with|whereBetween|whereNotNull|whereIn)\([^\)]+\))/', '$1->when($request->cabang_id && $request->cabang_id != \'all\', function ($q) use ($request) { $q->where(\'cabang_id\', $request->cabang_id); })', $content);
};

$models = ['Penjualan', 'Pendapatan', 'PenjualanKaryawan', 'Kasbon', 'AmbilGaji', 'SaldoGaji', 'Jurnal', 'SaldoOperasional', 'Dana', 'PembelianProduk', 'PenarikanLaba'];
foreach ($models as $m) {
    $replace($m);
}

// Ensure store methods get cabang_id
$content = str_replace(
    "'tgl' => \$request->tanggal,", 
    "'tgl' => \$request->tanggal,\n            'cabang_id' => \$request->cabang_id,", 
    $content
);

$content = str_replace(
    "'tgl'    => \$request->tgl,", 
    "'tgl'    => \$request->tgl,\n            'cabang_id' => \$request->cabang_id,", 
    $content
);

$content = str_replace(
    "'tgl'         => \$request->tgl,", 
    "'tgl'         => \$request->tgl,\n            'cabang_id' => \$request->cabang_id,", 
    $content
);

file_put_contents($file, $content);
echo "Done\n";
