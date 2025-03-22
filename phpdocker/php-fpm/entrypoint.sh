#!/bin/sh
set -e

# Ensure error log file exists with correct permissions
mkdir -p /var/log
chown www-data:www-data /var/log
chmod 775 /var/log

# Run the original entrypoint (if any) or PHP-FPM
exec "$@"
