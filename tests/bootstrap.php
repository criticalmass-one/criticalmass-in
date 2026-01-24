<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

if (file_exists(dirname(__DIR__).'/config/bootstrap.php')) {
    require dirname(__DIR__).'/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

// Load fixtures once before all tests run
// This ensures data is in the database before any test starts
if ($_SERVER['APP_ENV'] === 'test' && !isset($_SERVER['SKIP_FIXTURES'])) {
    // Use custom script that handles foreign key constraints properly
    passthru('php ' . __DIR__ . '/load_fixtures.php 2>&1', $returnCode);

    if ($returnCode !== 0) {
        echo "Warning: Failed to load fixtures. API tests may fail.\n";
    }
}
