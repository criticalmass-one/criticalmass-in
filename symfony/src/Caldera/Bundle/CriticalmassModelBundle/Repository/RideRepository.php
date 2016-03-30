<?php

namespace Caldera\Bundle\CriticalmassModelBundle\Repository;

use Caldera\Bundle\CriticalmassModelBundle\Entity\City;
use Caldera\Bundle\CriticalmassModelBundle\Entity\CriticalmapsUser;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Location;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;
use Doctrine\ORM\EntityRepository;

class RideRepository extends EntityRepository
{
    public function findCurrentRideForCity(City $city)
    {
        $dateTime = new \DateTime();

        $builder = $this->createQueryBuilder('ride');

        $builder->select('ride');

        $builder->where($builder->expr()->gte('ride.dateTime', '\''.$dateTime->format('Y-m-d h:i:s').'\''));
        $builder->andWhere($builder->expr()->eq('ride.city', $city->getId()));

        $builder->addOrderBy('ride.dateTime', 'DESC');

        $query = $builder->getQuery();
        $query->setMaxResults(1);

        $result = $query->getOneOrNullResult();

        return $result;
    }

    public function findRidesForCity(City $city, $order = 'DESC')
    {
        $builder = $this->createQueryBuilder('ride');

        $builder->select('ride');

        $builder->where($builder->expr()->eq('ride.city', $city->getId()));
        $builder->andWhere($builder->expr()->eq('ride.isArchived', 0));
        $builder->addOrderBy('ride.dateTime', $order);

        $query = $builder->getQuery();

        $result = $query->getResult();

        return $result;
    }

    public function findRecentRides($year = null, $month = null, $maxResults = null, $minParticipants = 0, $postShuffle = false)
    {
        $builder = $this->createQueryBuilder('ride');
        
        $builder->select('ride');
        
        $builder->where($builder->expr()->gte('ride.estimatedParticipants', $minParticipants));
        
        if ($month && $year) {
            $builder->andWhere($builder->expr()->eq('MONTH(ride.dateTime)', $month));
            $builder->andWhere($builder->expr()->eq('YEAR(ride.dateTime)', $month));
        }
        
        $builder->addOrderBy('ride.dateTime', 'DESC');
        
        $query = $builder->getQuery();
        
        if ($maxResults) {
            $query->setMaxResults($maxResults);
        }
        
        $result = $query->getResult();
        
        if ($postShuffle) {
            $result = array_rand($result);
        }
        
        return $result;
    }
    
    public function findRideByLatitudeLongitudeDateTime($latitude, $longitude, \DateTime $dateTime)
    {
        $queryString = 'SELECT r AS ride, SQRT((r.latitude - '.$latitude.') * (r.latitude - '.$latitude.') + (r.longitude - '.$longitude.') * (r.longitude - '.$longitude.')) AS distance FROM CalderaCriticalmassModelBundle:Ride r JOIN r.city c WHERE c.enabled = 1 AND DATE(r.dateTime) = \''.$dateTime->format('Y-m-d').'\' ORDER BY distance ASC';

        $query = $this->getEntityManager()->createQuery($queryString);
        $query->setMaxResults(1);
        $result = $query->getOneOrNullResult();

        if ($result) {
            return $result['ride'];
        }

        return null;
    }

    public function findCityRideByDate(City $city, \DateTime $dateTime)
    {
        $query = $this->getEntityManager()->createQuery('SELECT r AS ride FROM CalderaCriticalmassModelBundle:Ride r WHERE DATE(r.dateTime) = \''.$dateTime->format('Y-m-d').'\' AND r.city = '.$city->getId())->setMaxResults(1);

        $result = $query->getResult();

        $result = @array_pop($result);
        $result = @array_pop($result);

        return $result;
    }

    /**
     * Fetches all rides in a datetime range of three weeks before and three days after.
     *
     * @return array
     */
    public function findCurrentRides($order = 'ASC')
    {
        $startDateTime = new \DateTime();
        $startDateTimeInterval = new \DateInterval('P4W'); // four weeks ago
        $startDateTime->add($startDateTimeInterval);

        $endDateTime = new \DateTime();
        $endDateTimeInterval = new \DateInterval('P1W'); // one week after
        $endDateTime->sub($endDateTimeInterval);

        $builder = $this->createQueryBuilder('ride');

        $builder->select('ride');
        $builder->where($builder->expr()->lte('ride.dateTime', '\''.$startDateTime->format('Y-m-d H:i:s').'\''));
        $builder->andWhere($builder->expr()->gte('ride.dateTime', '\''.$endDateTime->format('Y-m-d H:i:s').'\''));

        $builder->andWhere($builder->expr()->eq('ride.isArchived', '0'));

        $builder->orderBy('ride.dateTime', $order);

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findByCityAndMonth(City $city, \DateTime $dateTime)
    {
        $startDateTime = new \DateTime($dateTime->format('Y').'-'.$dateTime->format('m').'-01 00:00:00');
        $endDateTime = new \DateTime($dateTime->format('Y').'-'.$dateTime->format('m').'-'.$dateTime->format('t').' 23:59:59');

        $builder = $this->createQueryBuilder('ride');

        $builder->select('ride');
        $builder->where($builder->expr()->gte('ride.dateTime', '\''.$startDateTime->format('Y-m-d H:i:s').'\''));
        $builder->andWhere($builder->expr()->lte('ride.dateTime', '\''.$endDateTime->format('Y-m-d H:i:s').'\''));

        $builder->andWhere($builder->expr()->eq('ride.city', $city->getId()));

        $builder->andWhere($builder->expr()->eq('ride.isArchived', '0'));

        $query = $builder->getQuery();
        
        return $query->getResult();
    }

    public function findFrontpageRides($order = 'ASC')
    {
        $startDateTime = new \DateTime();
        $startDateTimeInterval = new \DateInterval('P8W');
        $startDateTime->add($startDateTimeInterval);

        $endDateTime = new \DateTime();
        $endDateTimeInterval = new \DateInterval('P1D');
        $endDateTime->sub($endDateTimeInterval);

        $builder = $this->createQueryBuilder('ride');

        $builder->select('ride');
        $builder->where($builder->expr()->lte('ride.dateTime', '\''.$startDateTime->format('Y-m-d H:i:s').'\''));
        $builder->andWhere($builder->expr()->gte('ride.dateTime', '\''.$endDateTime->format('Y-m-d H:i:s').'\''));

        $builder->andWhere($builder->expr()->eq('ride.isArchived', '0'));

        $builder->orderBy('ride.dateTime', $order);

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findEstimatedRides()
    {
        $builder = $this->createQueryBuilder('ride');

        $builder->select('ride');
        $builder->where($builder->expr()->gt('ride.estimatedParticipants', 0));
        $builder->andWhere($builder->expr()->gt('ride.estimatedDuration', 0));
        $builder->andWhere($builder->expr()->gt('ride.estimatedDuration', 0));

        $builder->andWhere($builder->expr()->eq('ride.isArchived', 0));

        $builder->orderBy('ride.dateTime', 'DESC');

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findRidesInInterval(\DateTime $startDateTime = null, \DateTime $endDateTime = null)
    {
        if (!$startDateTime) {
            $startDateTime = new \DateTime();
        }

        if (!$endDateTime) {
            $endDate = new \DateTime();
            $dayInterval = new \DateInterval('P1M');
            $endDateTime = $endDate->add($dayInterval);
        }

        $builder = $this->createQueryBuilder('ride');

        $builder->select('ride');
        $builder->where($builder->expr()->gt('ride.dateTime', '\''.$startDateTime->format('Y-m-d').'\''));
        $builder->andWhere($builder->expr()->lt('ride.dateTime', '\''.$endDateTime->format('Y-m-d').'\''));

        $builder->andWhere($builder->expr()->eq('ride.isArchived', 0));

        $builder->orderBy('ride.dateTime', 'ASC');

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findRidesByYearMonth($year, $month)
    {
        $startDateTime = new \DateTime($year.'-'.$month.'-01');
        $endDateTime = new \DateTime($year.'-'.$month.'-'.$startDateTime->format('t'));

        return $this->findRidesInInterval($startDateTime, $endDateTime);
    }

    public function findRidesByDateTimeMonth(\DateTime $dateTime)
    {
        $startDateTime = $dateTime;
        $endDateTime = new \DateTime($startDateTime->format('Y-m-t'));

        return $this->findRidesInInterval($startDateTime, $endDateTime);
    }

    public function findLatestForCitySlug($citySlug)
    {
        $query = $this->getEntityManager()->createQuery('SELECT r AS ride FROM CalderaCriticalmassModelBundle:Ride r JOIN CalderaCriticalmassModelBundle:City c WITH c.id = r.city JOIN CalderaCriticalmassModelBundle:CitySlug cs WITH cs.city = c.id WHERE cs.slug = \''.$citySlug.'\' GROUP BY r.city ORDER BY r.dateTime DESC');

        $result = $query->setMaxResults(1)->getResult();

        $result = array_pop($result);
        $result = array_pop($result);

        return $result;
    }

    /**
     * @param $citySlug string
     * @param $rideDate string
     * @return Ride
     */
    public function findByCitySlugAndRideDate($citySlug, $rideDate)
    {
        // Maybe this datetime computation stuff is stupid. Will look for a better solution.
        $fromDateTime = new \DateTime($rideDate);
        $fromDateTime->setTime(0, 0, 0);

        $untilDateTime = new \DateTime($rideDate);
        $untilDateTime->setTime(23, 59, 59);

        $builder = $this->createQueryBuilder('ride');

        $builder->select('ride');
        $builder->join('ride.city', 'city');
        $builder->join('city.slugs', 'citySlug');

        $builder->where($builder->expr()->eq('citySlug.slug', '\''.$citySlug.'\''));
        $builder->andWhere($builder->expr()->gt('ride.dateTime', '\''.$fromDateTime->format('Y-m-d H:i:s').'\''));
        $builder->andWhere($builder->expr()->lt('ride.dateTime', '\''.$untilDateTime->format('Y-m-d H:i:s').'\''));

        $builder->andWhere($builder->expr()->eq('ride.isArchived', 0));

        $query = $builder->getQuery();

        return $query->getOneOrNullResult();
    }

    public function findByCityAndRideDate(City $city, $rideDate)
    {
        // Maybe this datetime computation stuff is stupid. Will look for a better solution.
        $fromDateTime = new \DateTime($rideDate);
        $fromDateTime->setTime(0, 0, 0);

        $untilDateTime = new \DateTime($rideDate);
        $untilDateTime->setTime(23, 59, 59);

        $builder = $this->createQueryBuilder('ride');

        $builder->select('ride');

        $builder->where($builder->expr()->eq('ride.city', $city->getId()));
        $builder->andWhere($builder->expr()->gt('ride.dateTime', '\''.$fromDateTime->format('Y-m-d H:i:s').'\''));
        $builder->andWhere($builder->expr()->lt('ride.dateTime', '\''.$untilDateTime->format('Y-m-d H:i:s').'\''));

        $builder->andWhere($builder->expr()->eq('ride.isArchived', 0));

        $builder->setMaxResults(1);

        $query = $builder->getQuery();

        return $query->getOneOrNullResult();
    }

    public function findLatestRidesOrderByParticipants(\DateTime $startDateTime, \DateTime $endDateTime)
    {
        $query = $this->getEntityManager()->createQuery('SELECT r AS ride FROM CalderaCriticalmassModelBundle:Ride r WHERE r.dateTime >= \''.$startDateTime->format('Y-m-d H:i:s').'\' AND r.dateTime <= \''.$endDateTime->format('Y-m-d H:i:s').'\' ORDER BY r.estimatedParticipants DESC');

        $result = array();

        $tmp1 = $query->getResult();

        foreach ($tmp1 as $tmp2)
        {
            foreach ($tmp2 as $ride)
            {
                $result[$ride->getCity()->getMainSlugString()] = $ride;
            }
        }
        return $result;
    }

    /**
     * This returns a previous ride entity with at least one subride.
     *
     * @param Ride $ride
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @author maltehuebner
     * @since 2016-02-01
     */
    public function getPreviousRideWithSubrides(Ride $ride)
    {
        $builder = $this->createQueryBuilder('ride');

        $builder->select('ride');

        $builder->join('ride.subrides', 'subrides');

        $builder->where($builder->expr()->lt('ride.dateTime', '\''.$ride->getDateTime()->format('Y-m-d H:i:s').'\''));
        $builder->andWhere($builder->expr()->eq('ride.city', $ride->getCity()->getId()));
        $builder->addOrderBy('ride.dateTime', 'DESC');
        $builder->setMaxResults(1);

        $query = $builder->getQuery();

        $result = $query->getOneOrNullResult();

        return $result;
    }

    /**
     * @param Ride $ride
     * @return Ride
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @author maltehuebner
     * @since 2015-09-18
     */
    public function getPreviousRide(Ride $ride)
    {
        $builder = $this->createQueryBuilder('ride');

        $builder->select('ride');
        $builder->where($builder->expr()->lt('ride.dateTime', '\''.$ride->getDateTime()->format('Y-m-d H:i:s').'\''));
        $builder->andWhere($builder->expr()->eq('ride.city', $ride->getCity()->getId()));
        $builder->addOrderBy('ride.dateTime', 'DESC');
        $builder->setMaxResults(1);

        $query = $builder->getQuery();

        $result = $query->getOneOrNullResult();

        return $result;
    }

    /**
     * @param Ride $ride
     * @return Ride
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @author maltehuebner
     * @since 2015-09-18
     */
    public function getNextRide(Ride $ride)
    {
        $builder = $this->createQueryBuilder('ride');

        $builder->select('ride');
        $builder->where($builder->expr()->gt('ride.dateTime', '\''.$ride->getDateTime()->format('Y-m-d H:i:s').'\''));
        $builder->andWhere($builder->expr()->eq('ride.city', $ride->getCity()->getId()));
        $builder->addOrderBy('ride.dateTime', 'ASC');
        $builder->setMaxResults(1);

        $query = $builder->getQuery();

        $result = $query->getOneOrNullResult();

        return $result;
    }

    public function getLocationsForCity(City $city)
    {
        $builder = $this->createQueryBuilder('ride');

        $builder->select(
            [
                'ride.location',
                'ride.latitude',
                'ride.longitude'
            ]
        );
        $builder->where($builder->expr()->eq('ride.city', $city->getId()));
        $builder->andWhere($builder->expr()->isNotNull('ride.location'));
        $builder->andWhere($builder->expr()->eq('ride.isArchived', 0));

        $builder->orderBy('ride.location', 'ASC');
        $builder->groupBy('ride.location');

        $query = $builder->getQuery();

        $result = $query->getResult();

        return $result;
    }

    public function countRidesByLocation(Location $location)
    {
        $builder = $this->createQueryBuilder('ride');

        $builder->select('COUNT(ride)');

        $builder->where($builder->expr()->like('ride.location', '\''.$location->getTitle().'\''));
        $builder->andWhere($builder->expr()->eq('ride.isArchived', 0));

        $query = $builder->getQuery();

        return $query->getResult();
    }


    public function findRidesByLocation(Location $location)
    {
        $builder = $this->createQueryBuilder('ride');

        $builder->select('ride');

        $builder->where($builder->expr()->like('ride.location', '\''.$location->getTitle().'\''));
        $builder->andWhere($builder->expr()->eq('ride.isArchived', 0));

        $builder->addOrderBy('ride.dateTime', 'DESC');

        $query = $builder->getQuery();

        return $query->getResult();
    }
}

