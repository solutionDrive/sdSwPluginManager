#!/usr/bin/env bash

FILTERED_FOLDERS=`find ./ -mindepth 1 -maxdepth 1 -type d | grep -Ev 'Resources|tests|etc|spec|logs|.phpspec|.git|vendor|.idea'`
SEPERATOR=" "
FOLDERS=$(printf "${SEPERATOR}%s" "${FILTERED_FOLDERS[@]}")

vendor/bin/ecs-standalone.phar check --no-progress-bar -n -c vendor/bin/easy-coding-standard-php5.6.yml $FOLDERS $@
