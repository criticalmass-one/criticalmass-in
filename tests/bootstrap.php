<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

if (file_exists(dirname(__DIR__).'/config/bootstrap.php')) {
    require dirname(__DIR__).'/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

// Note: Fixtures should be loaded manually before running tests if needed:
// php bin/console doctrine:database:drop --env=test --force
// php bin/console doctrine:database:create --env=test
// php bin/console doctrine:schema:create --env=test
// php bin/console doctrine:fixtures:load --env=test --no-interaction
