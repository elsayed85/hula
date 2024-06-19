FROM alpine:3.18

# Install system dependencies
RUN apk add --no-cache \
    curl \
    git \
    nginx \
    npm \
    php82 \
    php82-ctype \
    php82-curl \
    php82-dom \
    php82-fpm \
    php82-gd \
    php82-intl \
    php82-mbstring \
    php82-mysqli \
    php82-opcache \
    php82-openssl \
    php82-phar \
    php82-session \
    php82-xml \
    php82-xmlreader \
    php82-simplexml \
    php82-zlib \
    php82-fileinfo \
    php82-sodium \
    php82-tokenizer \
    php82-exif \
    php82-xmlwriter \
    php82-pdo \
    php82-pdo_mysql \
    php82-redis \
    php82-pear \
    php82-dev \
    php82-zip \
    php82-sockets \
    php82-mongodb \
    php82-iconv \
    supervisor \
    busybox-extras

RUN ln -s /usr/bin/php82 /usr/bin/php

# Clear cache
RUN rm -rf /var/lib/apt/lists/* \
    && rm -rf /tmp/*

RUN adduser -u 1001 -D -h /home/hula hula

RUN mkdir /var/www/html \
    && mkdir -p /home/hula/.composer \
    && chown -R hula:hula /home/hula

RUN chown -R hula:hula /var/lib/nginx \
    && chmod -R 777 /var/lib/nginx

RUN chown -R hula:hula /run \
    && chmod -R 777 /run /var/www/html

RUN touch /var/run/nginx.pid && touch /run/nginx/nginx.pid && \
    chown -R nginx:nginx /var/run/nginx.pid /run/nginx.pid /run/nginx/nginx.pid

RUN mkdir /var/log/supervisor \
    && touch /var/log/supervisor/worker.log

RUN chown -R hula:hula /run \
    && chmod -R 777 /run /var/www/html /var/log

COPY docker-config/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker-config/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker-config/php/clear-env.conf /etc/php82/php-fpm.d/clear-env.conf

# Install composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

USER nobody

COPY --chown=nobody . /var/www/html/
COPY .env.example /var/www/html/.env

USER root

RUN mkdir /var/www/html/storage/framework/sessions \
    && chmod -R 777 /var/www/html/storage

RUN composer install --ignore-platform-reqs

CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

USER hula

EXPOSE 5555
