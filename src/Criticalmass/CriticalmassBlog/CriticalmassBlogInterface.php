<?php declare(strict_types=1);

namespace App\Criticalmass\CriticalmassBlog;

interface CriticalmassBlogInterface
{
    public function getArticles(): array;
}
