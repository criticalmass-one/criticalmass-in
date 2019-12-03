<?php declare(strict_types=1);

namespace App\DBAL\CriticalDriver;

use Doctrine\DBAL\Platforms\MySqlPlatform;

class CriticalPlatform extends MySqlPlatform
{
    /**
     * {@inheritdoc}
     */
    public function getTruncateTableSQL($tableName, $cascade = false): string
    {
        return sprintf('SET foreign_key_checks = 0;TRUNCATE %s;', $tableName);
    }
}
