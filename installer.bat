@echo off
cd /d "%~dp0"
echo [1/6] Memeriksa Folder...
if not exist "www" mkdir www

echo [2/6] Memeriksa Laravel di folder www...
if not exist "www\artisan" (
    echo Mengunduh Laravel...
    call composer create-project laravel/laravel www
)

echo [3/6] Memeriksa file .env...
if not exist ".env" (
    echo Error: File .env utama tidak ditemukan!
    echo Pastikan file .env untuk konfigurasi Docker sudah dibuat.
    pause
    exit
)

if not exist "www\.env" (
    echo Menyalin .env.docker ke www\.env...
    copy "www\.env.docker" "www\.env"
)

echo [4/6] Menjalankan Docker Containers...
call docker-compose up -d --build

echo [5/6] Menunggu MySQL siap...
timeout /t 10

echo [6/6] Finalisasi di dalam Container...
docker exec laravel_app composer install
docker exec laravel_app php artisan key:generate
docker exec laravel_app php artisan migrate --force
docker exec laravel_app php artisan elastic:index:create

echo ---------------------------------------------------
echo Setup Selesai!
echo Container: laravel_app, laravel_db, redis, elasticsearch
echo App URL: http://localhost:8000
echo ---------------------------------------------------
pause
