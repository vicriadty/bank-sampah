@echo off
cd /d "%~dp0"
echo [1/5] Memeriksa Folder...
if not exist "www" mkdir www

echo [2/5] Memeriksa Laravel di folder www...
:: Perbaikan: Menggunakan path yang benar untuk cek file artisan
if not exist "www\artisan" (
    echo Mengunduh Laravel...
    :: Pastikan composer sudah ada di PATH Windows Anda
    call composer create-project laravel/laravel www
)

echo [3/5] Memastikan file .env Docker ada...
:: Pastikan file .env (untuk port & db) ada di root folder, bukan cuma di dalam www
if not exist ".env" (
    echo Error: File .env utama tidak ditemukan! 
    echo Pastikan file .env untuk konfigurasi Docker sudah dibuat.
    pause
    exit
)

echo [4/5] Menjalankan Docker Containers...
:: Perbaikan: Menggunakan call untuk memastikan proses tidak berhenti di tengah
call docker-compose up -d --build

echo [5/5] Finalisasi di dalam Container...
:: Memberi jeda 5 detik agar MySQL siap
timeout /t 5

:: Menjalankan perintah Laravel di dalam container
docker exec laravel_app composer install
docker exec laravel_app php artisan key:generate
docker exec laravel_app php artisan migrate --force

echo ---------------------------------------------------
echo Setup Selesai! 
echo Cek Dashboard Docker Desktop untuk container: laravel_app & laravel_db
echo ---------------------------------------------------
pause