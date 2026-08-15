### PERBAIKAN FITUR: MENAMPILKAN KEMBALI DETAIL GAJI SAAT MODAL DIBUKA

Tolong perbarui logika Javascript pada View (@resources/views/ambil_gaji/index.blade.php) untuk menangani kondisi ketika modal `#modal_add` ditutup lalu dibuka kembali:

1. **Refactor Fungsi AJAX:**
   - Bungkus logika AJAX pengambilan data gaji ke dalam fungsi terpisah, misalnya: `function loadDetailGaji()`.
   - Fungsi ini mengecek apakah input `tanggal` dan `karyawan_id` di dalam `#modal_add` sudah terisi.
   - Jika kedua input terisi, jalankan request AJAX GET ke endpoint `ambil-gaji.get-data-karyawan` dan tampilkan kontainer ringkasan info gaji beserta batas maksimal input `#jumlah`.

2. **Event Listener:**
   - Panggil `loadDetailGaji()` saat ada event `change` pada elemen `#tanggal` atau `#karyawan_id`.
   - Panggil juga `loadDetailGaji()` saat event modal Bootstrap dipicu ketika modal dibuka kembali:
     ```javascript
     $('#modal_add').on('shown.bs.modal', function () {
         loadDetailGaji();
     });
     ```

3. **Penanganan Reset Modal (Opsional):**
   - Jika tombol "Tambah Data" (`#btn_tambah_data`) diklik untuk membuat transaksi *baru*, pastikan form dan kontainer info detail di-reset/disembunyikan terlebih dahulu agar data bekas transaksi sebelumnya tidak tertinggal.

Tolong sesuaikan Javascript jQuery di view tersebut agar detail gaji langsung tampil otomatis jika nilai pegawai & tanggal sudah terisi saat modal dibuka kembali.