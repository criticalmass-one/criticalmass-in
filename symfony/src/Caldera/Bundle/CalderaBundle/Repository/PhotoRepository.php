<?php

namespace Caldera\Bundle\CalderaBundle\Repository;

use Caldera\Bundle\CalderaBundle\Entity\City;
use Caldera\Bundle\CalderaBundle\Entity\Event;
use Caldera\Bundle\CalderaBundle\Entity\Photo;
use Caldera\Bundle\CalderaBundle\Entity\Ride;
use Caldera\Bundle\CalderaBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

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

        if ($photo->getRide()) {
            $builder->where($builder->expr()->eq('photo.ride', $photo->getRide()->getId()));
        } else {
            $builder->where($builder->expr()->eq('photo.event', $photo->getEvent()->getId()));
        }

        $builder->andWhere($builder->expr()->lt('photo.dateTime', '\'' . $photo->getDateTime()->format('Y-m-d H:i:s') . '\''));
        $builder->andWhere($builder->expr()->eq('photo.enabled', 1));
        $builder->andWhere($builder->expr()->eq('photo.deleted', 0));

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

        if ($photo->getRide()) {
            $builder->where($builder->expr()->eq('photo.ride', $photo->getRide()->getId()));
        } else {
            $builder->where($builder->expr()->eq('photo.event', $photo->getEvent()->getId()));
        }

        $builder->andWhere($builder->expr()->gt('photo.dateTime', '\'' . $photo->getDateTime()->format('Y-m-d H:i:s') . '\''));
        $builder->andWhere($builder->expr()->eq('photo.enabled', 1));
        $builder->andWhere($builder->expr()->eq('photo.deleted', 0));

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

        $builder->where($builder->expr()->eq('photo.deleted', 0));

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
        foreach ($result as $row) {
            /**
             * @var Ride $ride
             */
            $ride = $row[0]->getRide();
            $key = $ride->getFormattedDate() . '_' . $ride->getId();

            $rides[$key] = $ride;
            $counter[$key] = $row[1];
        }

        return [
            'rides' => $rides,
            'counter' => $counter
        ];
    }

    public function findRidesForGallery(City $city = null)
    {
        $builder = $this->createQueryBuilder('photo');

        $builder->select('photo');
        $builder->addSelect('ride');
        $builder->addSelect('city');
        $builder->addSelect('COUNT(photo)');
        $builder->addSelect('featuredPhoto');

        $builder->where($builder->expr()->eq('photo.deleted', 0));

        if ($city) {
            $builder->andWhere($builder->expr()->eq('photo.city', $city->getId()));
        }

        $builder->join('photo.ride', 'ride');
        $builder->join('ride.city', 'city');
        $builder->leftJoin('ride.featuredPhoto', 'featuredPhoto');

        $builder->orderBy('ride.dateTime', 'desc');

        $builder->groupBy('ride');

        $query = $builder->getQuery();
        $result = $query->getResult();

        $galleryResult = [];

        foreach ($result as $row) {
            $ride = $row[0]->getRide();
            $counter = $row[1];

            $key = $ride->getFormattedDate() . '_' . $ride->getId();

            $galleryResult[$key]['ride'] = $ride;
            $galleryResult[$key]['counter'] = $counter;
        }

        return $galleryResult;
    }

    /**
     * @param City|null $city
     * @return array
     * @deprecated
     */
    public function findRidesWithPhotoCounter(City $city = null)
    {
        $builder = $this->createQueryBuilder('photo');

        $builder->select('photo');
        $builder->addSelect('COUNT(photo)');

        $builder->where($builder->expr()->eq('photo.deleted', 0));

        if ($city) {
            $builder->andWhere($builder->expr()->eq('photo.city', $city->getId()));
        }

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
        foreach ($result as $row) {
            /**
             * @var Ride $ride
             */
            $ride = $row[0]->getRide();
            $key = $ride->getFormattedDate() . '_' . $ride->getId();

            $rides[$key] = $ride;
            $counter[$key] = $row[1];
        }

        return [
            'rides' => $rides,
            'counter' => $counter
        ];
    }


    public function buildQueryPhotosByRide(Ride $ride)
    {
        $builder = $this->createQueryBuilder('photo');

        $builder->select('photo');

        $builder->where($builder->expr()->eq('photo.ride', $ride->getId()));
        $builder->andWhere($builder->expr()->eq('photo.deleted', 0));

        $builder->addOrderBy('photo.dateTime', 'ASC');

        return $builder->getQuery();
    }

    public function findPhotosByRide(Ride $ride)
    {
        $query = $this->buildQueryPhotosByRide($ride);

        return $query->getResult();
    }

    public function buildQueryPhotosByEvent(Event $event)
    {
        $builder = $this->createQueryBuilder('photo');

        $builder->select('photo');

        $builder->where($builder->expr()->eq('photo.event', $event->getId()));
        $builder->andWhere($builder->expr()->eq('photo.deleted', 0));

        $builder->addOrderBy('photo.dateTime', 'ASC');

        return $builder->getQuery();
    }

    public function countPhotosByEvent(Event $event)
    {
        $builder = $this->createQueryBuilder('photo');

        $builder->select('COUNT(photo)');

        $builder->where($builder->expr()->eq('photo.event', $event->getId()));
        $builder->andWhere($builder->expr()->eq('photo.enabled', 1));
        $builder->andWhere($builder->expr()->eq('photo.deleted', 0));

        $query = $builder->getQuery();

        return $query->getSingleScalarResult();
    }

    public function buildQueryPhotosByUserAndRide(User $user, Ride $ride)
    {
        $builder = $this->createQueryBuilder('photo');

        $builder->select('photo');

        $builder->where($builder->expr()->eq('photo.ride', $ride->getId()));
        $builder->andWhere($builder->expr()->eq('photo.user', $user->getId()));
        $builder->andWhere($builder->expr()->eq('photo.deleted', 0));

        $builder->addOrderBy('photo.dateTime', 'ASC');

        return $builder->getQuery();
    }

    public function findPhotosByUserAndRide(User $user, Ride $ride)
    {
        $query = $this->buildQueryPhotosByUserAndRide($user, $ride);

        return $query->getResult();
    }

    public function findSomePhotos($limit = 16, $maxViews = 15, City $city = null)
    {
        $builder = $this->createQueryBuilder('photo');

        $builder->select('photo');
        $builder->addSelect('RAND() as HIDDEN rand');

        $builder->where($builder->expr()->eq('photo.enabled', 1));
        $builder->andWhere($builder->expr()->isNotNull('photo.ride'));
        $builder->andWhere($builder->expr()->eq('photo.deleted', 0));

        if ($maxViews) {
            $builder->andWhere($builder->expr()->lte('photo.views', $maxViews));
        }

        if ($city) {
            $builder->andWhere($builder->expr()->eq('photo.city', $city->getId()));
        }

        $builder->addOrderBy('rand');

        $builder->setMaxResults($limit);

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findForTimelinePhotoCollector(\DateTime $startDateTime = null, \DateTime $endDateTime = null, $limit = null)
    {
        $builder = $this->createQueryBuilder('photo');

        $builder->select('photo');

        $builder->where($builder->expr()->eq('photo.enabled', 1));
        $builder->andWhere($builder->expr()->isNotNull('photo.ride'));
        $builder->andWhere($builder->expr()->eq('photo.deleted', 0));

        if ($startDateTime) {
            $builder->andWhere($builder->expr()->gte('photo.creationDateTime', '\'' . $startDateTime->format('Y-m-d H:i:s') . '\''));
        }

        if ($endDateTime) {
            $builder->andWhere($builder->expr()->lte('photo.creationDateTime', '\'' . $endDateTime->format('Y-m-d H:i:s') . '\''));
        }

        if ($limit) {
            $builder->setMaxResults($limit);
        }

        $builder->addOrderBy('photo.creationDateTime', 'DESC');

        $query = $builder->getQuery();

        return $query->getResult();
    }
}

