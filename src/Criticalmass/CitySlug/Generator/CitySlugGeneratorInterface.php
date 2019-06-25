<?php declare(strict_types=1);

namespace App\Criticalmass\CitySlug\Generator;

use App\Entity\City;
use App\Entity\CitySlug;

interface CitySlugGeneratorInterface
{
    public function generate(City $city): CitySlug;
}