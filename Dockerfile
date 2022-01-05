FROM phpswoole/swoole:4.8-php8.0-alpine

RUN apk --update add --no-cache \
    ${PHPIZE_DEPS} \
    bash

RUN docker-php-ext-install \
    pcntl \
    pdo \
    pdo_mysql \
    opcache

RUN docker-php-ext-enable \
    pcntl \
    pdo_mysql \
    opcache

RUN echo "swoole.use_shortname=off" >> /usr/local/etc/php/conf.d/docker-php-ext-swoole.ini \
    && echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.enable_cli=1" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.memory_consumption=256" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.max_accelerated_files=5000" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.interned_strings_buffer=12" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.use_cwd=0" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

RUN docker-php-source delete \
    && rm -rf /var/lib/apt/lists/* \
    && rm -rf /tmp/*

WORKDIR /app

COPY ./composer.* /app
COPY . /app

RUN composer install --prefer-dist --no-dev --optimize-autoloader

EXPOSE 9501

ENTRYPOINT [ "php", "/app/bin/hyperf.php" ]

CMD [ "start" ]