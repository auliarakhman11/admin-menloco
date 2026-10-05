const fs = require('fs');

const file = 'D:/programming/Laravel/admin-manloco/resources/views/laporan_keuangan/index.blade.php';
let content = fs.readFileSync(file, 'utf8');

// 1. Pendapatan DLL
// Header: Add CABANG if not present. Oh wait, my PHP script added CABANG.
// Let's ensure the data row has CABANG.
content = content.replace(/(<td class="text-end fw-semibold">Rp\s*\{\{\s*number_format\(\$item->jumlah, 0, ',', '\.'\)\s*\}\}<\/td>)\s*(<td>\{\{\s*\$item->ket\s*\}\}<\/td>)/gi, "$1\n                                          <td>{{ $item->cabang->nama ?? '-' }}</td>\n                                          $2");

// 2. Produk
// Header: Add CABANG before INPUT BY
content = content.replace(/(<th>JUMLAH \(RP\)<\/th>)\s*(<th>INPUT BY<\/th>)/gi, "$1\n                                      <th>CABANG</th>\n                                      $2");
// Tbody
content = content.replace(/(<td>\{\{\s*number_format\(\$item->jumlah, 0, ',', '\.'\)\s*\}\}<\/td>)\s*(<td>\{\{\s*\$item->user->name \?\? '-'\s*\}\}<\/td>)/gi, "$1\n                                          <td>{{ $item->cabang->nama ?? '-' }}</td>\n                                          $2");


// 3. DIV (Dana, DanaKeluar, SaldoMasuk, SaldoKeluar)
// Header
content = content.replace(/(<th>Jumlah<\/th>)\s*(<th>CABANG<\/th>)\s*(<th>Keterangan<\/th>)/gi, "$1\n                                      $2\n                                      $3"); // wait, I already added CABANG
// Tbody
// The regex above for Pendapatan DLL will also match DIV because DIV uses $item->jumlah and $item->ket !

// 4. Laba
// Header
content = content.replace(/(<th>Investor<\/th>)\s*(<th>Jumlah<\/th>)/gi, "$1\n                                      <th>Cabang</th>\n                                      $2");
// Tbody
content = content.replace(/(<td>\{\{\s*\$d->investor->nm_investor\s*\}\}<\/td>)\s*(<td class="text-end">\{\{\s*number_format\(\$d->jumlah, 0, ',', '\.'\)\s*\}\}<\/td>)/gi, "$1\n                                          <td>{{ $d->cabang->nama ?? '-' }}</td>\n                                          $2");

fs.writeFileSync(file, content);
console.log("Done");
