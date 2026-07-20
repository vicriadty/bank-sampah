# Performance Optimization Plan

## Diagnosa

Setelah aplikasi di-Dockerize, terdapat **5 faktor utama** yang menyebabkan performa menjadi lambat dan berat, bukan hanya karena Docker Desktop.

### Ringkasan Masalah

| # | Faktor | Dampak | Bobot |
|---|--------|--------|-------|
| 1 | `APP_DEBUG=true` + `LOG_LEVEL=debug` + write log ke Windows bound volume | Setiap request nulis log ke filesystem Windows via WSL2 (10-100x lebih lambat) | 🔴 KRITIS |
| 2 | Apache `mod_php` tanpa OPcache | Load & compile ulang ~200+ file framework tiap request | 🔴 KRITIS |
| 3 | Bound volume `./www:/var/www/html` | Filesystem I/O melalui 3 lapis abstraksi (NTFS → WSL2 9p → Docker overlay) | 🔴 KRITIS |
| 4 | Elasticsearch 950MB + MySQL 404MB = 1.35GB | Memory pressure, menyebabkan swapping/kompresi memori | 🟡 BERAT |
| 5 | `BCRYPT_ROUNDS=12` (default 10) | Login 4x lebih berat secara komputasi | 🟡 SEDANG |

---

## Prioritas 1 — Non-Fungsional (Efek Paling Besar)

### 1.1 Matikan Debug Mode & Turunkan Log Level

**File:** `www/.env`

```diff
- APP_DEBUG=true
+ APP_DEBUG=false
- LOG_LEVEL=debug
+ LOG_LEVEL=notice
```

**Efek:** Menghentikan penulisan log verbose ke bound volume (Windows filesystem) di setiap request. Ini adalah penyebab terbesar kelambatan.

### 1.2 Alihkan Log Channel ke stderr (Hindari Write ke Windows Filesystem)

**File:** `www/.env`

```diff
- LOG_CHANNEL=stack
- LOG_STACK=single
+ LOG_CHANNEL=stderr
```

Atau jika tetap ingin menggunakan stack channel, ubah `config/logging.php`:

```diff
'stack' => [
    'driver' => 'stack',
-   'channels' => explode(',', env('LOG_STACK', 'single')),
+   'channels' => ['stderr'],
    'ignore_exceptions' => false,
],
```

**Efek:** Log dikirim ke Docker stderr (terbaca via `docker logs`), tidak ada operasi I/O file ke Windows filesystem. Delay write file yang tadinya 10-100ms per operasi menjadi 0.

### 1.3 Tambahkan OPcache di Dockerfile

**File:** `Dockerfile` — tambahkan setelah baris `docker-php-ext-install ...`:

```dockerfile
# Install OPcache
RUN docker-php-ext-install opcache

# Copy konfigurasi OPcache
COPY opcache.ini /usr/local/etc/php/conf.d/opcache.ini
```

**File baru:** `opcache.ini` (di root project, sejajar dengan Dockerfile):

```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=10000
opcache.revalidate_freq=2
opcache.fast_shutdown=1
```

**Efek:** PHP menyimpan opcode hasil compile di shared memory. Framework Laravel (~200+ file) tidak perlu di-compile ulang di setiap request. Percepatan bisa mencapai **2-5x** untuk PHP berat seperti Laravel.

---

## Prioritas 2 — Resource Optimization

### 2.1 Kurangi RAM Elasticsearch

**File:** `docker-compose.yml`

```diff
-      - "ES_JAVA_OPTS=-Xms512m -Xmx512m"
+      - "ES_JAVA_OPTS=-Xms256m -Xmx256m"
```

**Efek:** Hemat 256MB RAM. Untuk development dengan ukuran data kecil (9 jenis sampah, 147 setoran, 168 nasabah), 256MB sudah lebih dari cukup.

### 2.2 Turunkan Bcrypt Rounds ke Default

**File:** `www/.env`

```diff
- BCRYPT_ROUNDS=12
+ BCRYPT_ROUNDS=10
```

**Efek:** Login 4x lebih ringan (setara default Laravel). Resource CPU untuk hashing password berkurang signifikan.

---

## Prioritas 3 — Docker Infrastructure

### 3.1 Alokasi RAM Lebih ke WSL2 (Windows Only)

**File baru:** `%UserProfile%\.wslconfig`

```ini
[wsl2]
memory=8GB
processors=4
```

**Efek:** Memberi lebih banyak RAM ke Docker VM. Efektif jika laptop memiliki ≥16GB RAM.

> **Catatan:** Setelah membuat file ini, restart WSL2 dengan `wsl --shutdown` lalu `docker-desktop restart`.

### 3.2 Gunakan Named Volume untuk Storage Logs (Opsi Lanjutan)

Jika tetap ingin menyimpan log ke file, pisahkan `storage/logs` dari bound volume agar tidak lewat Windows filesystem:

```yaml
volumes:
  - ./www:/var/www/html
  - app_logs:/var/www/html/storage/logs
```

Ini perlu rebuild container. Alternatif lebih praktis: **Prioritas 1.2** (log ke stderr).

---

## Ringkasan Dampak & Risiko

| # | Perubahan | Dampak Perkiraan | Risiko | Keterangan |
|---|-----------|-----------------|--------|------------|
| 1.1 | `APP_DEBUG=false`, `LOG_LEVEL=notice` | 🔴🔴🔴 Sangat Besar | Rendah | Tidak ada efek samping, hanya matikan debug |
| 1.2 | `LOG_CHANNEL=stderr` | 🔴🔴🔴 Sangat Besar | Rendah | Log tetap terbaca via `docker logs` |
| 1.3 | Install OPcache + config | 🔴🔴🔴 Sangat Besar | Rendah | Standard best practice, perlu rebuild container |
| 2.1 | ES RAM 512MB → 256MB | 🟡 Sedang | Rendah | Data kecil, 256MB cukup |
| 2.2 | Bcrypt 12 → 10 | 🟡 Sedang | Rendah | Default framework |
| 3.1 | `.wslconfig` RAM | 🟡 Sedang | Sedang | Perlu restart WSL2, tergantung RAM laptop |
| 3.2 | Named volume logs | 🟢 Kecil | Rendah | Opsional, prioritas 1.2 sudah cukup |

---

## Cara Verifikasi Setelah Implementasi

```bash
# Cek OPcache aktif
docker exec laravel_app php -m | grep opcache

# Cek log level
docker exec laravel_app php -r "echo config('app.debug') ? 'DEBUG ON' : 'DEBUG OFF';"

# Cek memory usage
docker stats --no-stream

# Cek apakah log masih nulis file
docker exec laravel_app ls -la storage/logs/laravel.log

# Test response time
time curl -so /dev/null http://localhost:8000/admin/dashboard
```
