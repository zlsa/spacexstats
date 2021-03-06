#!/bin/bash

#Variables
DBHOST=localhost
DBNAME=spacexstats
DBUSER=localuser
DBPASSWD=localpassword

echo "Begin custom provisioning..."

echo "[1/7] Installing ffmpeg..."
sudo add-apt-repository ppa:kirillshkrogalev/ffmpeg-next -y >/dev/null 2>&1
sudo apt-get update
sudo apt-get install ffmpeg -y

echo "[2/7] Installing & starting Elasticsearch..."
sudo apt-get update

# install java
sudo apt-get install openjdk-7-jre-headless -y >/dev/null 2>&1

# fetch and install elasticsearch
wget https://download.elasticsearch.org/elasticsearch/elasticsearch/elasticsearch-1.7.3.deb >/dev/null 2>&1
sudo dpkg -i elasticsearch-1.7.3.deb >/dev/null 2>&1

# make elasticsearch run on startup
update-rc.d elasticsearch defaults 95 10

sudo service elasticsearch start

echo "[3/7] Installing PHP extensions..."
sudo apt-get install php5-imagick -y
sudo service php5-fpm restart

echo "[4/7] Customizing MySQL..."
mysql -uroot -psecret -e "grant all privileges on $DBNAME.* to '$DBUSER'@'localhost' identified by '$DBPASSWD'"

#uninstall postgres
if [ -f "/etc/init.d/postgresql" ];  then
	apt-get purge -y postgresql-9.4 postgresql-client-common postgresql-contrib-9.4 postgresql-client-9.4 postgresql-common
	apt-get autoremove -y -qq
fi

echo "[5/7] Migrating..."
cd /home/vagrant/spacexstats
php artisan migrate
php artisan db:seed

echo "[6/7] Setting up Node.js"
# --no-bin-links is not required if you are not using Vagrant for Windows
npm install socket.io ioredis express --save --no-bin-links
npm install -g forever
forever start socket.js

echo "[7/7] Restarting nginx..."
service nginx restart