# --- Tahap 1: Install dependensi PHP dengan Composer ---
# Menggunakan image dasar PHP 8.1 dengan FPM dan Alpine (untuk ukuran kecil)
FROM php:8.1-fpm-alpine AS vendor

# Install ekstensi PHP yang umum dibutuhkan Laravel
RUN docker-php-ext-install pdo pdo_mysql

# Set direktori kerja
WORKDIR /app

# Copy hanya file yang dibutuhkan untuk composer, agar cache lebih efisien
COPY database/ database/
COPY composer.json composer.lock ./

# Install dependensi composer untuk produksi
RUN composer install --no-interaction --no-plugins --no-scripts --prefer-dist --optimize-autoloader


# --- Tahap 2: Build aset Node.js dengan NPM ---
FROM node:18-alpine AS assets

WORKDIR /app

# Copy file package.json untuk install dependensi
COPY package.json package-lock.json ./

# Install dependensi NPM
RUN npm install

# Copy semua file proyek untuk proses build
COPY . .

# Build aset untuk produksi (ini yang akan membuat file CSS Anda)
RUN npm run build


# --- Tahap 3: Final Image yang akan dijalankan ---
FROM php:8.1-fpm-alpine

WORKDIR /var/www/html

# Install ekstensi PHP yang dibutuhkan untuk runtime
RUN docker-php-ext-install pdo pdo_mysql

# Copy semua file aplikasi dari direktori lokal
COPY . .

# Copy dependensi vendor dari tahap 'vendor'
COPY --from=vendor /app/vendor/ ./vendor/

# Copy aset yang sudah di-build dari tahap 'assets'
COPY --from=assets /app/public/build/ ./public/build/

# Atur kepemilikan file agar server web bisa menulis ke storage & cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port yang akan digunakan
EXPOSE 8000