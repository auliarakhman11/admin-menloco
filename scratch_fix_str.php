<?php
$file = "D:/programming/Laravel/admin-manloco/resources/views/laporan_keuangan/index.blade.php";
$content = file_get_contents($file);

// Clean up any stray <td> that was injected at the end of the modal
$content = str_replace("                                          <td>{{ \$item->cabang->nama ?? '-' }}</td>\n    <!-- Modal Detail", "    <!-- Modal Detail", $content);

// 1. Pendapatan DLL & DIV (using $item->ket)
// Find: <td>{{ $item->ket }}</td>
// Replace: <td>{{ $item->cabang->nama ?? '-' }}</td>
//          <td>{{ $item->ket }}</td>
$content = str_replace(
    "<td>{{ \$item->ket }}</td>", 
    "<td>{{ \$item->cabang->nama ?? '-' }}</td>\n                                          <td>{{ \$item->ket }}</td>", 
    $content
);

// 2. Produk (using $item->user->name)
$content = str_replace(
    "<td>{{ \$item->user->name ?? '-' }}</td>", 
    "<td>{{ \$item->cabang->nama ?? '-' }}</td>\n                                          <td>{{ \$item->user->name ?? '-' }}</td>", 
    $content
);
$content = str_replace(
    "<th>INPUT BY</th>", 
    "<th>CABANG</th>\n                                      <th>INPUT BY</th>", 
    $content
);

// 3. Penarikan Laba (using $d->investor->nm_investor and $d->jumlah)
$content = str_replace(
    "<td>{{ \$d->investor->nm_investor }}</td>", 
    "<td>{{ \$d->cabang->nama ?? '-' }}</td>\n                                          <td>{{ \$d->investor->nm_investor }}</td>", 
    $content
);
// For the header, we want to add Cabang before Investor, wait, or after Investor.
// Let's add after Investor (before Jumlah)
$content = str_replace(
    "<th>Investor</th>", 
    "<th>CABANG</th>\n                                      <th>Investor</th>", 
    $content
);

// 4. Saldo Laba (using $d->ket)
$content = str_replace(
    "<td>{{ \$d->ket }}</td>", 
    "<td>{{ \$d->cabang->nama ?? '-' }}</td>\n                                          <td>{{ \$d->ket }}</td>", 
    $content
);

// Write back
file_put_contents($file, $content);
echo "Done\n";
