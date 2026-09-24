#!/bin/sh
set -e

# Configure port for Nginx
export PORT="${PORT:-8080}"
if [ -f /etc/nginx/templates/default.conf.template ]; then
    mkdir -p /etc/nginx/http.d /etc/nginx/conf.d /etc/nginx/sites-enabled
    rm -f /etc/nginx/sites-enabled/default
    sed "s/\${PORT}/${PORT}/g" /etc/nginx/templates/default.conf.template > /etc/nginx/conf.d/default.conf
    cp /etc/nginx/conf.d/default.conf /etc/nginx/http.d/default.conf 2>/dev/null || true
fi

# Ensure storage and bootstrap/cache directories exist
mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/storage/app/public \
         /var/www/html/storage/app/private \
         /var/www/html/bootstrap/cache

# Fix permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Clear framework caches to guarantee fresh environment configuration on container start
php /var/www/html/artisan config:clear || true
php /var/www/html/artisan route:clear || true
php /var/www/html/artisan view:clear || true

# If custom command was passed, execute that; otherwise start supervisord
if [ "$#" -gt 0 ]; then
    exec "$@"
else
    exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
fi
