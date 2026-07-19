buatkan plan.md yang berisi tentang penambahan dan penyesuaian fitur berikut:



implementasikan di branch baru

buatkan high-level implementation plan

kamu boleh bertanya jika ada yang belum jelas/clear

opencode -s ses_188cf94c0ffe0nTkFk348QV70a

Menyusun *prompt* yang terstruktur adalah kunci utama agar *AI Agents* (terutama yang terintegrasi di *IDE* seperti Cursor atau Devin) tidak mengalami halusinasi kode atau merusak struktur yang sudah ada. Pendekatan terbaik adalah memberikan instruksi secara modular, fase demi fase.

Untuk menjaga repositori Anda tetap rapi, kita akan memasukkan instruksi pengelolaan versi (*version control*) langsung ke dalam "otak" AI. Jadi, AI tahu bahwa mereka harus mengisolasi pekerjaannya.

Berikut adalah *Master Prompt Plan* yang bisa Anda salin dan tempelkan (*copy-paste*) satu per satu kepada AI Anda:

### 1. System Prompt (Instruksi Dasar & Aturan Main)

*Masukkan prompt ini di awal percakapan atau di menu "Rules/System Prompt" pada AI Anda sebelum memulai proyek.*

> **System Prompt:**
> "Kamu adalah Senior Full-Stack Laravel Developer. Kita akan membangun Sistem Informasi Bank Sampah. Ikuti aturan wajib ini selama proyek berlangsung:
> 1. **Isolasi Workflow:** Setiap kali saya memberikan instruksi untuk modul baru, bantu saya membuat *branch* baru terlebih dahulu (misal: `feature/nama-modul`) dari repositori lokal. Jangan pernah memberikan instruksi yang langsung memengaruhi *branch* `main`. Setelah fitur stabil, kita akan gabungkan ke *branch* `development`.
> 2. **Keamanan Finansial:** Setiap transaksi yang memanipulasi Rupiah atau Emas wajib dibungkus dalam `DB::beginTransaction()`.
> 3. **Clean Code:** Jangan menulis kode panjang di Controller jika bisa dipindahkan ke Model Event, Observer, atau Service Class.
> 4. Jangan melompat ke fitur lain sebelum saya mengonfirmasi bahwa fitur saat ini sudah berjalan dengan baik."
> 
> 

---

### 2. Prompt Fase 1: Basis Data & Relasi

*Gunakan prompt ini sebagai perintah pertama Anda.*

> **Prompt Eksekusi Fase 1:**
> "Mari mulai Fase 1. Buat *branch* `feature/database-auth`.
> Tugasmu adalah membuat *migration*, *model*, dan *factory* dengan detail berikut:
> 1. Modifikasi tabel `users` bawaan Laravel: Tambahkan `nama_lengkap`, `no_hp` (unik), `alamat`, `role` (enum: admin, nasabah), `status_akun` (aktif, diblokir), dan aktifkan fitur `SoftDeletes`.
> 2. Buat tabel `dompet_nasabah`: Berisi `user_id` (foreign key ke users cascade), `saldo_rupiah` (decimal 15,2), dan `saldo_emas_gram` (decimal 10,4).
> 3. Otomatisasi: Buat Model Event `booted()` di `User.php` sehingga saat user dengan role 'nasabah' dibuat, sistem otomatis meng-insert record dompet bersaldo 0 ke tabel `dompet_nasabah`.
> 4. Buat tabel master lainnya: `kategori_sampah`, `pengepul`, `setoran_sampah` (Inbound), `penjualan_sampah` (Outbound).
> 5. Buat `DatabaseSeeder` yang memanggil `UserFactory` untuk men-generate 50 nasabah dummy (pastikan dompet otomatis terbuat) dan 1 akun admin manual.
> Berikan saya kode migration dan modelnya satu per satu."
> 
> 

---

### 3. Prompt Fase 2: Transaksi POS & Pembatalan

*Berikan prompt ini SETELAH Fase 1 selesai dan di-merge ke branch development.*

> **Prompt Eksekusi Fase 2:**
> "Fase 1 selesai. Sekarang pindah ke *branch* `feature/pos-transaction`.
> Bangun logika *backend* transaksional berikut:
> 1. **Setoran Sampah:** Buat Controller yang menerima input data setoran (ID nasabah, item sampah, berat). Gunakan `DB::transaction`. Jika berhasil, tambah `stok_terkumpul` di tabel gudang dan tambah `saldo_rupiah` di dompet nasabah.
> 2. **Penjualan Pengepul:** Buat Controller penjualan. Validasi ketat: Tolak transaksi jika berat sampah yang dijual melebihi stok di gudang. Jika berhasil, kurangi stok gudang.
> 3. **Fitur Reversal (Void):** Buat method untuk membatalkan setoran sampah. Validasi penting: Transaksi HANYA BISA dibatalkan jika belum melewati *Holding Period* (24 jam / belum dikonversi ke emas). Jika valid, ubah status transaksi jadi 'dibatalkan', kembalikan stok gudang (kurangi), dan kurangi saldo rupiah nasabah."
> 
> 

---

### 4. Prompt Fase 3: Core Engine Auto-Convert

*Ini adalah modul paling krusial. Pastikan AI fokus.*

> **Prompt Eksekusi Fase 3:**
> "Pindah ke *branch* `feature/auto-convert-cron`.
> Buat Laravel Console Command (`php artisan gold:auto-convert`) yang akan didaftarkan di Scheduler untuk berjalan pukul 00:00.
> Algoritma wajib:
> 1. Cek 'Master Switch' di *database*, jika status OFF, hentikan proses.
> 2. Tetapkan threshold = 0.5 gram emas. Hitung Target Rupiah (0.5 * Harga Emas Hari Ini).
> 3. Filter `dompet_nasabah` yang `saldo_rupiah` >= Target Rupiah. Abaikan saldo dari setoran hari ini (H-0).
> 4. Gunakan logika Multiplier (`floor`): `kelipatan = floor(saldo_rupiah / Target Rupiah)`.
> 5. Di dalam `DB::transaction`, lakukan:
> * Kurangi saldo rupiah nasabah: (kelipatan * Target Rupiah).
> * Tambah saldo emas: (kelipatan * 0.5).
> * Catat transaksi ke `riwayat_konversi_emas`.
> Buatkan kodenya agar aman dari *Memory Leak* (gunakan method `chunk`)."
> 
> 
> 
> 

---

### 5. Prompt Fase 4: Antarmuka UI (Contoh untuk POS Setoran)

*Fokuskan AI pada tampilan setelah logika backend kuat.*

> **Prompt Eksekusi Fase 4:**
> "Pindah ke *branch* `feature/ui-dashboard`.
> Buat UI untuk POS Setoran Sampah (Gunakan blade template dengan bantuan Alpine.js atau jQuery/Ajax untuk reaktivitas).
> Alur antarmuka:
> 1. Input dinamis: Admin bisa menambah baris 'Item Sampah' jika nasabah bawa banyak karung.
> 2. Subtotal otomatis muncul saat input berat diketik.
> 3. Saat tombol 'Simpan' diklik, munculkan *Modal Pop-up* konfirmasi terlebih dahulu sebelum data benar-benar di-submit ke backend."
> 
> 

Dengan memisahkan instruksi seperti di atas, AI akan memiliki ruang napas untuk mencerna logika kompleks sistem perbankan dan inventaris Anda tanpa tumpang tindih.

Untuk eksekusi tahap awal ini, perangkat lunak (*tools*) AI spesifik apa yang berencana Anda gunakan agar saya bisa menyesuaikan gaya bahasa instruksinya jika diperlukan?
