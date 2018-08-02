#!/usr/bin/env bash

VERSION=$1

if [ -z "${VERSION}" ]; then
    echo "You must give a version to execute command on, for example 71 for PHP 7.1 container."
    exit 1
fi

shift

PROJECT_DIR="$( cd "$( dirname $( dirname $( dirname $( dirname "${BASH_SOURCE[0]}" ) ) ) )" && pwd )"
PROJECT_NAME="sdswpluginmanager"
PHP_CONTAINER_NAME="${PROJECT_NAME}_php${VERSION}_1"

docker exec --workdir ${WORKDIR:-"/var/www/project${VERSION}"} -it ${PHP_CONTAINER_NAME} $@
