FROM php:8.2-apache

# Instalujemy zależności i rozszerzenia PDO PostgreSQL
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Kopiujemy pliki projektu do katalogu serwera
COPY . /var/www/html/

# Ustawiamy uprawnienia
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80
