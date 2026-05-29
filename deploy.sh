#!/bin/bash
set -e

PHP=/opt/plesk/php/8.5/bin/php
COMPOSER="$PHP /usr/lib/plesk-9.0/composer.phar"
NPM="PATH=/opt/plesk/node/20/bin:$PATH npm"

usage() {
    echo "Usage: $0 [steps...]"
    echo ""
    echo "Without arguments, all steps are executed."
    echo ""
    echo "Available steps:"
    echo "  pull        Git pull"
    echo "  composer    Composer install"
    echo "  npm         NPM install"
    echo "  build       NPM build"
    echo "  cache       Cache clear"
    echo "  migrate     Doctrine migrations"
    echo ""
    echo "Examples:"
    echo "  $0                   # Full deploy"
    echo "  $0 pull composer     # Only git pull and composer install"
    echo "  $0 build cache       # Only rebuild assets and clear cache"
}

step_pull() {
    echo "=== Git Pull ==="
    git pull
}

step_composer() {
    echo "=== Composer Install ==="
    $COMPOSER install --no-dev --optimize-autoloader
}

step_npm() {
    echo "=== NPM Install ==="
    eval $NPM install
}

step_build() {
    echo "=== NPM Build ==="
    eval $NPM run build
}

step_cache() {
    echo "=== Cache Clear ==="
    $PHP bin/console cache:clear
}

step_migrate() {
    echo "=== Migrations ==="
    $PHP bin/console doctrine:migrations:migrate --no-interaction
}

if [ "$1" = "--help" ] || [ "$1" = "-h" ]; then
    usage
    exit 0
fi

if [ $# -eq 0 ]; then
    step_pull
    step_composer
    step_npm
    step_build
    step_cache
    step_migrate
else
    for step in "$@"; do
        case $step in
            pull)     step_pull ;;
            composer) step_composer ;;
            npm)      step_npm ;;
            build)    step_build ;;
            cache)    step_cache ;;
            migrate)  step_migrate ;;
            *)        echo "Unknown step: $step"; usage; exit 1 ;;
        esac
    done
fi

echo "=== Done ==="
