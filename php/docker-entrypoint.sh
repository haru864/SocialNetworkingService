#!/bin/bash

echo "Start wait-for-it.sh"

/usr/local/bin/wait-for-it.sh mysql:3306 -t 0 -- php app/console.php migrate --init

echo "Finish wait-for-it.sh"

exec "$@"
