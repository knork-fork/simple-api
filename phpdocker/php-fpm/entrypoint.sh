#!/bin/sh
set -e

# Ensure error log file exists with correct permissions
mkdir -p /var/log
chown www-data:www-data /var/log
chmod 775 /var/log

mkdir -p /var/log/xhprof
chmod 777 /var/log/xhprof

# Ensure correct permissions for config dir (so that caching works)
chmod -R 777 /application/config

# Add safe directory to git config to prevent composer dump-autoload warnings
git config --global --add safe.directory /application

# Run the original entrypoint (if any) or PHP-FPM
exec "$@"
