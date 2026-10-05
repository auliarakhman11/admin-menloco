<?php
$file = "D:/programming/Laravel/admin-manloco/resources/views/laporan_keuangan/index.blade.php";
$lines = file($file);

$out = [];
foreach ($lines as $i => $line) {
    // 1. Pendapatan DLL & DIV tbody ($item->ket)
    if (strpos($line, '<td>{{ $item->ket }}</td>') !== false) {
        $out[] = "                                          <td>{{ \$item->cabang->nama ?? '-' }}</td>\n";
    }
    
    // 2. Produk tbody ($item->user->name)
    // Wait, let's make sure it's inside the modalProduk context.
    if (strpos($line, '<td>{{ $item->user->name ?? \'-\' }}</td>') !== false) {
        $out[] = "                                          <td>{{ \$item->cabang->nama ?? '-' }}</td>\n";
    }

    // 3. Penarikan Laba tbody (number_format($d->jumlah))
    if (strpos($line, 'class="text-end">{{ number_format($d->jumlah') !== false) {
        // we add cabang BEFORE jumlah, so we output it here before adding the line
        $out[] = "                                          <td>{{ \$d->cabang->nama ?? '-' }}</td>\n";
    }

    // Output the current line
    $out[] = $line;

    // Headers
    // 1. Produk Header (before INPUT BY)
    if (strpos($line, '<th>INPUT BY</th>') !== false) {
        // remove the last line we just added and add CABANG first
        array_pop($out);
        $out[] = "                                      <th>CABANG</th>\n";
        $out[] = $line;
    }
    // 2. Laba Header (before Jumlah)
    // There are multiple "<th>Jumlah</th>", but we want the one in the Laba table (next to Investor)
    if (strpos($line, '<th>Investor</th>') !== false) {
        $out[] = "                                      <th>CABANG</th>\n";
    }
}

file_put_contents($file, implode("", $out));
echo "Done\n";
