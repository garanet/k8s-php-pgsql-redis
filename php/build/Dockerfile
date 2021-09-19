# php+pgsql+redis project - DOCKER CUSTOM IMAGE
# @maintainer G.Gatto 2021 - www.garanet.net
# repo from https://github.com/garanet/k8s-php-pgsql-redis
# Tested on a MacOsx with Docker + Kuberneters (Docker-Desktop)
FROM php:8.0-fpm
# Update the distro repo
RUN apt-get update
# Install PDO and PGSQL Drivers
RUN apt-get install -y libpq-dev vim iputils-ping \
  && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
  && docker-php-ext-install pdo pdo_pgsql pgsql
# Install PHP REDIS via pecl
RUN pecl install redis && docker-php-ext-enable redis
# Copy the PHP files
WORKDIR /app
COPY *.php /app