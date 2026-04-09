FROM php:8.4.1-fpm

ARG WWW_UID
ARG WWW_GID

# Устанавливаем системные зависимости и PHP-расширения
RUN apt update && apt install -y \
    gosu \
    mc nano git \
    libfreetype6-dev libjpeg62-turbo-dev libpng-dev libzip-dev libpq-dev \
    libexif-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_pgsql zip gd exif opcache \
    && pecl install redis \
    && docker-php-ext-enable redis exif

# Устанавливаем Node.js 20 и обновляем npm до последней версии.
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt install -y nodejs && \
    npm install -g npm@latest

# Приводим UID/GID встроенного пользователя www-data в соответствие с хостовым пользователем.
# Это устраняет проблемы с правами доступа к файлам на примонтированных томах (volumes).
RUN usermod -u $WWW_UID www-data && groupmod -g $WWW_GID www-data

COPY entrypoint.sh /usr/local/bin/entrypoint.sh

COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

COPY uploads.ini /usr/local/etc/php/conf.d/uploads.ini

RUN apt-get -y install supervisor
COPY supervisor/supervisord.conf /etc/supervisord.conf
COPY supervisor/xlaravel.pool.conf /usr/local/etc/php-fpm.d/xlaravel.pool.conf

# Supervisor logs
RUN mkdir -p /var/log/worker \
    && chown -R ${WWW_UID}:${WWW_GID} /var/log/worker \
    && chmod -R 775 /var/log/worker

WORKDIR /srv/api

RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]

CMD ["php-fpm"]

EXPOSE 9000
