<?php declare(strict_types=1);

namespace App\Criticalmass\Feature\Feature;

class Art11Feature extends AbstractFeature
{
    public function __construct(bool $featureArt11)
    {
        $this->enabled = $featureArt11;
    }
}
