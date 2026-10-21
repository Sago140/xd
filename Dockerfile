FROM php:8.2-apache

# Kopiujemy pliki projektu do katalogu serwera
COPY . /var/www/html/

# Włączamy uprawnienia do zapisu (żeby skrypt mógł modyfikować pliki json/txt)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80