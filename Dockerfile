FROM alpine:3.5

MAINTAINER Simone Pasquini <simone.pasquini@gmail.com>

RUN apk update && \
    apk add \
        'ca-certificates' \
        'wget' \
        'php5=5.6.30-r0' \
        'php5-json=5.6.30-r0' \
        'php5-openssl=5.6.30-r0' \
        'php5-zlib=5.6.30-r0' \
        'php5-dom=5.6.30-r0' \
        'php5-phar=5.6.30-r0' && \
    rm -rf /var/cache/apk/*

RUN wget https://phar.phpunit.de/phpunit-5.7.phar && \
    chmod +x phpunit-5.7.phar && \
    mv phpunit-5.7.phar /usr/local/bin/phpunit

RUN wget https://getcomposer.org/installer && \
    php installer --install-dir=bin --filename=composer && \
    rm installer
