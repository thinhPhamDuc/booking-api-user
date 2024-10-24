# Use a Debian-based image
FROM php:8.2-fpm AS local

ARG USER_ID=1000
ARG GROUP_ID=1000

# Install required packages and libraries
RUN apt-get update && apt-get install -y \
    curl \
    build-essential \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libxml2-dev \
    g++ \
    make \
    autoconf \
    openssl \
    git \
    bash \
    libzip-dev \
    zip \
    unzip \
    imagemagick libmagickwand-dev --no-install-recommends \
    && pecl install imagick \
    && docker-php-ext-enable imagick

RUN docker-php-ext-install \
    pdo_mysql \
    opcache \
    gd \
    zip \
  && docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg \
  && docker-php-ext-install -j$(nproc) gd

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
  && chmod +x /usr/local/bin/composer

# RUN pecl install grpc && docker-php-ext-enable grpc

RUN curl -sL https://deb.nodesource.com/setup_18.x | bash -- &&\
    apt-get install -y nodejs

RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# RUN apt-get update && apt-get install -y cron

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN rm -rf /var/cache/apk/*

RUN groupadd -g $GROUP_ID myuser && useradd -u $USER_ID -g $GROUP_ID -m myuser

RUN sed -i 's/user = www-data/user = myuser/g' /usr/local/etc/php-fpm.d/www.conf
RUN sed -i 's/group = www-data/group = myuser/g' /usr/local/etc/php-fpm.d/www.conf

WORKDIR /var/www
RUN mkdir -p /etc/crontabs
RUN chmod 755 /etc/crontabs

RUN echo '* * * * * cd /var/www && php artisan schedule:run >> /dev/null 2>&1' >> /etc/crontabs/root

# WORKDIR /usr/share/nginx/html

FROM local AS remote

# COPY ./html /var/www
# COPY ./docker/php/php.ini /usr/local/etc/php/php.ini

# ## composer install
# ENV COMPOSER_ALLOW_SUPERUSER 1
# WORKDIR /var/www
# RUN composer install && \
#   php artisan storage:link

# ## Laravel
# RUN chmod -R 777 storage bootstrap/cache

USER myuser

VOLUME /var/www/public
