<?php

namespace Caldera\CriticalmassCoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

class RideRepository extends EntityRepository
{
	public function findCurrentRides()
	{
        $query = $this->getEntityManager()->createQuery('SELECT r AS ride FROM CalderaCriticalmassCoreBundle:Ride r WHERE r.visibleSince <= CURRENT_TIMESTAMP() AND r.visibleUntil >= CURRENT_TIMESTAMP() GROUP BY r.city ORDER BY r.dateTime DESC');
        //$query = $this->getEntityManager()->createQuery('SELECT r AS ride FROM CalderaCriticalmassCoreBundle:Ride r GROUP BY r.city ORDER BY r.dateTime DESC');

        return $query->getResult();
	}

    public function findLatestForCitySlug($citySlug)
    {
        $query = $this->getEntityManager()->createQuery('SELECT r AS ride FROM CalderaCriticalmassCoreBundle:Ride r JOIN CalderaCriticalmassCoreBundle:City c WITH c.id = r.city JOIN CalderaCriticalmassCoreBundle:CitySlug cs WITH cs.city = c.id WHERE cs.slug = \''.$citySlug.'\' GROUP BY r.city ORDER BY r.dateTime DESC');

        $result = $query->setMaxResults(1)->getResult();

        $result = array_pop($result);
        $result = array_pop($result);

        return $result;
    }

    public function findLatestRidesOrderByParticipants(\DateTime $startDateTime, \DateTime $endDateTime)
    {
        $query = $this->getEntityManager()->createQuery('SELECT r AS ride FROM CalderaCriticalmassCoreBundle:Ride r WHERE r.dateTime >= \''.$startDateTime->format('Y-m-d H:i:s').'\' AND r.dateTime <= \''.$endDateTime->format('Y-m-d H:i:s').'\' ORDER BY r.estimatedParticipants DESC');

        $result = $query->getResult();
        $result = array_pop($result);

        return $result;
    }
}

