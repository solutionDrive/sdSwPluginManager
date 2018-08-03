#!/usr/bin/env bash

PHP_VERSION=$(php -r "echo PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION;")

php ./vendor/bin/phpspec-standalone.php${PHP_VERSION}.phar run --no-code-generation --format=dot
