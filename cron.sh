#!/bin/sh

# This script is used to run cron jobs.
# Example usage: sh cron.sh CreatePostTitle DEV
# File needs to be executable: chmod +x cron.sh and LF

if [ "$2" = "PROD" ]; then
  echo "Changing path to Prod";
  echo "---------"
  echo "New path: /home/u428100319/domains/<site>/public_html";
  # shellcheck disable=SC2164
  cd /home/u428100319/domains/<site>/public_html;
  echo "---------"
fi

: '
  Jobs
  - ExampleCron
'

echo "Running cron job $1"
php run-cron.php "$1"
