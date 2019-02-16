<?php declare(strict_types=1);

namespace App\Criticalmass\Feature\Feature;

interface FeatureInterface
{
    public function isEnabled(): bool;
    public function setEnabled(bool $enabled): FeatureInterface;
    public function getName(): string;
}
