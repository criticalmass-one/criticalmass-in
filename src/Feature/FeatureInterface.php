<?php declare(strict_types=1);

namespace App\Feature;

interface FeatureInterface
{
    public function enabled(): bool;
    public function getName(): string;
}
