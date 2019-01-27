FROM php:5.6-cli
COPY . /usr/src/sdk
WORKDIR /usr/src/sdk
CMD [ "php", "./run-tests.php" ]
