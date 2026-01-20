# Sử dụng image PHP kèm Apache
FROM php:8.0-apache

# Cài đặt extension mysqli để kết nối database
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Copy toàn bộ source code vào thư mục web root của container
COPY . /var/www/html/

# Cấp quyền cho thư mục (để chức năng upload file hoạt động được)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html