#!/bin/bash

REPO_DIR="/var/www/booking.agrad.ru/"

function COMPOSER_UPDATE() {
    echo "Composer updating..."
    cd ${REPO_DIR} && /usr/bin/php composer.phar update --no-plugins --no-scripts --no-dev;
    echo "Composer has been updated"
}

function START_SERVICE() {
    echo "Service starting..."
    php-fpm7;
    nginx;
    echo "Service has been started"
}


START_SERVICE && COMPOSER_UPDATE && /bin/bash