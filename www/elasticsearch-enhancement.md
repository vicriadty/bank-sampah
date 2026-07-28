Saya menyarankan agar AI Agent **tidak langsung mengubah query Elasticsearch**, tetapi melakukannya secara bertahap. Dengan begitu setiap perubahan dapat diuji, dibandingkan hasilnya, dan jika terjadi penurunan relevansi pencarian akan lebih mudah diidentifikasi.

Berikut adalah high level plan yang saya rekomendasikan.

Buatkan branch fix/elasticsearch-improve

---

# Prompt untuk AI Agent

## Objective

Lakukan peningkatan kualitas fitur pencarian pada aplikasi Bank Sampah berbasis Laravel 12 yang menggunakan MySQL sebagai primary database, Elasticsearch sebagai search engine, dan Redis sebagai cache.

Tujuan implementasi adalah meningkatkan **akurasi hasil pencarian**, **relevansi ranking**, serta **kualitas response API**, tanpa mengubah arsitektur yang sudah berjalan.

Implementasi dilakukan secara bertahap (phase-based), dengan setiap phase dapat diuji secara independen.

---

# Phase 1 – Audit dan Analisis Search

### Tujuan

Lakukan audit terhadap implementasi pencarian Elasticsearch saat ini.

### Task

- Analisis seluruh Search Repository:
    - NasabahSearchRepository
    - JenisSampahSearchRepository
    - SetoranSearchRepository

- Identifikasi:
    - field yang menggunakan ngram
    - jenis query yang digunakan
    - analyzer yang dipakai
    - ranking (\_score)

- Dokumentasikan kelemahan implementasi saat ini, misalnya:
    - hasil terlalu banyak
    - false positive
    - ranking kurang relevan
    - pencarian nama menghasilkan data yang tidak sesuai.

### Deliverable

Dokumen audit implementasi search.

---

# Phase 2 – Perbaikan Query Elasticsearch

### Tujuan

Meningkatkan relevansi hasil pencarian.

### Task

Ganti query sederhana:

```php
multi_match
```

menjadi query bertingkat menggunakan:

- bool query
- should clause
- match_phrase
- prefix
- multi_match sebagai fallback
- minimum_should_match yang sesuai

Prioritas pencarian:

1. Exact phrase
2. Prefix
3. Partial search (ngram)

Field dapat memiliki bobot (boost) yang berbeda.

Contoh prioritas:

- nama lebih tinggi daripada alamat
- nik lebih tinggi daripada alamat
- email lebih rendah jika tidak sering digunakan

Pastikan hasil pencarian tetap kompatibel dengan pagination Laravel.

### Deliverable

Search Repository yang lebih relevan dan mudah dikembangkan.

---

# Phase 3 – Optimasi Mapping dan Analyzer

### Tujuan

Mengurangi false positive.

### Task

Evaluasi seluruh mapping Elasticsearch.

Pastikan:

- field yang memang perlu ngram tetap menggunakan ngram
- field yang tidak membutuhkan partial matching menggunakan keyword atau analyzer standar
- evaluasi penggunaan ngram pada:
    - nama
    - alamat
    - email
    - nik

Buat rekomendasi mapping terbaik sesuai kebutuhan aplikasi Bank Sampah.

Jika diperlukan, lakukan recreate index dan reimport data.

### Deliverable

Mapping Elasticsearch yang lebih optimal.

---

# Phase 4 – Optimasi Ranking

### Tujuan

Meningkatkan kualitas urutan hasil pencarian.

### Task

Gunakan boosting pada field.

Contoh prioritas:

- nama
- nik
- email
- alamat

Pastikan pencarian:

```
Raf
```

lebih memprioritaskan

```
Raffa
Rafli
Rafif
```

dibanding

```
Safitri
Ifa
```

Tambahkan minimum_score apabila diperlukan untuk menghilangkan hasil dengan relevansi yang sangat rendah.

### Deliverable

Ranking hasil pencarian lebih akurat.

---

# Phase 5 – Perbaikan Response API

### Tujuan

Meningkatkan kualitas response.

### Task

Perbaiki response JSON.

Pastikan collection selalu di-reset menggunakan:

```php
->values()
```

sehingga response menjadi:

```json
[
  {...},
  {...},
  {...}
]
```

bukan

```json
{
  "0": {...},
  "1": {...},
  "3": {...}
}
```

Tambahkan metadata response jika belum tersedia:

- search_engine
- cache_status
- search_time
- total
- current_page
- last_page

Pastikan struktur response konsisten.

### Deliverable

API response lebih bersih dan mudah digunakan frontend.

---

# Phase 6 – Search Quality Testing

### Tujuan

Memastikan seluruh perubahan meningkatkan kualitas pencarian.

### Task

Buat skenario pengujian untuk:

### Exact Match

```
Raffa
```

harus menghasilkan

```
Raffa
```

di urutan pertama.

---

### Prefix Search

```
Raf
```

menghasilkan

```
Raffa
Rafli
Rafif
```

---

### Partial Search

```
ffa
```

tetap menemukan

```
Raffa
```

---

### NIK Search

```
320102
```

menghasilkan NIK yang sesuai.

---

### Email Search

```
gmail
```

menghasilkan email yang relevan.

---

### Address Search

Pastikan pencarian alamat tidak menghasilkan terlalu banyak false positive.

---

### Redis

Lakukan dua kali request.

Request pertama:

```
X-Cache: MISS
```

Request kedua:

```
X-Cache: HIT
```

---

### Elasticsearch Fallback

Matikan Elasticsearch.

Pastikan pencarian tetap berhasil menggunakan MySQL.

---

### Performance Comparison

Bandingkan:

- waktu sebelum optimasi
- waktu sesudah optimasi

Gunakan data:

- search time
- jumlah dokumen
- relevansi hasil

### Deliverable

Dokumen hasil pengujian beserta perbandingan sebelum dan sesudah optimasi.

---

# Target Akhir

Setelah seluruh phase selesai, implementasi pencarian harus memenuhi karakteristik berikut:

- Akurasi pencarian meningkat dengan mengurangi false positive.
- Hasil pencarian diurutkan berdasarkan tingkat relevansi yang lebih baik.
- Struktur response API konsisten dan bersih untuk frontend.
- Integrasi Elasticsearch, Redis, dan MySQL fallback tetap berjalan tanpa perubahan arsitektur.
- Performa pencarian tetap cepat, dengan request pertama melalui Elasticsearch dan request berikutnya memanfaatkan cache Redis.
- Seluruh perubahan terdokumentasi dan dapat dijadikan bagian dari dokumentasi teknis maupun bahan demonstrasi saat sidang skripsi.
