#!/usr/bin/env bash


PROJECT_DIR="$( cd "$( dirname $( dirname $( dirname $( dirname "${BASH_SOURCE[0]}") ) ) )" && pwd )"
PROJECT_NAME=sdswpluginmanager
DOCKER_COMPOSE_YAML=${PROJECT_DIR}"/etc/test/docker-compose.yml"

function prepare {
    echo "No preperation needed in here."
}

function echo_configuration {
    echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~"
    echo "Running (web)server on ports:"
    echo "PHP 5.6: 10856"
    echo "PHP 7.0: 10870"
    echo "PHP 7.1: 10871"
    echo "PHP 7.2: 10872"
    echo "MySQL:   10331"
    echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~"
}

function build_container {
    prepare
    docker_compose_cmd build $@
}

function run_container {
    prepare
    echo_configuration
    docker_compose_cmd up $@
}

function start_container {
    prepare
    echo_configuration
    docker_compose_cmd up --no-start $@
    docker_compose_cmd start $@
    WORKDIR=/var/www ${PROJECT_DIR}/etc/scripts/test/runInDev.sh 56 sh /opt/host/etc/scripts/test/ensureProjectHomeExists.sh
    WORKDIR=/var/www ${PROJECT_DIR}/etc/scripts/test/runInDev.sh 70 sh /opt/host/etc/scripts/test/ensureProjectHomeExists.sh
    WORKDIR=/var/www ${PROJECT_DIR}/etc/scripts/test/runInDev.sh 71 sh /opt/host/etc/scripts/test/ensureProjectHomeExists.sh
    WORKDIR=/var/www ${PROJECT_DIR}/etc/scripts/test/runInDev.sh 72 sh /opt/host/etc/scripts/test/ensureProjectHomeExists.sh
    sleep 15 && WORKDIR=/var/www ${PROJECT_DIR}/etc/scripts/test/runInDev.sh 56 sh /opt/host/etc/scripts/test/ensureDatabasesExist.sh
}

function stop_container {
    docker_compose_cmd stop $@
}

function restart_container {
    stop_container $@
    start_container $@
}


function remove_container {
    docker_compose_cmd down -v
    docker_compose_cmd rm -v $@
}

function get_logs {
    docker_compose_cmd logs $@
}

function reset_container {
    remove_container -s -f $@
    start_container $@
}

function docker_compose_cmd {
    docker-compose \
        -f ${DOCKER_COMPOSE_YAML} \
        -p ${PROJECT_NAME} \
        $@
}


## start of the real program

#set -a
#. ${PROJECT_DIR}/etc/dev/docker/dev.docker.env
#set +a

export PROJECT_DIR
export PROJECT_NAME

case "$1" in
    build)
        shift
        build_container $@
        ;;
    run)
        shift
        run_container $@
        ;;
    start)
        shift
        start_container $@
        ;;
    stop)
        shift
        stop_container $@
        ;;
    restart)
        shift
        restart_container $@
        ;;
    reset)
        shift
        reset_container $@
        ;;
    remove|rm)
        shift
        remove_container $@
        ;;
    log|logs)
        shift
        get_logs $@
        ;;
    *)
        echo "usage: start/stop/run/restart/build/reset/remove/logs"
        ;;
esac
