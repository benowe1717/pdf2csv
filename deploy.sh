#!/bin/bash

PHP=`which php`
COMPOSER=`which composer`
NOHUP=`which nohup`

BINARIES=("php" "composer" "nohup")
WORKERS=("async" "scheduler_main")
TIME_LIMIT="3600"

APP_ENV="prod"

for ((i=0; i < ${#BINARIES[@]}; i++)); do
    RESULT=$(which ${BINARIES[i]} > /dev/null 2>&1; echo $?)
    if [[ "$RESULT" != 0 ]]; then
        /bin/echo "ERROR: Unable to locate ${BINARIES[i]}!"
        exit 1
    fi
done

$COMPOSER dump-env $APP_ENV
$COMPOSER install --no-dev --optimize-autoloader
$PHP ./bin/console doctrine:migrations:migrate --no-interaction
$PHP ./bin/console importmap:install
$PHP ./bin/console sass:build
$PHP ./bin/console asset-map:compile
$PHP ./bin/console cache:clear

# Now start up workers to consume the messages
for worker in "${WORKERS[@]}"; do
    $NOHUP ./start_worker.sh "$worker" "$TIME_LIMIT" &
done
