#!/bin/sh

mkdir /var/www/html/deploy/docker/mysql && \
wget "https://storage.yandexcloud.net/cloud-certs/CA.pem" -O /var/www/html/deploy/docker/mysql/root.pem && \
chmod 0755 /var/www/html/deploy/docker/mysql/root.pem

cd /var/www/html

mv .env.example .env

/usr/bin/supervisord -c /etc/supervisord.conf

chown -R www-data:www-data /var/www/html/storage/app/public
