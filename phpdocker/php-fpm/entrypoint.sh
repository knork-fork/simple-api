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

# Run the original entrypoint (if any) or PHP-FPM
exec "$@"
