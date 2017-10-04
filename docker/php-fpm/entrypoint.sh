#!/usr/bin/env bash

if [[ "$@" == "php-fpm" ]]
then
    composer install --prefer-dist
fi

exec "$@"