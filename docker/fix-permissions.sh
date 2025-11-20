#!/bin/sh
# Fix permissions for writable directory
# This ensures cache, logs, session, and uploads are writable by www-data

chown -R www-data:www-data /var/www/html/writable
chmod -R 777 /var/www/html/writable

# Ensure vendor is owned by www-data for any runtime composer operations
if [ -d /var/www/html/vendor ]; then
    chown -R www-data:www-data /var/www/html/vendor
fi

echo "Permissions fixed successfully"
