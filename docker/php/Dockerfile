FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql pgsql zip

COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

ARG USER_ID=1000
ARG GROUP_ID=1000

RUN usermod -u ${USER_ID} www-data && \
    groupmod -g ${GROUP_ID} www-data

WORKDIR /var/www/html

COPY ./entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

USER www-data