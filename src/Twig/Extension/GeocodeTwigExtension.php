<?php declare(strict_types=1);

namespace App\Twig\Extension;

use App\Criticalmass\Geocoding\ReverseGeocodeable;
use App\Criticalmass\Geocoding\ReverseGeocoderInterface;
use Geocoder\Location;

class GeocodeTwigExtension extends \Twig_Extension
{
    /** @var ReverseGeocoderInterface $geocoder */
    protected $geocoder;

    public function __construct(ReverseGeocoderInterface $geocoder)
    {
        $this->geocoder = $geocoder;
    }

    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('nominatim_location', [$this, 'nominatimLocation'], [
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

