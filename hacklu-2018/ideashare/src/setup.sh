#!/bin/bash
apt update && apt upgrade -y

apt install -y apache2
echo 'export HOSTNAME=$(hostname -f)' >> /etc/apache2/envvars
cp conf/apache2/ports.conf /etc/apache2/
cp conf/apache2/000-default.conf /etc/apache2/sites-available
a2ensite 000-default
a2enmod headers

# PHP
apt install -y php libapache2-mod-php php-sqlite3 php-xml

chrome
apt install -y chromium-browser
wget https://dl.google.com/linux/direct/google-chrome-stable_current_amd64.deb
dpkg -i google-chrome-stable_current_amd64.deb
apt-get -fy install



# copy www
rm -r /var/www/*
mkdir /var/www/html
cp -r html/* /var/www/html 
cp gendb.php /var/www/


# gen db
cd /var/www/
php gendb.php
chown -R www-data:www-data /var/www/

# restart apache
service apache2 restart