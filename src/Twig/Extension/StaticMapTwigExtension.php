<?php declare(strict_types=1);

namespace App\Twig\Extension;

use App\Criticalmass\StaticMap\StaticMapService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class StaticMapTwigExtension extends AbstractExtension
{
    public function __construct(
        private readonly StaticMapService $staticMapService,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('static_map_polyline', [$this, 'staticMapPolyline']),
            new TwigFunction('static_map_marker', [$this, 'staticMapMarker']),
        ];
    }

    public function staticMapPolyline(
        string $polyline,
        string $color,
        int $width = 600,
        int $height = 150,
        int $strokeWidth = 3,
    ): ?string {
        return $this->staticMapService->generatePolylineMap(
            $polyline,
            $color,
            $width,
            $height,
            $strokeWidth,
        );
    }

    public function staticMapMarker(
        float $latitude,
        float $longitude,
        string $markerType = 'city',
        string $color = '#FF0000',
        int $width = 600,
        int $height = 150,
    ): ?string {
        return $this->staticMapService->generateMarkerMap(
            $latitude,
            $longitude,
            $markerType,
            $color,
            $width,
            $height,
        );
    }

    public function getName(): string
    {
        return 'static_map_extension';
    }
}
