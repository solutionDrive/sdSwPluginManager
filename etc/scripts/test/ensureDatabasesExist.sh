#!/usr/bin/env sh

echo \
    "CREATE DATABASE IF NOT EXISTS test56; " \
    "CREATE DATABASE IF NOT EXISTS test70; " \
    "CREATE DATABASE IF NOT EXISTS test71; " \
    "CREATE DATABASE IF NOT EXISTS test72; " \
    | mysql -uroot -proot -hmysql;
