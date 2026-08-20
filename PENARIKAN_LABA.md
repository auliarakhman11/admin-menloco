# Fitur Penarikan Laba Investor

Dokumentasi implementasi fitur penarikan laba bersih investor di Laravel.

## 1. Perintah Artisan
```bash
php artisan make:model PenarikanLaba
```

## 2. Model (`app/Models/PenarikanLaba.php`)

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenarikanLaba extends Model
{
    use HasFactory;

    protected $table = 'penarikan_laba';

    protected $fillable = [
        'investor_id',
        'tgl',
        'jumlah',
    ];

    protected $casts = [
        'tgl' => 'date',
        'jumlah' => 'double',
    ];

    public function investor()
    {
        return $this->belongsTo(Investor::class, 'investor_id');
    }
}
```

## 3. Route (`routes/web.php`)

```php
use App\Http\Controllers\PenarikanLabaController;

Route::post('/penarikan-laba', [PenarikanLabaController::class, 'store'])->name('penarikan.store');
```

## 4. Controller (`app/Http/Controllers/PenarikanLabaController.php`)

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PenarikanLaba;

class PenarikanLabaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'investor_id' => 'required|exists:investors,id',
            'tgl'         => 'required|date',
            'jumlah'      => 'required|numeric|min:1',
        ]);

        PenarikanLaba::create([
            'investor_id' => $request->investor_id,
            'tgl'         => $request->tgl,
            'jumlah'      => $request->jumlah,
        ]);

        return redirect()->back()->with('success', 'Penarikan laba berhasil dicatat.');
    }
}
```

## 5. View & Script (Bootstrap + jQuery)

```html
<!-- Tombol Tambah -->
<div class="d-flex align-items-center gap-2">
    <span>{{ $investor->nama }}</span>
    <button type="button" 
            class="btn btn-sm btn-success btn-tarik" 
            data-id="{{ $investor->id }}" 
            data-nama="{{ $investor->nama }}">
        + Tarik Laba
    </button>
</div>

<!-- Modal Form Penarikan -->
<div class="modal fade" id="modalPenarikan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('penarikan.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Penarikan Laba - <span id="namaInvestorModal"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="investor_id" id="modal_investor_id">
                    
                    <div class="mb-3">
                        <label for="tgl" class="form-label">Tanggal Penarikan</label>
                        <input type="date" class="form-control" name="tgl" id="tgl" value="{{ date('Y-m-d') }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="jumlah" class="form-label">Jumlah Penarikan (Rp)</label>
                        <input type="number" class="form-control" name="jumlah" id="jumlah" min="1" placeholder="Masukkan nominal" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Penarikan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.btn-tarik').on('click', function() {
        let investorId = $(this).data('id');
        let investorNama = $(this).data('nama');

        $('#modal_investor_id').val(investorId);
        $('#namaInvestorModal').text(investorNama);
        
        $('#modalPenarikan').modal('show');
    });
});
</script>
```
