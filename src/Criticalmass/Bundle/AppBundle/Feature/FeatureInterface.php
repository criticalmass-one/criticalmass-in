<?php declare(strict_types=1);

namespace Criticalmass\Bundle\AppBundle\Feature;

interface FeatureInterface
{
    public function enabled(): bool;
    public function getName(): string;
}
