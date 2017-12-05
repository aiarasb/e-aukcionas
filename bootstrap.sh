#!/usr/bin/env bash

# get root access
sudo su

sudo apt-add-repository ppa:ondrej/php
apt-get update && sudo apt-get upgrade

# install git
apt-get install git

# install nginx
apt-get install -y nginx
service nginx start

# install node
apt-get install -y g++
curl -sL https://deb.nodesource.com/setup_6.x | sudo -E bash -
sudo apt-get install -y nodejs

# install php
apt-get install -y php7.1-cli php7.1-common php7.1-fpm php7.1-xml php7.1-mysql php-xdebug

# install composer globally
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('SHA384', 'composer-setup.php') === '544e09ee996cdf60ece3804abc52599c22b1f40f4323403c44d44fdfdd586475ca9813a858088ffbc1f233e9b180f061') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
mv composer.phar /usr/local/bin/composer

# set up nginx server
rm /etc/nginx/sites-available/default
rm /etc/nginx/sites-enabled/default
cp /srv/.provision/nginx/nginx.conf /etc/nginx/sites-available/site.conf
chmod 644 /etc/nginx/sites-available/site.conf
ln -sf /etc/nginx/sites-available/site.conf /etc/nginx/sites-enabled/site.conf
sed -e "s/sendfile on;/sendfile off;/" /etc/nginx/nginx.conf > temp_file
mv -f temp_file /etc/nginx/nginx.conf
service nginx restart

# set locale
export LC_ALL=C

# add default ssh dir
echo "cd /srv" | tee -a /etc/bash.bashrc

# install mariadb
apt-get install -y mariadb-server
