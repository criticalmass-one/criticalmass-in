<?php declare(strict_types=1);

namespace App\DQL;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\ORM\Query\SqlWalker;

/**
 * Fuer die beiden DQL-Funktionen, die sich nicht in Standard-SQL ausdruecken
 * lassen und deshalb wissen muessen, gegen welche Datenbank sie laufen.
 *
 * Die uebrigen brauchen das nicht: EXTRACT und CAST beherrschen MySQL und
 * PostgreSQL gleichermassen.
 */
trait PlatformAware
{
    protected function isPostgreSql(SqlWalker $sqlWalker): bool
    {
        return $sqlWalker->getConnection()->getDatabasePlatform() instanceof PostgreSQLPlatform;
    }
}
