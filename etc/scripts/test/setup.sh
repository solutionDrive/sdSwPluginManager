#!/usr/bin/env bash

PROJECT_DIR="$( cd "$( dirname $( dirname $( dirname $( dirname "${BASH_SOURCE[0]}") ) ) )" && pwd )"

${PROJECT_DIR}/etc/scripts/test/runInDev.sh 56 sh /opt/host/etc/scripts/test/installShopware.sh
${PROJECT_DIR}/etc/scripts/test/runInDev.sh 70 sh /opt/host/etc/scripts/test/installShopware.sh
${PROJECT_DIR}/etc/scripts/test/runInDev.sh 71 sh /opt/host/etc/scripts/test/installShopware.sh
${PROJECT_DIR}/etc/scripts/test/runInDev.sh 72 sh /opt/host/etc/scripts/test/installShopware.sh

