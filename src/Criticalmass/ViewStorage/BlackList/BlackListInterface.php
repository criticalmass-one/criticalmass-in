<?php declare(strict_types=1);

namespace App\Criticalmass\ViewStorage\BlackList;

interface BlackListInterface
{
    public function isBlackListed(): bool;
}
