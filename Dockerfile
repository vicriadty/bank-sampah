FROM php:8.4-apache

# 1. Install system dependencies untuk PHP extensions & Composer
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev

# 2. Clear cache apt untuk memperkecil ukuran image
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# 3. Install PHP extensions yang dibutuhkan Laravel
RUN docker-php-ext-install pdo_mysql mysqli mbstring exif pcntl bcmath gd zip

# 4. Install Redis extension via PECL (dibutuhkan untuk session/cache/queue)
RUN pecl install redis && docker-php-ext-enable redis

# 5. Install OPcache untuk mempercepat eksekusi PHP
RUN docker-php-ext-install opcache

COPY opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# 6. Ambil Composer versi terbaru langsung dari official image-nya
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. Konfigurasi Apache Document Root ke folder public Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 7. Aktifkan mod_rewrite Apache (wajib untuk routing Laravel)
RUN a2enmod rewrite