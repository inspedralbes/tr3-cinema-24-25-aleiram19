#!/bin/bash
set -e

# Instalar dependencias
composer install

# Crear el archivo .env si no existe
php artisan key:generate

# Ejecutar migraciones
php artisan migrate:fresh --seed

# Iniciar el servidor
php artisan serve --host=0.0.0.0 --port=8000
