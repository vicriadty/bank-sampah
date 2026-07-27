Buatkan branch fix/auth-standard dan implementasikan:

Hapus kolom email pada table Users
Karena kolom email sudah ada pada table Nasabah

Standar panjang karakter pada input

Modifikasi panjang length pada migration
tambahkan validasi minLength dan maxLength pada setiap input yang berhubungan dengan AUTHENTICATION  

1. FORM LOGIN

Username

- minLength: 5
- maxLength: 30

Password:

- minLength: 8
- maxLength: 100

2. FORM REGISTER

NIK:

- minLength: 16
- maxLength: 16

Nama:

- minLength: 3
- maxLength: 100

No_hp:

- minLength: 10
- maxLength: 13

Penetapan tipe data standar untuk Harga/Kg dan Berat:

Lakukan validasi ketat di sisi server sebelum data disimpan ke database. 
Contoh logika validasinya:Harga (Rupiah): Wajib angka, harus bilangan bulat (integer), dan minimal bernilai 1.
Berat: Wajib angka, boleh desimal, dan minimal bernilai 0.1.

Modifikasi Tipe Data
- Harga menggunakan 
