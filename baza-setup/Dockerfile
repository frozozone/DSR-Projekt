#PHP + Apache slika, dodamo samo se gonilnik za PostgreSQL (pdo_pgsql)
FROM php:8.3-apache

#libpq-dev rabimo da se pdo_pgsql lahko prevede
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql
