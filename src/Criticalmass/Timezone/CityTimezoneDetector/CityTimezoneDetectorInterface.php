<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Timezone\CityTimezoneDetector;

use AppBundle\Entity\City;

interface CityTimezoneDetectorInterface
{
    public function queryForCity(City $city): ?string;
}
