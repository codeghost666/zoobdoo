#!/usr/bin/env bash

CERTS_FOLDER="/var/www/docker/nginx/certs"
CONF_FOLDER="/var/www/docker/nginx/sites-enabled"
NUMBER_OF_NECESSARY_CERTS=0

for f in "$CERTS_FOLDER/default.crt" "$CERTS_FOLDER/default.key" "$CERTS_FOLDER/dhparam.pem"; do
    if [ -f "$f" ]; then
       NUMBER_OF_NECESSARY_CERTS=$[NUMBER_OF_NECESSARY_CERTS+1]
    fi
done

mkdir /etc/nginx/sites-enabled

if [ "$NUMBER_OF_NECESSARY_CERTS" -eq "3" ]; then
    mkdir /etc/nginx/certs

    cp "$CERTS_FOLDER/default.crt" /etc/nginx/certs/default.crt;
    cp "$CERTS_FOLDER/default.key" /etc/nginx/certs/default.key;
    cp "$CERTS_FOLDER/dhparam.pem" /etc/nginx/certs/dhparam.pem;

    cp "$CONF_FOLDER/erentpay-ssl.conf" /etc/nginx/sites-enabled/default.conf;
else
    cp "$CONF_FOLDER/erentpay.conf" /etc/nginx/sites-enabled/default.conf;
fi

exec "$@"