FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
  && docker-php-ext-install pdo_mysql zip \
  && a2enmod rewrite

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --optimize-autoloader --no-interaction

# Aponta o Apache para a pasta web/ do Yii2
RUN sed -i 's#/var/www/html#/var/www/html/web#' /etc/apache2/sites-available/000-default.conf \
  && sed -i 's#/var/www/#/var/www/html/web/#' /etc/apache2/apache2.conf

RUN mkdir -p /var/www/html/runtime /var/www/html/web/assets \
  && chown -R www-data:www-data /var/www/html/runtime /var/www/html/web/assets

RUN chmod +x docker/entrypoint.sh

EXPOSE 80

ENTRYPOINT ["docker/entrypoint.sh"]
