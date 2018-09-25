<?php declare(strict_types=1);

namespace App\Criticalmass\Feature\FeatureManager;

use App\Criticalmass\Feature\Feature\FeatureInterface;

interface FeatureManagerInterface
{
    public function addFeature(FeatureInterface $feature): FeatureManagerInterface;
    public function isFeatureEnabled(string $featureName): bool;
}
