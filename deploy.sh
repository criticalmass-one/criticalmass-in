#!/bin/bash
set -e

PHP=/opt/plesk/php/8.5/bin/php
COMPOSER="$PHP /usr/lib/plesk-9.0/composer.phar"
NPM="PATH=/opt/plesk/node/20/bin:$PATH npm"

echo "=== Git Pull ==="
git pull

echo "=== Composer Install ==="
$COMPOSER install --no-dev --optimize-autoloader

echo "=== NPM Install ==="
eval $NPM install

echo "=== NPM Build ==="
eval $NPM run build

echo "=== Cache Clear ==="
$PHP bin/console cache:clear

echo "=== Migrations ==="
$PHP bin/console doctrine:migrations:migrate --no-interaction

echo "=== Done ==="
