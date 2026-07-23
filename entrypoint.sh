#!/bin/sh
set -e

echo "Ajustando permisos de almacenamiento..."
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Verificar si DATABASE_URL existe
if [ -n "$DATABASE_URL" ]; then
    echo "DATABASE_URL detectada."
else
    echo "ADVERTENCIA: DATABASE_URL no está definida."
fi

# Optimización de Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Enlace simbólico de storage
php artisan storage:link --force || true

# Ejecutar migraciones automáticas
echo "Ejecutando migraciones de base de datos..."
php artisan migrate --force

# Iniciar PHP-FPM en segundo plano
php-fpm -D

# Iniciar Nginx en primer plano
echo "Iniciando Nginx..."
nginx -g 'daemon off;'