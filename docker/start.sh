#!/bin/sh

# Lancer les migrations
php artisan migrate --force

# Créer le lien storage si nécessaire
php artisan storage:link 2>/dev/null || true

# Démarrer PHP-FPM en arrière-plan
php-fpm -D

# Démarrer Nginx au premier plan
nginx -g "daemon off;"