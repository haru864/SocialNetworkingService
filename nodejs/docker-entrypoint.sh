#!/bin/bash

echo "[nodejs] Start wait-for-it.sh"

/usr/local/bin/wait-for-it.sh redis:6379 -t 0

echo "[nodejs] Finish wait-for-it.sh"

exec "$@"
