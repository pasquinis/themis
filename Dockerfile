FROM alpine:3.5

MAINTAINER Simone Pasquini <simone.pasquini@gmail.com>

RUN apk update && \
    apk add \
        'ca-certificates' \
        'wget' \
        'sqlite' \
        'php5=5.6.36-r0' \
        'php5-json=5.6.36-r0' \
        'php5-openssl=5.6.36-r0' \
        'php5-zlib=5.6.36-r0' \
        'php5-dom=5.6.36-r0' \
        'php5-sqlite3=5.6.36-r0' \
        'php5-pdo_sqlite=5.6.36-r0' \
        'php5-phar=5.6.36-r0' \
        'php5-ctype=5.6.36-r0' && \
    rm -rf /var/cache/apk/*

RUN wget https://getcomposer.org/installer && \
    php installer --install-dir=bin --filename=composer && \
    rm installer

RUN sed -i "s/;date.timezone =.*/date.timezone = UTC/" /etc/php5/php.ini
RUN sed -i "s/log_errors =.*/log_errors = On/" /etc/php5/php.ini
RUN sed -i "s/;error_log = php_errors.log/error_log = \/var\/log\/php.log/" /etc/php5/php.ini

ADD . /themis
WORKDIR /themis

RUN composer install
CMD ["php", "-S", "0.0.0.0:8080", "-t", "/themis/public", "/themis/public/index.php"]
