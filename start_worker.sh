#!/bin/bash

THREAD="$1"
TIME_LIMIT="$2"

while true; do
    php bin/console messenger:consume $THREAD --time-limit=$TIME_LIMIT
done
