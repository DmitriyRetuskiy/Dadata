FROM php:8.1-fpm

RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim \
    optipng \
    pngquant \
    gifsicle \
    vim \
    libzip-dev \
    unzip \
    git \
    libonig-dev \
    curl \
    nginx \
    supervisor \
    wget \
    imagemagick

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl

RUN apt-get update && apt-get install -y \
    libmagickwand-dev --no-install-recommends \
    && pecl install imagick \
    && docker-php-ext-enable imagick

WORKDIR /var/www/html

COPY composer.lock composer.json ./

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY . .

RUN cp deploy/docker/supervisor.conf /etc/supervisord.conf
RUN cp deploy/docker/nginx.conf /etc/nginx/sites-enabled/default
RUN cp deploy/docker/config/php.ini /usr/local/etc/php/conf.d/app.ini

RUN chown -R www-data:www-data storage

RUN composer install

RUN ln -s /var/www/html/storage/app/public /var/www/html/public/storage

EXPOSE 80

RUN sed -i 's/\r$//' /var/www/html/deploy/docker/run.sh && \
    chmod +x /var/www/html/deploy/docker/run.sh

CMD ["/bin/sh", "-c", "/var/www/html/deploy/docker/run.sh"]
