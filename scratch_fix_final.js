const fs = require('fs');
const file = 'D:/programming/Laravel/admin-manloco/resources/views/laporan_keuangan/index.blade.php';
const lines = fs.readFileSync(file, 'utf8').split('\n');

for (let i = 0; i < lines.length; i++) {
    // Tbody: Pendapatan DLL and DIV
    if ([1051, 1172, 1293, 1413, 1511].includes(i)) {
        lines[i] = "                                          <td>{{ $item->cabang->nama ?? '-' }}</td>\n" + lines[i];
    }
    // Tbody: Produk
    if (i === 1622) {
        lines[i] = "                                          <td>{{ $item->cabang->nama ?? '-' }}</td>\n" + lines[i];
    }
    
    // Header: Laba
    if ([1947, 2054, 2162].includes(i) && lines[i].includes('<th>Jumlah</th>')) {
        lines[i] = "                                      <th>CABANG</th>\n" + lines[i];
    }
    // Tbody: Laba
    if ([1959, 2066, 2174].includes(i) && lines[i].includes('number_format($d->jumlah')) {
        lines[i] = "                                          <td>{{ $d->cabang->nama ?? '-' }}</td>\n" + lines[i];
    }
}

fs.writeFileSync(file, lines.join('\n'));
console.log('Done');
