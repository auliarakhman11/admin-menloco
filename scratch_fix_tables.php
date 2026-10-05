<?php
$file = "D:/programming/Laravel/admin-manloco/resources/views/laporan_keuangan/index.blade.php";
$content = file_get_contents($file);

// 1. Pendapatan DLL
// It already has <th>CABANG</th> from my previous script. Let's fix the tbody.
$content = preg_replace(
    '/(<td class="text-end fw-semibold">Rp\s*\{\{\s*number_format\(\$item->jumlah, 0, \',\', \'\.\'\)\s*\}\}<\/td>)\s*(<td>\{\{\s*\$item->ket\s*\}\}<\/td>)/i',
    "$1\n                                          <td>{{ \$item->cabang->nama ?? '-' }}</td>\n                                          $2",
    $content
);

// 2. Pembelian Produk
// Header: Add CABANG before INPUT BY
$content = preg_replace(
    '/(<th>JUMLAH \(RP\)<\/th>)\s*(<th>INPUT BY<\/th>)/i',
    "$1\n                                      <th>CABANG</th>\n                                      $2",
    $content
);
// Tbody: Add CABANG before user
$content = preg_replace(
    '/(<td>\{\{\s*number_format\(\$item->jumlah, 0, \',\', \'\.\'\)\s*\}\}<\/td>)\s*(<td>\{\{\s*\$item->user->name \?\? \'-\'\s*\}\}<\/td>)/i',
    "$1\n                                          <td>{{ \$item->cabang->nama ?? '-' }}</td>\n                                          $2",
    $content
);

// 3. DIV (4 tables: detailDana, detailDanaKeluar, detailDanaSaldoMasuk, detailDanaSaldoKeluar)
// Header: Add CABANG before KETERANGAN
$content = preg_replace(
    '/(<th>JUMLAH<\/th>)\s*(<th>KETERANGAN<\/th>)/i',
    "$1\n                                      <th>CABANG</th>\n                                      $2",
    $content
);
// Tbody: Add CABANG before ket
$content = preg_replace(
    '/(<td class="text-end fw-semibold">Rp\s*\{\{\s*number_format\(\$item->jumlah, 0, \',\', \'\.\'\)\s*\}\}<\/td>)\s*(<td>\{\{\s*\$item->ket\s*\}\}<\/td>)/i',
    "$1\n                                          <td>{{ \$item->cabang->nama ?? '-' }}</td>\n                                          $2",
    $content
);

// 4. Penarikan Laba (detailPenarikanLaba)
// Header: Add CABANG before Jumlah
$content = preg_replace(
    '/(<th>Investor<\/th>)\s*(<th>Jumlah<\/th>)/i',
    "$1\n                                      <th>Cabang</th>\n                                      $2",
    $content
);
// Tbody: Add CABANG before jumlah
$content = preg_replace(
    '/(<td>\{\{\s*\$d->investor->nm_investor\s*\}\}<\/td>)\s*(<td class="text-end">\{\{\s*number_format\(\$d->jumlah, 0, \',\', \'\.\'\)\s*\}\}<\/td>)/i',
    "$1\n                                          <td>{{ \$d->cabang->nama ?? '-' }}</td>\n                                          $2",
    $content
);


file_put_contents($file, $content);
echo "Done\n";
