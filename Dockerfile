# Dùng PHP 8.2 với Apache
FROM php:8.2-apache

# Cài các gói cần thiết cho PostgreSQL
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Copy toàn bộ code vào thư mục web server
COPY . /var/www/html/

# Mở port 80
EXPOSE 80