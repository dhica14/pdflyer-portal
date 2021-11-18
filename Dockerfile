FROM php:8.0-apache

# 設定ファイルをdockerコンテナ内のPHP、Apacheに読み込ませる
COPY ./app/php/php.ini /usr/local/etc/php/
COPY ./app/apache/*.conf /etc/apache2/sites-enabled/

COPY ./portal/ /var/www/html/portal/

# ミドルウェアインストール
RUN apt-get update \
    && apt-get install -y \
    git \
    zip \
    unzip \
    vim \
    libpng-dev \
    libpq-dev \
    && docker-php-ext-install pdo_mysql

# Debianでrootを使う場合　※最新LTS版（長期サポート版）
RUN curl -sL https://deb.nodesource.com/setup_current.x | bash -
RUN apt install -y nodejs


COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /composer
ENV PATH $PATH:/composer/vendor/bin

WORKDIR /var/www/html
RUN composer global require "laravel/installer"

# Laravelで必要になるmodRewriteを有効化する
RUN mv /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled
RUN /bin/sh -c a2enmod rewrite

# githubにアップされないので必要
WORKDIR /var/www/html/portal
RUN composer install
