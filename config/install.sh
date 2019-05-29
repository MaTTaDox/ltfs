#!/usr/bin/env bash
sudo su <<EOF
wget -q -O- https://packages.sury.org/php/apt.gpg | apt-key add -
echo "deb https://packages.sury.org/php/ stretch main" | tee /etc/apt/sources.list.d/php.list
apt-get update
apt-get install -y supervisor git php7.2 php7.2-curl php7.2-cli php7.2-common php7.2-json php7.2-opcache php7.2-mysql php7.2-mbstring php7.2-zip php7.2-xml php7.2-fpm nginx unzip
curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer
cd /var/www
ssh-keyscan github.com >> ~/.ssh/known_hosts
git clone git://github.com/mattadox/ltfs.git
cd /var/www/ltfs
aws s3 cp s3://longterm-fs/.env /var/www/ltfs/.env --region=eu-central-1
mkdir -p config/jwt
aws s3 cp s3://longterm-fs/private.pem /var/www/ltfs/config/jwt/private.pem --region=eu-central-1
aws s3 cp s3://longterm-fs/public.pem /var/www/ltfs/config/jwt/public.pem --region=eu-central-1
chmod 777 config/jwt/*
composer install
rm -f /etc/nginx/sites-enabled/default
php bin/console deploy:nginx
php bin/console deploy:supervisor
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate -n --allow-no-migration
aws sns publish --topic-arn "arn:aws:sns:eu-central-1:040427673238:as-ltfs" --message "{}"
EOF