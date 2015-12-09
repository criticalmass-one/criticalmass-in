<?php

namespace Caldera\Bundle\CriticalmassModelBundle\Repository;

use Caldera\Bundle\CriticalmassModelBundle\Entity\Photo;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;
use Doctrine\ORM\EntityRepository;
use FOS\UserBundle\Entity\User;

class PhotoRepository extends EntityRepository
{
    /**
     * @param Photo $photo
     * @return Photo
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @author maltehuebner
     * @since 2015-12-06
     */
    public function getPreviousPhoto(Photo $photo)
    {
        $builder = $this->createQueryBuilder('photo');

        $builder->select('photo');
        $builder->where($builder->expr()->eq('photo.ride', $photo->getId()));
        $builder->where($builder->expr()->lt('photo.dateTime', '\''.$photo->getDateTime()->format('Y-m-d H:i:s').'\''));
        $builder->addOrderBy('photo.dateTime', 'DESC');
        $builder->setMaxResults(1);

        $query = $builder->getQuery();

        $result = $query->getOneOrNullResult();

        return $result;
    }

    /**
     * @param Photo $photo
     * @return Photo
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @author maltehuebner
     * @since 2015-12-06
     */
    public function getNextPhoto(Photo $photo)
    {
        $builder = $this->createQueryBuilder('photo');

        $builder->select('photo');
        $builder->where($builder->expr()->eq('photo.ride', $photo->getId()));
        $builder->where($builder->expr()->gt('photo.dateTime', '\''.$photo->getDateTime()->format('Y-m-d H:i:s').'\''));
        $builder->addOrderBy('photo.dateTime', 'ASC');
        $builder->setMaxResults(1);

        $query = $builder->getQuery();

        $result = $query->getOneOrNullResult();

        return $result;
    }


    public function findRidesWithPhotoCounterByUser(User $user)
    {
        $builder = $this->createQueryBuilder('photo');

        $builder->select('photo');
        $builder->addSelect('COUNT(photo)');

        $builder->groupBy('photo.ride');

        $builder->join('photo.ride', 'ride');
        $builder->orderBy('ride.dateTime', 'desc');

        $query = $builder->getQuery();
        $result = $query->getResult();

        $rides = array();
        $counter = array();

        /**
         * @var Photo $photo
         */
        foreach ($result as $row)
        {
            /**
             * @var Ride $ride
             */
            $ride = $row[0]->getRide();
            $key = $ride->getFormattedDate().'_'.$ride->getId();

            $rides[$key] = $ride;
            $counter[$key] = $row[1];
        }

        return [
            'rides' => $rides,
            'counter' => $counter
            ];
    }

    public function findPhotosByRide(Ride $ride)
    {
        $builder = $this->createQueryBuilder('photo');
        
        $builder->select('photo');

        $builder->where($builder->expr()->eq('photo.ride', $ride->getId()));
        $builder->andWhere($builder->expr()->eq('photo.enabled', true));
        
        $builder->addOrderBy('photo.dateTime', 'ASC');
        
        $query = $builder->getQuery();
        
        $result = $query->getResult();
        
        return $result;
    }

    public function findPhotosByUserAndRide(User $user, Ride $ride)
    {
        $builder = $this->createQueryBuilder('photo');

        $builder->select('photo');

        $builder->where($builder->expr()->eq('photo.ride', $ride->getId()));
        $builder->andWhere($builder->expr()->eq('photo.user', $user->getId()));
        //$builder->andWhere($builder->expr()->eq('photo.enabled', true));

        $builder->addOrderBy('photo.dateTime', 'ASC');

        $query = $builder->getQuery();

        $result = $query->getResult();

        return $result;
    }
}

