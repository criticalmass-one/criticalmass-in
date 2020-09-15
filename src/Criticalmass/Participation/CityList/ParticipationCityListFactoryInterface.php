<?php declare(strict_types=1);

namespace App\Criticalmass\Participation\CityList;

use App\Entity\User;

interface ParticipationCityListFactoryInterface
{
    public function buildForUser(User $user): ParticipationCityListFactory;
    public function sort(): ParticipationCityListFactory;
    public function getParticipationCityList(): ParticipationCityList;
}
