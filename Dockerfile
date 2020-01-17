FROM php:7.3

RUN apt-get update && apt-get install -y \
    zip \
    unzip

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
