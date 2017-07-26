FROM php:apache
COPY . /var/www/html

RUN apt-get update && apt-get install -y \
        libjpeg62-turbo-dev \
        libpng12-dev \
    && docker-php-ext-install mysqli \
    && docker-php-ext-install -j$(nproc) gd \
    && a2enmod rewrite && service apache2 restart
