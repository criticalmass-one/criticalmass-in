<?php

/**
 * Load fixtures for testing.
 * This script handles foreign key constraints by disabling them temporarily.
 */

// Force test environment
$_SERVER['APP_ENV'] = 'test';
$_SERVER['APP_DEBUG'] = '1';

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;

// Load environment variables
if (file_exists(dirname(__DIR__) . '/.env')) {
    (new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');
}

// Boot kernel to get the database connection
$kernel = new Kernel('test', true);
$kernel->boot();

/** @var \Doctrine\DBAL\Connection $connection */
$connection = $kernel->getContainer()->get('doctrine')->getConnection();

// Disable foreign key checks for MySQL/MariaDB
try {
    $connection->executeStatement('SET FOREIGN_KEY_CHECKS=0');
    echo "Foreign key checks disabled.\n";
} catch (\Exception $e) {
    echo "Could not disable foreign key checks: " . $e->getMessage() . "\n";
}

$kernel->shutdown();

// Now run fixtures
$command = sprintf(
    'php %s/bin/console doctrine:fixtures:load --env=test --no-interaction 2>&1',
    dirname(__DIR__)
);

passthru($command, $returnCode);

// Re-enable foreign key checks (boot a new kernel)
$kernel = new Kernel('test', true);
$kernel->boot();
$connection = $kernel->getContainer()->get('doctrine')->getConnection();

try {
    $connection->executeStatement('SET FOREIGN_KEY_CHECKS=1');
    echo "Foreign key checks re-enabled.\n";
} catch (\Exception $e) {
    echo "Could not re-enable foreign key checks: " . $e->getMessage() . "\n";
}

$kernel->shutdown();

exit($returnCode);
