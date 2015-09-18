<?php

namespace Caldera\Bundle\CriticalmassModelBundle\Repository;

use Caldera\Bundle\CriticalmassModelBundle\Entity\Track;
use Doctrine\ORM\EntityRepository;

/**
 * Class TrackRepository
 * 
 * Reposity for Track entites.
 *
 * @package Caldera\Bundle\CriticalmassModelBundle\Repository
 * @author maltehuebner
 * @since 2015-09-18
 */
class TrackRepository extends EntityRepository
{
    /**
     * Get the previous track of the parameterized track. Only collects tracks of the same user and sorts them by the
     * datetime of the ride.
     * 
     * @param Track $track
     * @return Track
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @author maltehuebner
     * @since 2015-09-18
     */
    public function getPreviousTrack(Track $track)
    {
        $builder = $this->createQueryBuilder('track');

        $builder->select('track');
        $builder->join('track.ride', 'ride');
        $builder->where($builder->expr()->lt('ride.dateTime', '\''.$track->getRide()->getDateTime()->format('Y-m-d H:i:s').'\''));
        $builder->andWhere($builder->expr()->eq('track.user', $track->getUser()->getId()));
        $builder->addOrderBy('track.startDateTime', 'DESC');
        $builder->setMaxResults(1);

        $query = $builder->getQuery();

        $result = $query->getOneOrNullResult();

        return $result;
    }

    /**
     * Get the next track of the parameterized track. Only collects tracks of the same user and sorts them by the
     * datetime of the ride.
     *
     * @param Track $track
     * @return Track
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @author maltehuebner
     * @since 2015-09-18
     */
    public function getNextTrack(Track $track)
    {
        $builder = $this->createQueryBuilder('track');

        $builder->select('track');
        $builder->join('track.ride', 'ride');
        $builder->where($builder->expr()->gt('ride.dateTime', '\''.$track->getRide()->getDateTime()->format('Y-m-d H:i:s').'\''));
        $builder->andWhere($builder->expr()->eq('track.user', $track->getUser()->getId()));
        $builder->addOrderBy('track.startDateTime', 'ASC');
        $builder->setMaxResults(1);

        $query = $builder->getQuery();

        $result = $query->getOneOrNullResult();

        return $result;
    }
}

