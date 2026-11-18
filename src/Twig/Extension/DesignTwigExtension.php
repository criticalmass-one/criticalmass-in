<?php declare(strict_types=1);

namespace App\Twig\Extension;

use App\Design\DesignChoice;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DesignTwigExtension extends AbstractExtension
{
    public function __construct(
        private readonly DesignChoice $designChoice,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('design_v2', $this->designChoice->isV2(...)),
            new TwigFunction('design_switch_available', $this->designChoice->isAvailable(...)),
        ];
    }
}
