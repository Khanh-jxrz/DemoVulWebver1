FROM php:8.0-apache

RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

COPY src/ /var/www/html/
COPY flag.txt /flag/flag.txt
COPY flag_ssrf.txt /opt/flag_ssrf.txt

RUN echo 'Alias /flag.txt /opt/flag_ssrf.txt' >> /etc/apache2/apache2.conf \
 && echo '<Directory /opt>' >> /etc/apache2/apache2.conf \
 && echo '    Require all granted' >> /etc/apache2/apache2.conf \
 && echo '</Directory>' >> /etc/apache2/apache2.conf

RUN mkdir -p /var/www/html/uploads \
 && chown -R www-data:www-data /var/www/html /opt \
 && chmod -R 755 /var/www/html \
 && chmod 644 /opt/flag_ssrf.txt \
 && chmod -R 775 /var/www/html/uploads
