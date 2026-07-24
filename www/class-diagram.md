# Class Diagram — Bank Sampah

## Daftar Entitas Bisnis

| Class | Atribut | Method | Relasi |
|---|---|---|---|
| **Pengguna** | username, email, password, role (admin/nasabah) | login(), logout(), daftar() | 1-1 ke Nasabah |
| **Nasabah** | nik, nama, jenisKelamin, tempatLahir, alamat, noHp | daftar(), editProfil(), lihatSaldo(), totalEmasGram() | 1-1 ke Dompet, 1-1 ke Pengguna, 1-M ke Setoran, 1-M ke KonversiEmas |
| **Dompet** | saldoRupiah, saldoEmasGram | — | 1-1 milik Nasabah |
| **Pengepul** | nama, alamat, noHp, status | daftar(), edit() | 1-M ke Penjualan |
| **KategoriSampah** | namaKategori, keterangan | tambah(), edit() | 1-M ke JenisSampah |
| **JenisSampah** | namaJenis, hargaPerKg, stok | tambah(), edit(), updateStok() | M-1 ke KategoriSampah, 1-M ke DetailSetoran, 1-M ke DetailPenjualan |
| **Setoran** | kodeSetoran, totalHarga, status (sukses/batal) | buatSetoran(), batalkan() | M-1 ke Nasabah, 1-M ke DetailSetoran |
| **DetailSetoran** | berat, hargaPerKg, subtotal | — | M-1 ke Setoran, M-1 ke JenisSampah |
| **Penjualan** | kodePenjualan, tanggal, totalHarga, status (sukses/batal) | buatPenjualan(), batalkan() | M-1 ke Pengepul, 1-M ke DetailPenjualan |
| **DetailPenjualan** | berat, hargaPerKg, subtotal | — | M-1 ke Penjualan, M-1 ke JenisSampah |
| **KonversiEmas** | saldoTerpakai, hargaEmasPerGram, jumlahGram, sisaSaldoRupiah, totalSaldoEmas | — | M-1 ke Nasabah |
| **Pengaturan** | key, value | ambilNilai() | — |

## Relasi Antar Class

```text
Pengguna (1) ──── (1) Nasabah (1) ──── (M) Setoran (1) ──── (M) DetailSetoran (M) ──── (1) JenisSampah
                          │                                                     │
                          │                                                     │
                          (1)                                                 (1)
                          │                                                     │
                          │                                                     │
                        Dompet                                        KategoriSampah
                          │
                          (M)
                          │
                      KonversiEmas

Pengepul (1) ──── (M) Penjualan (1) ──── (M) DetailPenjualan (M) ──── (1) JenisSampah

Pengaturan (independent)
```

## Keterangan Relasi

| Relasi | Tipe | Penjelasan |
|---|---|---|
| Pengguna — Nasabah | 1-1 | Setiap akun pengguna (role nasabah) memiliki satu data nasabah |
| Nasabah — Dompet | 1-1 | Setiap nasabah memiliki satu dompet ( saldo rupiah + saldo emas gram) |
| Nasabah — Setoran | 1-M | Satu nasabah dapat melakukan banyak setoran |
| Setoran — DetailSetoran | 1-M | Satu setoran terdiri dari banyak item sampah |
| DetailSetoran — JenisSampah | M-1 | Setiap item setoran merujuk ke satu jenis sampah |
| Pengepul — Penjualan | 1-M | Satu pengepul dapat melakukan banyak pembelian |
| Penjualan — DetailPenjualan | 1-M | Satu penjualan terdiri dari banyak item sampah |
| DetailPenjualan — JenisSampah | M-1 | Setiap item penjualan merujuk ke satu jenis sampah |
| Nasabah — KonversiEmas | 1-M | Satu nasabah dapat memiliki banyak riwayat konversi emas |
| KategoriSampah — JenisSampah | 1-M | Satu kategori dapat memiliki banyak jenis sampah |
| Pengaturan | — | Tabel konfigurasi sistem (master switch, dll) |
