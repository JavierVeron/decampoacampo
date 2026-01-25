FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    git unzip zip libmariadb-dev \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && docker-php-ext-install mysqli pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

RUN echo 'Alias /api /var/www/html/app/index.php' >> /etc/apache2/apache2.conf

RUN echo '<Directory /var/www/html> \n\
    Options Indexes FollowSymLinks \n\
    AllowOverride All \n\
    Require all granted \n\
</Directory>' >> /etc/apache2/apache2.conf

WORKDIR /var/www/html

COPY composer.json composer.lock* ./

RUN composer install --no-interaction --no-scripts --no-autoloader

COPY . .

RUN composer dump-autoload --optimize && \
    chown -R www-data:www-data /var/www/html