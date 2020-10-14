<?php declare(strict_types=1);

namespace App\Twig\Extension;

use App\Criticalmass\StaticMap\UrlGenerator\UrlGeneratorInterface;
use App\EntityInterface\StaticMapableInterface;

class StaticmapsTwigExtension extends \Twig_Extension
{
    /** @var UrlGeneratorInterface $urlGenerator */
    protected $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('static_map', [$this, 'staticmap',], ['is_safe' => ['raw']]),
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

