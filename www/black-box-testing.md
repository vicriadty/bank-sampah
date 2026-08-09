# Black Box Testing — Aplikasi Bank Sampah

**Jenis Pengujian:** Requirement-Based Black Box Testing
**Teknologi:** Laravel 12, MySQL, Elasticsearch, Redis, Docker
**Format Test Case:**

| ID | Nama Fitur | Langkah Pengujian | Input | Hasil yang Diharapkan | Hasil | Keterangan |
|----|-----------|-------------------|-------|----------------------|-------|------------|

---

## Phase 1 — Authentication

### 1.1 Login

| ID | Nama Fitur | Langkah Pengujian | Input | Hasil yang Diharapkan | Hasil | Keterangan |
|----|-----------|-------------------|-------|----------------------|-------|------------|
| AUTH-01 | Login dengan kredensial valid (admin) | Buka halaman `/login`, masukkan username dan password yang benar, klik Login | username: `admin`, password: `admin123` | Redirect ke `/admin/dashboard`, session terbentuk | | Positif |
| AUTH-02 | Login dengan kredensial valid (nasabah) | Buka halaman `/login`, masukkan username dan password nasabah, klik Login | username: `nasabah1`, password: `nasabah123` | Redirect ke `/nasabah/dashboard`, session terbentuk | | Positif |
| AUTH-03 | Login dengan username kosong | Buka halaman `/login`, biarkan field username kosong, klik Login | username: `(kosong)`, password: `admin123` | Tampil pesan error "Username wajib diisi", tidak login | | Negatif — validasi required |
| AUTH-04 | Login dengan password kosong | Buka halaman `/login`, biarkan field password kosong, klik Login | username: `admin`, password: `(kosong)` | Tampil pesan error "Password wajib diisi", tidak login | | Negatif — validasi required |
| AUTH-05 | Login dengan username salah | Buka halaman `/login`, masukkan username yang tidak terdaftar | username: `wronguser`, password: `admin123` | Tampil pesan error "Username atau password salah." | | Negatif — autentikasi gagal |
| AUTH-06 | Login dengan password salah | Buka halaman `/login`, masukkan password yang salah | username: `admin`, password: `wrongpass` | Tampil pesan error "Username atau password salah." | | Negatif — autentikasi gagal |
| AUTH-07 | Login saat sudah terautentikasi | Setelah login, buka `/login` lagi | — | Redirect back ke halaman sebelumnya | | Boundary — session aktif |

### 1.2 Logout

| ID | Nama Fitur | Langkah Pengujian | Input | Hasil yang Diharapkan | Hasil | Keterangan |
|----|-----------|-------------------|-------|----------------------|-------|------------|
| AUTH-08 | Logout berhasil | Klik tombol Logout di navbar, konfirmasi di SweetAlert2 | — | Session dihapus, redirect ke `/login` | | Positif |
| AUTH-09 | Akses halaman setelah logout | Setelah logout, buka `/admin/dashboard` secara langsung | — | Redirect ke `/login` (middleware auth) | | Negatif — akses tanpa login |

### 1.3 Hak Akses Admin

| ID | Nama Fitur | Langkah Pengujian | Input | Hasil yang Diharapkan | Hasil | Keterangan |
|----|-----------|-------------------|-------|----------------------|-------|------------|
| AUTH-10 | Admin mengakses halaman admin | Login sebagai admin, buka `/admin/dashboard` | — | Halaman dashboard admin ditampilkan | | Positif |
| AUTH-11 | Admin mengakses halaman nasabah | Login sebagai admin, buka `/nasabah/dashboard` | — | Akses ditolak atau redirect (role middleware) | | Negatif — unauthorized |
| AUTH-12 | Nasabah mengakses halaman admin | Login sebagai nasabah, buka `/admin/dashboard` | — | Akses ditolak (role:admin middleware) | | Negatif — unauthorized |

### 1.4 Hak Akses Nasabah

| ID | Nama Fitur | Langkah Pengujian | Input | Hasil yang Diharapkan | Hasil | Keterangan |
|----|-----------|-------------------|-------|----------------------|-------|------------|
| AUTH-13 | Nasabah mengakses halaman nasabah | Login sebagai nasabah, buka `/nasabah/dashboard` | — | Halaman dashboard nasabah ditampilkan | | Positif |
| AUTH-14 | Nasabah mengakses halaman admin | Login sebagai nasabah, buka `/admin/dashboard` | — | Akses ditolak (role:nasabah middleware) | | Negatif — unauthorized |

### 1.5 Registrasi

| ID | Nama Fitur | Langkah Pengujian | Input | Hasil yang Diharapkan | Hasil | Keterangan |
|----|-----------|-------------------|-------|----------------------|-------|------------|
| AUTH-15 | Registrasi dengan data valid | Buka `/register`, isi seluruh field dengan data valid, klik Daftar | NIK: `3201234567890001`, nama: `Budi`, jenis_kelamin: `Laki-laki`, tempat_lahir: `Bandung`, tanggal_lahir: `01-01-2000`, alamat: `Jl. Merdeka`, no_hp: `08123456789`, username: `budi`, email: `budi@test.com`, password: `budi123`, password_confirmation: `budi123` | User dan Nasabah dibuat, redirect ke `/login` dengan pesan sukses | | Positif |
| AUTH-16 | Registrasi dengan NIK sudah terdaftar | Isi form dengan NIK yang sudah ada di database | NIK: `3201234567890001` (sudah ada), data lain valid | Tampil pesan error "NIK sudah terdaftar." | | Negatif — validasi unique |
| AUTH-17 | Registrasi dengan username sudah digunakan | Isi form dengan username yang sudah ada | username: `admin` (sudah ada), data lain valid | Tampil pesan error "Username sudah digunakan." | | Negatif — validasi unique |
| AUTH-18 | Registrasi dengan email sudah terdaftar | Isi form dengan email yang sudah ada | email: `admin@test.com` (sudah ada), data lain valid | Tampil pesan error "Email sudah terdaftar." | | Negatif — validasi unique |
| AUTH-19 | Registrasi dengan NIK kurang dari 16 digit | Isi field NIK dengan kurang dari 16 karakter | NIK: `12345` (5 digit) | Tampil pesan error "NIK harus 16 digit." | | Boundary — validasi size |
| AUTH-20 | Registrasi dengan password kurang dari 6 karakter | Isi password dengan kurang dari 6 karakter | password: `abc`, password_confirmation: `abc` | Tampil pesan error "Password minimal 6 karakter." | | Boundary — validasi min |
| AUTH-21 | Registrasi dengan konfirmasi password tidak cocok | Isi password dan konfirmasi berbeda | password: `budi123`, password_confirmation: `salah` | Tampil pesan error "Konfirmasi password tidak cocok." | | Negatif — validasi confirmed |
| AUTH-22 | Registrasi dengan email format tidak valid | Isi email dengan format salah | email: `bukanemail` | Tampil pesan error "Format email tidak valid." | | Negatif — validasi email |
| AUTH-23 | Registrasi dengan field kosong | Klik Daftar tanpa mengisi apapun | Semua field kosong | Tampil pesan error validasi untuk setiap field required | | Negatif — validasi required |
| AUTH-24 | Registrasi dengan tanggal lahir hari ini | Isi tanggal lahir = tanggal hari ini | tanggal_lahir: `(hari ini)` | Tampil pesan error "Tanggal lahir harus sebelum hari ini." | | Boundary — validasi before:today |

### 1.6 Session

| ID | Nama Fitur | Langkah Pengujian | Input | Hasil yang Diharapkan | Hasil | Keterangan |
|----|-----------|-------------------|-------|----------------------|-------|------------|
| AUTH-25 | Session timeout | Login, tunggu session expired (default 120 menit), akses halaman | — | Redirect ke `/login` | | Boundary — session expiry |
| AUTH-26 | Session regenerasi saat login | Login, periksa session ID sebelum dan sesudah login | — | Session ID berubah (regenerate) untuk mencegah session fixation | | Positif — keamanan |
| AUTH-27 | Session invalidasi saat logout | Login, lakukan logout, tekan tombol back browser | — | Tidak bisa mengakses halaman yang dilindungi | | Positif — keamanan |

---

## Phase 2 — Master Data

### 2.1 Nasabah

| ID | Nama Fitur | Langkah Pengujian | Input | Hasil yang Diharapkan | Hasil | Keterangan |
|----|-----------|-------------------|-------|----------------------|-------|------------|
| MD-01 | Lihat daftar nasabah | Login admin, buka halaman nasabah | — | Tabel nasabah ditampilkan dengan pagination 10 data | | Positif |
| MD-02 | Tambah nasabah dengan data valid | Klik Tambah, isi semua field via modal, klik Simpan | NIK: `3201234567890099`, nama: `Siti`, username: `siti`, email: `siti@test.com`, password: `siti1223`, jenis_kelamin: `Perempuan`, tanggal_lahir: `01-01-2000`, tempat_lahir: `Jakarta`, alamat: `Jl. Sudirman`, no_hp: `0812345678` | Nasabah dan akun login dibuat, muncul toast sukses | | Positif |
| MD-03 | Tambah nasabah dengan NIK tidak 16 digit | Klik Tambah, isi NIK kurang dari 16 digit | NIK: `12345` | Validasi error: "NIK harus 16 digit" | | Negatif — validasi size |
| MD-04 | Tambah nasabah dengan field kosong | Klik Tambah, klik Simpan tanpa isi apapun | Semua field kosong | Validasi error untuk setiap field required | | Negatif — validasi required |
| MD-05 | Edit nasabah | Klik tombol Edit pada nasabah, ubah nama, klik Simpan Perubahan | nama: `Siti Aminah` | Data nasabah terupdate, toast sukses | | Positif |
| MD-06 | Edit nasabah dengan NIK duplikat | Ubah NIK nasabah menjadi NIK nasabah lain | NIK: (NIK nasabah lain) | Validasi error atau data terupdate (tergantung unique rule edit) | | Negatif — validasi unique |
| MD-07 | Hapus nasabah tanpa riwayat setoran | Klik tombol Hapus pada nasabah yang belum pernah setor | — | Konfirmasi SweetAlert2, data terhapus | | Positif |
| MD-08 | Hapus nasabah yang memiliki riwayat setoran | Klik tombol Hapus pada nasabah yang punya setoran | — | Error: "Nasabah tidak dapat dihapus karena masih memiliki riwayat setoran." | | Negatif — integritas data |
| MD-09 | Pencarian nasabah | Ketik nama nasabah di field pencarian, klik Filter | nasabah: `Siti` | Tabel menampilkan nasabah yang namanya mengandung "Siti" | | Positif |
| MD-10 | Pencarian nasabah dengan keyword tidak ada | Ketik nama yang tidak ada di database | nasabah: `XYZNOTEXIST` | Tabel menampilkan "Tidak ada data" | | Negatif |

### 2.2 Pengepul

| ID | Nama Fitur | Langkah Pengujian | Input | Hasil yang Diharapkan | Hasil | Keterangan |
|----|-----------|-------------------|-------|----------------------|-------|------------|
| MD-11 | Lihat daftar pengepul | Login admin, buka halaman pengepul | — | Tabel pengepul ditampilkan dengan pagination | | Positif |
| MD-12 | Tambah pengepul dengan data valid | Klik Tambah, isi semua field, klik Simpan | nama: `PT Sampah Jaya`, alamat: `Jl. Industri`, no_hp: `021123456` | Pengepul berhasil ditambahkan | | Positif |
| MD-13 | Tambah pengepul dengan field kosong | Klik Tambah, klik Simpan tanpa isi | Semua field kosong | Validasi error: nama, alamat, no_hp wajib diisi | | Negatif — validasi required |
| MD-14 | Edit pengepul | Klik Edit, ubah alamat, klik Simpan Perubahan | alamat: `Jl. Baru No.1` | Data pengepul terupdate | | Positif |
| MD-15 | Hapus pengepul tanpa riwayat penjualan | Klik Hapus pada pengepul yang belum pernah jual | — | Data pengepul terhapus | | Positif |
| MD-16 | Hapus pengepul yang memiliki riwayat penjualan | Klik Hapus pada pengepul yang punya penjualan | — | Error: "Pengepul tidak dapat dihapus karena masih memiliki riwayat penjualan." | | Negatif — integritas data |
| MD-17 | Pencarian pengepul | Ketik nama pengepul di field pencarian | pengepul: `PT Sampah` | Tabel menampilkan pengepul yang cocok | | Positif |

### 2.3 Kategori Sampah & Jenis Sampah (Sampah)

| ID | Nama Fitur | Langkah Pengujian | Input | Hasil yang Diharapkan | Hasil | Keterangan |
|----|-----------|-------------------|-------|----------------------|-------|------------|
| MD-18 | Lihat daftar jenis sampah | Login admin, buka halaman sampah | — | Tabel jenis sampah ditampilkan dengan nama kategori | | Positif |
| MD-19 | Tambah jenis sampah valid | Klik Tambah, isi kategori, nama, harga per kg | kategori: `Kertas`, nama_jenis: `Kardus`, harga_per_kg: `2000` | Jenis sampah berhasil ditambahkan | | Positif |
| MD-20 | Tambah jenis sampah dengan field kosong | Klik Tambah, klik Simpan tanpa isi | Semua field kosong | Validasi error: kategori, nama_jenis, harga_per_kg wajib diisi | | Negatif — validasi required |
| MD-21 | Tambah jenis sampah dengan harga negatif | Isi harga_per_kg dengan angka negatif | harga_per_kg: `-500` | Validasi error: harga harus numeric/positif | | Boundary |
| MD-22 | Filter jenis sampah berdasarkan kategori | Pilih kategori di dropdown filter, klik Filter | nama_kategori: `Plastik` | Tabel hanya menampilkan jenis sampah dari kategori Plastik | | Positif |
| MD-23 | Edit jenis sampah | Klik Edit, ubah harga, klik Simpan Perubahan | harga_per_kg: `2500` | Harga jenis sampah terupdate | | Positif |
| MD-24 | Hapus jenis sampah dengan stok > 0 | Klik Hapus pada jenis sampah yang masih punya stok | — | Error: "Sampah tidak dapat dihapus karena masih memiliki stok." | | Negatif — validasi stok |
| MD-25 | Hapus jenis sampah dengan stok = 0 | Klik Hapus pada jenis sampah yang stoknya 0 | — | Jenis sampah berhasil dihapus | | Positif |

---

## Phase 3 — Transaksi

### 3.1 Setoran Sampah

| ID | Nama Fitur | Langkah Pengujian | Input | Hasil yang Diharapkan | Hasil | Keterangan |
|----|-----------|-------------------|-------|----------------------|-------|------------|
| TRX-01 | Tambah setoran dengan data valid | Klik Tambah Setoran, pilih nasabah, pilih kategori, pilih jenis sampah, masukkan berat, klik Simpan | nasabah_id: 1, sampah_id: [1], berat: [2] | Setoran tersimpan, kode_setoran otomatis (S0001), stok bertambah, saldo rupiah nasabah bertambah | | Positif |
| TRX-02 | Tambah setoran dengan berat < 0.1 kg | Isi berat kurang dari 0.1 | berat: [0.05] | Validasi error: berat minimal 0.1 | | Boundary |
| TRX-03 | Tambah setoran tanpa memilih nasabah | Klik Simpan tanpa pilih nasabah | nasabah_id: kosong | Validasi error: nasabah wajib dipilih | | Negatif |
| TRX-04 | Tambah setoran tanpa memilih sampah | Pilih nasabah, tapi tidak pilih jenis sampah | sampah_id: kosong | Validasi error: sampah wajib dipilih | | Negatif |
| TRX-05 | Tambah setoran multiple items | Tambah beberapa baris sampah sekaligus | 3 jenis sampah dengan berat berbeda | Semua item tersimpan, total_harga akurat, stok tiap jenis bertambah | | Positif |
| TRX-06 | Cek kenaikan stok setelah setoran | Periksa stok jenis sampah sebelum dan sesudah setoran | — | Stok jenis sampah = stok awal + berat setoran | | Positif — integritas data |
| TRX-07 | Cek kenaikan saldo setelah setoran | Periksa saldo rupiah nasabah sebelum dan sesudah setoran | — | Saldo rupiah = saldo awal + total_harga setoran | | Positif — integritas data |
| TRX-08 | Filter setoran berdasarkan nama nasabah | Ketik nama nasabah di field filter | nasabah: `Budi` | Tabel hanya menampilkan setoran milik nasabah "Budi" | | Positif |
| TRX-09 | Filter setoran berdasarkan tanggal | Pilih tanggal awal dan akhir | tanggal_awal: `01-01-2026`, tanggal_akhir: `31-12-2026` | Tabel hanya menampilkan setoran pada rentang tanggal tersebut | | Positif |
| TRX-10 | Filter dengan tanggal awal saja (tanpa akhir) | Isi tanggal_awal, biarkan tanggal_akhir kosong | tanggal_awal: `01-01-2026` | Tabel menampilkan semua data (filter tidak aktif) | | Boundary |

### 3.2 Detail Setoran

| ID | Nama Fitur | Langkah Pengujian | Input | Hasil yang Diharapkan | Hasil | Keterangan |
|----|-----------|-------------------|-------|----------------------|-------|------------|
| TRX-11 | Detail setoran terbentuk otomatis | Setelah setoran berhasil, periksa tabel setoran_details | — | Setiap item sampah memiliki record detail dengan berat, harga_per_kg, subtotal yang benar | | Positif |
| TRX-12 | Kode setoran otomatis | Buat setoran baru, periksa kode_setoran | — | Kode setoran berformat S + 4 digit (S0001, S0002, dst) | | Positif |

### 3.3 Penjualan Sampah

| ID | Nama Fitur | Langkah Pengujian | Input | Hasil yang Diharapkan | Hasil | Keterangan |
|----|-----------|-------------------|-------|----------------------|-------|------------|
| TRX-13 | Tambah penjualan dengan data valid | Klik Tambah Penjualan, pilih pengepul, pilih jenis sampah, masukkan berat, klik Simpan | pengepul_id: 1, sampah_id: [1], berat: [5] | Penjualan tersimpan, stok jenis sampah berkurang | | Positif |
| TRX-14 | Tambah penjualan dengan berat melebihi stok | Isi berat lebih besar dari stok tersedia | berat: [99999] | Error: "Berat penjualan melebihi stok tersedia." | | Negatif — validasi stok |
| TRX-15 | Tambah penjualan tanpa memilih pengepul | Klik Simpan tanpa pilih pengepul | pengepul_id: kosong | Validasi error: pengepul wajib dipilih | | Negatif |
| TRX-16 | Cek pengurangan stok setelah penjualan | Periksa stok sebelum dan sesudah penjualan | — | Stok = stok awal - berat penjualan | | Positif — integritas data |
| TRX-17 | Kode penjualan otomatis | Buat penjualan baru, periksa kode_penjualan | — | Kode penjualan berformat P + 4 digit (P0001, P0002, dst) | | Positif |

### 3.4 Batalkan Transaksi (Void)

| ID | Nama Fitur | Langkah Pengujian | Input | Hasil yang Diharapkan | Hasil | Keterangan |
|----|-----------|-------------------|-------|----------------------|-------|------------|
| TRX-18 | Batalkan setoran berhasil | Klik tombol void pada setoran berhasil, isi alasan, konfirmasi SweetAlert2 | alasan_batal: `Salah input` | Status setoran berubah "dibatalkan", stok dikembalikan, saldo dikurangi | | Positif |
| TRX-19 | Batalkan setoran tanpa alasan | Klik void, biarkan alasan kosong, klik Ya Batalkan | alasan_batal: kosong | Validasi error: "Alasan pembatalan wajib diisi" | | Negatif |
| TRX-20 | Batalkan setoran yang sudah dibatalkan | Klik void pada setoran yang sudah "dibatalkan" | — | Error: "Transaksi ini sudah dibatalkan sebelumnya." | | Boundary |
| TRX-21 | Cek reversal stok saat batal setoran | Periksa stok setelah pembatalan | — | Stok = stok saat ini - berat setoran (kembali ke sebelum setoran) | | Positif — reversal |
| TRX-22 | Cek reversal saldo saat batal setoran | Periksa saldo rupiah nasabah setelah pembatalan | — | Saldo = saldo saat ini - total_harga setoran | | Positif — reversal |
| TRX-23 | Batalkan penjualan | Klik void pada penjualan berhasil, isi alasan, konfirmasi | alasan_batal: `Stok salah` | Status penjualan "dibatalkan", stok dikembalikan | | Positif |
| TRX-24 | Batalkan penjualan yang sudah dibatalkan | Klik void pada penjualan yang sudah "dibatalkan" | — | Error: "Transaksi ini sudah dibatalkan sebelumnya." | | Boundary |

### 3.5 Konversi Emas

| ID | Nama Fitur | Langkah Pengujian | Input | Hasil yang Diharapkan | Hasil | Keterangan |
|----|-----------|-------------------|-------|----------------------|-------|------------|
| TRX-25 | Toggle Master Switch auto-convert ON | Klik toggle switch di halaman Riwayat Konversi Emas | — | Status berubah ON, pesan sukses "Master Switch auto-convert: ON" | | Positif |
| TRX-26 | Toggle Master Switch auto-convert OFF | Klik toggle switch saat status ON | — | Status berubah OFF, pesan sukses "Master Switch auto-convert: OFF" | | Positif |

---

## Phase 4 — Business Rules

| ID | Nama Fitur | Langkah Pengujian | Input | Hasil yang Diharapkan | Hasil | Keterangan |
|----|-----------|-------------------|-------|----------------------|-------|------------|
| BR-01 | Setoran tidak dapat dibatalkan jika saldo sudah dikonversi emas | Coba batalkan setoran pada nasabah yang sudah mengkonversi saldo ke emas | — | Error: "Saldo rupiah nasabah tidak mencukupi untuk reversal." | | Aturan bisnis |
| BR-02 | Setoran tidak dapat dibatalkan jika stok sudah dijual | Coba batalkan setoran di mana stok sampah terkait sudah dijual ke pengepul | — | Error: "Stok sampah X tidak mencukupi untuk reversal." | | Aturan bisnis |
| BR-03 | Validasi stok penjualan | Masukkan berat penjualan > stok tersedia | berat: (stok+1) | Error: "Stok sampah X tidak mencukupi." | | Aturan bisnis |
| BR-04 | Validasi saldo saat batal setoran | Batalkan setoran nasabah yang saldo rupiahnya sudah tidak cukup | — | Error: "Saldo rupiah nasabah tidak mencukupi untuk reversal." | | Aturan bisnis |
| BR-05 | Nasabah tidak bisa dihapus jika punya setoran | Hapus nasabah yang sudah pernah setor | — | Error: "Nasabah tidak dapat dihapus karena masih memiliki riwayat setoran." | | Integritas data |
| BR-06 | Pengepul tidak bisa dihapus jika punya penjualan | Hapus pengepul yang sudah pernah jual | — | Error: "Pengepul tidak dapat dihapus karena masih memiliki riwayat penjualan." | | Integritas data |
| BR-07 | Jenis sampah tidak bisa dihapus jika stok > 0 | Hapus jenis sampah yang masih punya stok | — | Error: "Sampah tidak dapat dihapus karena masih memiliki stok." | | Integritas data |
| BR-08 | Kode setoran unik dan auto-increment | Buat beberapa setoran berturut-turut | — | Kode setoran: S0001, S0002, S0003 (tidak duplikat) | | Aturan bisnis |
| BR-09 | Kode penjualan unik dan auto-increment | Buat beberapa penjualan berturut-turut | — | Kode penjualan: P0001, P0002, P0003 (tidak duplikat) | | Aturan bisnis |
| BR-10 | Subtotal setoran = harga_per_kg × berat | Buat setoran, periksa subtotal di detail | berat: 2, harga_per_kg: 5000 | subtotal = 10000 | | Kalkulasi |
| BR-11 | Total harga setoran = jumlah semua subtotal | Periksa total_harga di record setoran | 3 item dengan subtotal berbeda | total_harga = sum(subtotal semua item) | | Kalkulasi |
| BR-12 | Total harga penjualan = jumlah semua subtotal | Periksa total_harga di record penjualan | — | total_harga = sum(subtotal semua item) | | Kalkulasi |

---

## Phase 5 — Elasticsearch & Redis

| ID | Nama Fitur | Langkah Pengujian | Input | Hasil yang Diharapkan | Hasil | Keterangan |
|----|-----------|-------------------|-------|----------------------|-------|------------|
| ES-01 | Pencarian nasabah normal (Elasticsearch) | Akses `/search/nasabah?q=Budi` | q: `Budi` | Response JSON dengan data nasabah, header `X-Search-Time` tercatat | | Positif — ES active |
| ES-02 | Pencarian sampah normal | Akses `/search/sampah?q=Kardus` | q: `Kardus` | Response JSON dengan data jenis sampah | | Positif |
| ES-03 | Pencarian setoran normal | Akses `/search/setoran?q=S0001` | q: `S0001` | Response JSON dengan data setoran | | Positif |
| ES-04 | Pencarian dengan cache Redis (HIT) | Akses pencarian yang sama dua kali berturut-turut | q: `Budi` (request ke-2) | Header `X-Cache: HIT`, `X-Served-From: Redis` | | Positif — cache hit |
| ES-05 | Pencarian pertama kali (MISS) | Akses pencarian dengan keyword baru | q: `KeywordBaru123` | Header `X-Cache: MISS`, `X-Served-From: Elasticsearch` atau `Mysql` | | Positif — cache miss |
| ES-06 | Cache expiration | Akses pencarian, tunggu > 300 detik, akses lagi | — | Request ke-2: `X-Cache: MISS` (cache expired) | | Boundary — TTL 300s |
| ES-07 | Elasticsearch dimatikan → fallback MySQL | Matikan Elasticsearch, lakukan pencarian | — | Pencarian tetap berhasil, `X-Served-From: Mysql`, log warning tercatat | | Fallback |
| ES-08 | Pencarian dengan keyword kosong | Akses `/search/nasabah?q=` | q: `(kosong)` | Response: `{"data": [], "total": 0}` | | Boundary |
| ES-09 | Pencarian dengan keyword tidak ditemukan | Akses `/search/nasabah?q=XYZNOTEXIST` | q: `XYZNOTEXIST` | Response: `{"data": [], "total": 0}` | | Negatif |
| ES-10 | Header debug mode | Pastikan `APP_DEBUG=true`, lakukan pencarian | — | Header `X-Cache`, `X-Served-From`, `X-Search-Time` tercatat di response | | Positif |

---

## Phase 6 — Dashboard & Reporting

### 6.1 Dashboard Statistik

| ID | Nama Fitur | Langkah Pengujian | Input | Hasil yang Diharapkan | Hasil | Keterangan |
|----|-----------|-------------------|-------|----------------------|-------|------------|
| RPT-01 | Dashboard admin menampilkan card statistik | Login admin, buka `/admin/dashboard` | — | Card: Total Nasabah, Total Pengepul, Total Jenis Sampah, Total Saldo ditampilkan | | Positif |
| RPT-02 | Dashboard admin menampilkan grafik | Login admin, buka `/admin/dashboard` | — | Grafik: Tren Harga Emas, Setoran per Bulan (bar), Komposisi Kategori (donut), Nasabah Baru, Konversi Saldo ditampilkan | | Positif |
| RPT-03 | Dashboard admin menampilkan tabel nasabah terbaru | Login admin, buka `/admin/dashboard` | — | Tabel nasabah terbaru dengan 5 data teratas ditampilkan | | Positif |
| RPT-04 | Dashboard nasabah | Login nasabah, buka `/nasabah/dashboard` | — | Card Saldo Rupiah, Saldo Emas, Harga Emas Terkini, grafik Setoran dan Konversi ditampilkan | | Positif |

### 6.2 Cache Dashboard

| ID | Nama Fitur | Langkah Pengujian | Input | Hasil yang Diharapkan | Hasil | Keterangan |
|----|-----------|-------------------|-------|----------------------|-------|------------|
| RPT-05 | Dashboard menggunakan cache Redis | Login admin, buka dashboard, periksa Redis keys | — | Keys `dashboard:*` tercipta di Redis | | Positif |
| RPT-06 | Dashboard load dari cache pada request ke-2 | Buka dashboard dua kali berturut-turut | — | Request ke-2 lebih cepat (data dari cache) | | Positif |

### 6.3 Cetak Laporan & Filter

| ID | Nama Fitur | Langkah Pengujian | Input | Hasil yang Diharapkan | Hasil | Keterangan |
|----|-----------|-------------------|-------|----------------------|-------|------------|
| RPT-07 | Cetak laporan setoran (PDF) | Login admin, buka halaman setoran, atur filter, klik Cetak Laporan | tanggal_awal: `01-01-2026`, tanggal_akhir: `31-12-2026` | PDF laporan setoran ditampilkan di tab baru | | Positif |
| RPT-08 | Cetak laporan penjualan (PDF) | Login admin, buka halaman penjualan, atur filter, klik Cetak Laporan | tanggal_awal: `01-01-2026`, tanggal_akhir: `31-12-2026` | PDF laporan penjualan ditampilkan di tab baru | | Positif |
| RPT-09 | Cetak laporan riwayat konversi emas (PDF) | Login admin, buka halaman riwayat konversi, klik Cetak Laporan | — | PDF laporan konversi emas ditampilkan di tab baru (landscape) | | Positif |
| RPT-10 | Cetak laporan tanpa filter (data kosong) | Klik Cetak Laporan tanpa atur filter, jika tidak ada data | — | PDF kosong atau menampilkan semua data (tergantung implementasi) | | Boundary |
| RPT-11 | Filter laporan berdasarkan tanggal | Atur tanggal awal dan akhir, klik Filter | tanggal_awal: `01-06-2026`, tanggal_akhir: `30-06-2026` | Tabel hanya menampilkan data pada bulan Juni 2026 | | Positif |
| RPT-12 | Filter laporan berdasarkan nasabah/pengepul | Pilih nasabah/pengepul di dropdown, klik Filter | nasabah_id: 1 | Tabel hanya menampilkan data untuk nasabah tersebut | | Positif |
| RPT-13 | Reset filter | Klik tombol Reset setelah filter aktif | — | Semua filter terhapus, tabel menampilkan semua data | | Positif |

---

## Ringkasan Test Case

| Phase | Jumlah Test Case | Positif | Negatif | Boundary |
|-------|-----------------|---------|---------|----------|
| Phase 1 — Authentication | 27 | 10 | 14 | 3 |
| Phase 2 — Master Data | 25 | 15 | 7 | 3 |
| Phase 3 — Transaksi | 26 | 17 | 5 | 4 |
| Phase 4 — Business Rules | 12 | 6 | 6 | 0 |
| Phase 5 — Elasticsearch & Redis | 10 | 6 | 1 | 3 |
| Phase 6 — Dashboard & Reporting | 13 | 11 | 0 | 2 |
| **Total** | **113** | **65** | **33** | **15** |
