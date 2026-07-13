# Plan: Data Sampah untuk Nasabah

Implementasikan plan ini pada branch baru

## Deskripsi

Tambahkan menu "Data Sampah" di sidebar nasabah untuk melihat daftar jenis sampah beserta kategori dan harga per kg. Tanpa kolom stok.

## Yang Diubah

### 1. Route (`routes/web.php`)

Tambah route di group nasabah:

```php
Route::get('/data-sampah', [DashboardController::class, 'dataSampah'])->name('data-sampah');
```

### 2. Controller (`app/Http/Controllers/Nasabah/DashboardController.php`)

Tambah method `dataSampah()`:

```php
public function dataSampah()
{
    $jenisSampahs = JenisSampah::with('kategoriSampah')->latest()->paginate(10);
    return view('nasabah.data-sampah', compact('jenisSampahs'));
}
```

### 3. Sidebar (`resources/views/layouts/sidebar-nasabah.blade.php`)

Tambah menu "Data Sampah" di section "Aktivitas Saya", setelah "Info Saldo":

```html
<li
    class="nav-item {{ request()->routeIs('nasabah.data-sampah') ? 'active' : '' }}"
>
    <a class="nav-link" href="{{ route('nasabah.data-sampah') }}">
        <i class="fas fa-fw fa-trash"></i>
        <span>Data Sampah</span>
    </a>
</li>
```

### 4. View Baru (`resources/views/nasabah/data-sampah.blade.php`)

- Extend `layouts.app`
- Tabel: No | Kategori | Jenis Sampah | Harga/kg
- Tanpa kolom stok
- Pagination 10/halaman
- Style mengikuti SB Admin 2 (card shadow, table-bordered, dll)
