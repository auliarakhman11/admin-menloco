<?php
$file = "D:/programming/Laravel/admin-manloco/resources/views/laporan_keuangan/index.blade.php";
$content = file_get_contents($file);

// Replace <th>Keterangan</th> with <th>CABANG</th> <th>Keterangan</th> ONLY if it's not preceded by Cabang.
// Wait, the easiest way is to just blindly replace it and then dedup any <th>Cabang</th> <th>CABANG</th>.
$content = str_replace(
    "<th>Jumlah</th>\n                                      <th>Keterangan</th>",
    "<th>Jumlah</th>\n                                      <th>CABANG</th>\n                                      <th>Keterangan</th>",
    $content
);
$content = str_replace(
    "<th>Input By</th>",
    "<th>CABANG</th>\n                                      <th>Input By</th>",
    $content
);

file_put_contents($file, $content);
echo "Done\n";
