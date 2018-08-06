#!/usr/bin/env bash

DIR="$( cd "$( dirname $( dirname $( dirname $( dirname "${BASH_SOURCE[0]}") ) ) )" && pwd )"

find ${DIR}/spec ${DIR}/src -type f -name "*.php" -print0 | \
    xargs -0 -n1 -P32 php -l
