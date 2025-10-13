FROM php:8.1.0RC6-fpm


RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    vim \
    unzip

RUN docker-php-ext-install pdo pdo_mysql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


RUN echo "alias ll='ls -al'" >> /root/.bashrc && \
    echo "alias ts='./vendor/bin/phpunit'" >> /root/.bashrc && \
    echo "alias composer-update='composer update'" >> /root/.bashrc


WORKDIR /var/www

