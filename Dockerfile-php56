FROM php:5.6-cli

RUN pecl install xdebug-2.5.5 \
    && docker-php-ext-enable xdebug

COPY . /usr/src/sdk

WORKDIR /usr/src/sdk

CMD [ "php", "./run-tests.php" ]
