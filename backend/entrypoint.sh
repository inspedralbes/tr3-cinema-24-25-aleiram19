#!/bin/bash
set -e

# Instalar dependencias
composer install

# Crear el archivo .env si no existe
php artisan key:generate

# Esperar a que MySQL esté listo
echo "Esperando a que MySQL esté disponible..."
until php -r "try { new PDO('mysql:host=mysql;dbname=cinema', 'user', 'user'); echo 'MySQL está disponible!'; } catch(PDOException \$e) { echo \$e->getMessage() . PHP_EOL; sleep(5); }" | grep -q 'disponible'; do
  echo "MySQL no está disponible aún - esperando..."
  sleep 3
done

# Ejecutar migraciones
php artisan migrate:fresh --seed

# Iniciar el servidor
php artisan serve --host=0.0.0.0 --port=8000
