FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www/html

RUN a2enmod rewrite \
    && printf "ServerName localhost\n" > /etc/apache2/conf-available/servername.conf \
    && a2enconf servername

RUN echo "upload_max_filesize = 20M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 25M" >> /usr/local/etc/php/conf.d/uploads.ini

COPY . /var/www/html

RUN chown -R www-data:www-data /var/www/html

