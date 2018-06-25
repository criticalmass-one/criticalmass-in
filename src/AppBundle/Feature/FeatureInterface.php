<?php declare(strict_types=1);

namespace AppBundle\Feature;

interface FeatureInterface
{
    public function enabled(): bool;
    public function getName(): string;
}
