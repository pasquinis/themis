FROM alpine:3.5

MAINTAINER Simone Pasquini <simone.pasquini@gmail.com>

RUN apk update && \
    apk add \
        'ca-certificates' \
        'wget' \
        'sqlite' \
        'php5=5.6.30-r0' \
        'php5-json=5.6.30-r0' \
        'php5-openssl=5.6.30-r0' \
        'php5-zlib=5.6.30-r0' \
        'php5-dom=5.6.30-r0' \
        'php5-sqlite3=5.6.30-r0' \
        'php5-phar=5.6.30-r0' && \
    rm -rf /var/cache/apk/*

RUN wget https://phar.phpunit.de/phpunit-5.7.phar && \
    chmod +x phpunit-5.7.phar && \
    mv phpunit-5.7.phar /usr/local/bin/phpunit

RUN wget https://getcomposer.org/installer && \
    php installer --install-dir=bin --filename=composer && \
    rm installer

RUN sed -i "s/;date.timezone =.*/date.timezone = UTC/" /etc/php5/php.ini
RUN sed -i "s/log_errors =.*/log_errors = On/" /etc/php5/php.ini
RUN sed -i "s/;error_log = php_errors.log/error_log = \/var\/log\/php.log/" /etc/php5/php.ini
