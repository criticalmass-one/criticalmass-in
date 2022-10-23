<?php declare(strict_types=1);

namespace App\Criticalmass\Participation\CityList;

use App\Entity\Participation;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;

class ParticipationCityListFactory implements ParticipationCityListFactoryInterface
{
    /** @var ParticipationCityList $participationCityList */
    protected $participationCityList;

    public function __construct(protected ManagerRegistry $registry)
    {
    }

    public function buildForUser(User $user): ParticipationCityListFactory
    {
        $this->participationCityList = new ParticipationCityList();

        $participationList = $this->registry->getRepository(Participation::class)->findByUser($user);

        /** @var Participation $participation */
        foreach ($participationList as $participation) {
            $participationCityListItem = new ParticipationCityListItem($participation->getRide()->getCity());

            $this->participationCityList->addCityItem($participationCityListItem);
        }

        return $this;
    }

    public function sort(): ParticipationCityListFactory
    {
        $list = $this->participationCityList->getList();

        usort($list, function (ParticipationCityListItem $a, ParticipationCityListItem $b): int
        {
            if ($a->getCounter() === $b->getCounter()) {
                return $a->getCity()->getCity() < $b->getCity()->getCity() ? -1 : 1;
            }

            return $a->getCounter() < $b->getCounter() ? 1 : -1;
        });

        $this->participationCityList->setList($list);

        return $this;
    }

    public function getParticipationCityList(): ParticipationCityList
    {
        return $this->participationCityList;
    }
}
