#!/usr/bin/env bash

FOLDERS="./src"
vendor/bin/ecs-standalone.phar check --no-progress-bar -n -c vendor/bin/easy-coding-standard-php5.6.yml $FOLDERS $@
