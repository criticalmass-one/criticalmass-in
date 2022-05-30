<?php declare(strict_types=1);

namespace App\Twig\Extension;

use App\Criticalmass\Geocoding\ReverseGeocodeable;
use App\Criticalmass\Geocoding\ReverseGeocoderInterface;
use Geocoder\Location;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class GeocodeTwigExtension extends AbstractExtension
{
    protected ReverseGeocoderInterface $geocoder;

    public function __construct(ReverseGeocoderInterface $geocoder)
    {
        $this->geocoder = $geocoder;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('nominatim_location', [$this, 'nominatimLocation'], [
                'is_safe' => ['html'],
            ]),
        ];
    }

    public function nominatimLocation(ReverseGeocodeable $geocodeable): ?Location
    {
        return $this->geocoder->query($geocodeable);
    }

    public function getName(): string
    {
        return 'geocode_extension';
    }
}

