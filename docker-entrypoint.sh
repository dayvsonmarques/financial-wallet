#!/bin/bash
set -e

if [ "$(id -u)" = "0" ]; then
    # Fix existing permissions first
    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
    
    # Run as www-data with umask set
    # umask 0002 ensures new files: 664 (rw-rw-r--), directories: 775 (rwxrwxr-x)
    exec gosu www-data bash -c "umask 0002 && exec $*"
else
    exec "$@"
fi
