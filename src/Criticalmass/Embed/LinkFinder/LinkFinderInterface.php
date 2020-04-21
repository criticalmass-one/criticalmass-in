<?php declare(strict_types=1);

namespace App\Criticalmass\Embed\LinkFinder;

interface LinkFinderInterface
{
    public function findInText(string $text): array;
}