#!/usr/bin/env bash

FOLDERS="./src ./spec"
vendor/bin/ecs-standalone.phar check --no-progress-bar -n -c vendor/bin/easy-coding-standard-php7.1.yml $FOLDERS $@
