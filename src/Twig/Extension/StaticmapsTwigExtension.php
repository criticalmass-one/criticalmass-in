<?php declare(strict_types=1);

namespace App\Twig\Extension;

use App\Criticalmass\StaticMap\UrlGenerator\UrlGeneratorInterface;
use App\EntityInterface\StaticMapableInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class StaticmapsTwigExtension extends AbstractExtension
{
    public function __construct(protected UrlGeneratorInterface $urlGenerator)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('static_map', $this->staticmap(...), ['is_safe' => ['raw']]),
        ];
    }

    public function staticmap(StaticMapableInterface $object, int $width = null, int $height = null, int $zoom = null): string
    {
        return $this->urlGenerator->generate($object, $width, $height, $zoom);
    }

    public function getName(): string
    {
        return 'staticmaps_extension';
    }
}
