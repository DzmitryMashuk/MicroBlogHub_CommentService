FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/CommentService

COPY --chown=www-data:www-data ./app /var/www/CommentService

RUN composer install --prefer-dist --no-progress --no-interaction

RUN chown -R www-data:www-data /var/www/CommentService \
    && chmod -R 755 /var/www/CommentService \
    && chmod -R gu+w /var/www/CommentService/storage \
    && chmod -R guo+w /var/www/CommentService/storage

EXPOSE 9000
CMD ["php-fpm"]
