FROM php:8.0-apache

# Cài mysqli
RUN docker-php-ext-install mysqli

# Copy source web (KHÔNG copy database.sql)
COPY src/ /var/www/html/

# Cấp quyền
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/uploads
