#!/usr/bin/env bash
sudo su <<EOF
wget -q -O- https://packages.sury.org/php/apt.gpg | apt-key add -
echo "deb https://packages.sury.org/php/ stretch main" | tee /etc/apt/sources.list.d/php.list
apt-get update
apt-get install -y supervisor git php7.2 php7.2-cli php7.2-common php7.2-json php7.2-opcache php7.2-mysql php7.2-mbstring php7.2-zip php7.2-xml php7.2-fpm nginx unzip
curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer
cd /var/www
ssh-keyscan github.com >> ~/.ssh/known_hosts
git clone git://github.com/mattadox/ltfs.git
cd /var/www/ltfs
aws s3 cp s3://longterm-fs/.env /var/www/ltfs/.env --region=eu-central-1
mkdir -p config/jwt
openssl genrsa -out config/jwt/private.pem -aes256 -passout pass:ltfs 4096
openssl rsa -pubout -in config/jwt/private.pem -passin pass:ltfs -out config/jwt/public.pem
chmod 777 config/jwt/*
composer install
php bin/console deploy:nginx
php bin/console deploy:supervisor
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate -n --allow-no-migration
EOF