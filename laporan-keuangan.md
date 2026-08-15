### PERAN & TUJUAN
Kamu adalah asisten AI yang bertugas memperbaiki tampilan dan kalkulasi khusus pada baris **Gaji/Komisi Capster** di tabel Laporan Keuangan Laravel 9.
Masalah saat ini: Kolom Pengeluaran, Aktual Harian, dan Total Aktual pada baris Gaji/Komisi Capster belum muncul atau tidak terisi dengan benar di Blade view (`index.blade.php`).

### FILE TERKAIT (CONTEXT)
- Controller: `@app/Http/Controllers/LaporanKeuanganController.php`
- View: `@resources/views/laporan_keuangan/index.blade.php`
- Model Terlibat: `@app/Models/Kasbon.php`, `@app/Models/AmbilGaji.php`, `@app/Models/PenjualanKaryawan.php`

---

### SPESIFIKASI PEKERJAAN DETAIL (PRESISI KOLOM HTML)

#### 1. CONTROLLER (`LaporanKeuanganController.php`)
Pastikan variabel berikut dihitung dengan tepat pada method `index(Request $request)` berdasarkan rentang `$startDate` dan `$endDate` (dengan batas minimal `2026-07-01`):

1. **Saldo Gaji Capster (`$gajiCapster`)**:
   - PenjualanKaryawan dengan `jenis_service` 1 dan 2 (`SUM(harga)`).
2. **Pengeluaran Gaji Capster (`$pengeluaranGajiCapster`)**:
   - Total Kasbon: `Kasbon::where('void', 0)->whereDate('tanggal', '>=', $startDate)->whereDate('tanggal', '<=', $endDate)->sum('jumlah');`
   - Total Ambil Gaji: `AmbilGaji::where('void', 0)->whereDate('tanggal', '>=', $startDate)->whereDate('tanggal', '<=', $endDate)->sum('jumlah');`
   - `$pengeluaranGajiCapster = $totalKasbon + $totalAmbilGaji;`
3. **Aktual Harian Capster (`$aktualGajiCapster`)**:
   - `$aktualGajiCapster = $gajiCapster - $pengeluaranGajiCapster;`
4. **Total Aktual (`$totalAktualCapster`)**:
   - `$totalAktualCapster = $aktualGajiCapster;`

Oper semua variabel ini ke `return view('laporan_keuangan.index', compact(...))`.

---

#### 2. VIEW HTML (`index.blade.php`)
Pastikan struktur baris HTML `<tr>` untuk **Gaji/Komisi Capster** memiliki susunan kolom (`<td>`) yang lengkap dan presisi sesuai urutan header tabel Laporan Keuangan.

**Struktur 8 Kolom Tabel Laporan Keuangan:**
1. Kolom 1: Nama Kategori / Keterangan
2. Kolom 2: Saldo Tunai (`-`)
3. Kolom 3: Saldo Bank / Non-Tunai (`-`)
4. Kolom 4: **SALDO (LAPORAN HARIAN)** -> Isi `$gajiCapster`
5. Kolom 5: **PENGELUARAN (LAPORAN HARIAN)** -> Isi `$pengeluaranGajiCapster`
6. Kolom 6: **AKTUAL HARIAN (LAPORAN HARIAN)** -> Isi `$aktualGajiCapster`
7. Kolom 7: **TOTAL AKTUAL** -> Isi `$totalAktualCapster`
8. Kolom 8: Keterangan / Aksi (`-`)

**Kode Blade HTML Presisi untuk Baris Gaji/Komisi Capster:**
```html
<tr>
    <td class="fw-bold">Gaji/Komisi Capster</td>
    <td class="text-center">-</td>
    <td class="text-center">-</td>
    <td class="text-end">Rp {{ number_format($gajiCapster, 0, ',', '.') }}</td>
    <td class="text-end text-danger">Rp {{ number_format($pengeluaranGajiCapster, 0, ',', '.') }}</td>
    <td class="text-end text-primary fw-bold">Rp {{ number_format($aktualGajiCapster, 0, ',', '.') }}</td>
    <td class="text-end text-success fw-bold">Rp {{ number_format($totalAktualCapster, 0, ',', '.') }}</td>
    <td class="text-center">-</td>
</tr>
```

---

### CATATAN EKSEKUSI
- Jangan biarkan kolom 5 (Pengeluaran), kolom 6 (Aktual Harian), dan kolom 7 (Total Aktual) berisi tanda strip `-` pada baris Gaji/Komisi Capster.
- Pastikan nama kolom tanggal di tabel `kasbon` dan `ambil_gaji` disesuaikan (misal: `tanggal` atau `created_at`).