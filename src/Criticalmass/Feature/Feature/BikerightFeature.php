<?php declare(strict_types=1);

namespace App\Criticalmass\Feature\Feature;

class BikerightFeature extends AbstractFeature
{
    public function __construct(bool $featureBikeright)
    {
        $this->enabled = $featureBikeright;
    }
}
