#!/bin/bash

# スクリプトの実際のパスを取得（シンボリックリンクを解決）
SCRIPT_PATH=$(readlink -f "$0")

# スクリプトが存在するディレクトリのパスを取得
SCRIPT_DIR=$(dirname "$SCRIPT_PATH")

echo "*/1 * * * * /usr/bin/php ${SCRIPT_DIR}/backend/app/Batch/ReservedTweetExecution.php > ${SCRIPT_DIR}/backend/log/batch.log 2>&1" >> /tmp/mycron
echo "* * */1 * * /usr/bin/php ${SCRIPT_DIR}/backend/app/Batch/DeleteOldMail.php > ${SCRIPT_DIR}/backend/log/batch.log 2>&1" >> /tmp/mycron
crontab /tmp/mycron
