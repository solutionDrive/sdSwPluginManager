#!/usr/bin/env sh

SHOPWARE_VERSION=5.4.5
SHOPWARE_URL=http://releases.s3.shopware.com.s3.amazonaws.com/install_5.4.5_6847c0845f0f97230aa05c7294fa726a96dda3ff.zip

DB_HOST=mysql
DB_PORT=3306
DB_USERNAME=root
DB_PASSWORD=root
DB_DATABASE="getenv('MYSQL_DATABASE')"
CONFIG_FILE=${PROJECT_HOME}/config.php

cd ${PROJECT_HOME}
wget -O install.zip "${SHOPWARE_URL}"

unzip install.zip

# write config
printf '%s\n' ",s~'host' => '.*'~'host' => '${DB_HOST}'~g" w q | ed -s "${CONFIG_FILE}"
printf '%s\n' ",s~'port' => '.*'~'port' => '${DB_PORT}'~g" w q | ed -s "${CONFIG_FILE}"
printf '%s\n' ",s~'username' => '.*'~'username' => '${DB_USERNAME}'~g" w q | ed -s "${CONFIG_FILE}"
printf '%s\n' ",s~'password' => '.*'~'password' => '${DB_PASSWORD}'~g" w q | ed -s "${CONFIG_FILE}"
printf '%s\n' ",s~'dbname' => '.*'~'dbname' => ${DB_DATABASE}~g" w q | ed -s "${CONFIG_FILE}"

# install shopware including database
php recovery/install/index.php \
    --no-interaction \
    --quiet \
    --no-skip-import \
    --db-host="${DB_HOST}" \
    --db-user="${DB_USERNAME}" \
    --db-password="${DB_PASSWORD}" \
    --db-name="${MYSQL_DATABASE}" \
    --shop-locale="de_DE" \
    --shop-host="${WEB_HOST}" \
    --shop-path="" \
    --shop-name="Testshop" \
    --shop-email="sdadmin@sd.test" \
    --shop-currency="EUR" \
    --admin-username="sdadmin" \
    --admin-password="sdadmin" \
    --admin-email="sdadmin@sd.test" \
    --admin-name="sdadmin" \
    --admin-locale="de_DE"

chown -R www-data:www-data ${PROJECT_HOME}/
