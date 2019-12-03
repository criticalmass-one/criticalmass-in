<?php declare(strict_types=1);

namespace App\DBAL\CriticalDriver;

use Doctrine\DBAL\Driver\PDOMySql\Driver;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class CriticalDriver extends Driver
{
    /**
     * {@inheritdoc}
     */
    public function getDatabasePlatform(): AbstractPlatform
    {
        return new CriticalPlatform();
    }
}
