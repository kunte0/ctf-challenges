#!/bin/bash
apt update && apt upgrade -y

apt install -y apache2
cp conf/ports.conf /etc/apache2/
cp conf/000-default.conf /etc/apache2/sites-available
a2ensite 000-default

# PHP
add-apt-repository -y ppa:ondrej/php
apt update -y
apt install -y php5.6


# copy www
rm -r /var/www/*
mkdir /var/www/html

cp -r html/* /var/www/html

chmod -R 444 /var/www/
chmod -R +x /var/www/

# restart apache
service apache2 restart