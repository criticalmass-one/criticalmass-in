<?php

namespace Caldera\Bundle\CriticalmassModelBundle\Repository;

use Caldera\Bundle\CriticalmassModelBundle\Entity\City;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Event;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;
use Doctrine\ORM\EntityRepository;
use \Caldera\Bundle\CriticalmassModelBundle\Entity\Thread;

class PostRepository extends EntityRepository
{
    public function countPosts()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('COUNT(post.id)');
        $qb->from('CalderaCriticalmassModelBundle:Post', 'post');

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getPostsForRide(Ride $ride)
    {
        $builder = $this->createQueryBuilder('post');

        $builder->select('post');
        $builder->where($builder->expr()->eq('post.ride', $ride->getId()));
        $builder->andWhere($builder->expr()->eq('post.enabled', true));
        $builder->addOrderBy('post.dateTime', 'ASC');

        $query = $builder->getQuery();

        $result = $query->getResult();

        return $result;
    }

    public function countPostsForCityRides(City $city)
    {
        $builder = $this->createQueryBuilder('post');

        $builder->select('COUNT(post.id)');

        $builder->join('post.ride', 'ride');

        $builder->where($builder->expr()->eq('ride.city', $city->getId()));


        $query = $builder->getQuery();

        return $query->getSingleScalarResult();
    }

    public function getPostsForCityRides(City $city)
    {
        $builder = $this->createQueryBuilder('post');

        $builder->select('post');

        $builder->join('post.ride', 'ride');

        $builder->where($builder->expr()->eq('ride.city', $city->getId()));


        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findPostsForThread(Thread $thread)
    {
        $builder = $this->createQueryBuilder('post');

        $builder->select('post');

        $builder->where($builder->expr()->eq('post.thread', $thread->getId()));
        $builder->andWhere($builder->expr()->eq('post.enabled', 1));
        $builder->addOrderBy('post.dateTime', 'ASC');

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function countPostsForEvent(Event $event)
    {
        $builder = $this->createQueryBuilder('post');

        $builder->select('COUNT(post.id)');

        $builder->where($builder->expr()->eq('post.event', $event->getId()));
        $builder->andWhere($builder->expr()->eq('post.enabled', 1));

        $query = $builder->getQuery();

        return $query->getSingleScalarResult();
    }

    public function findRecentChatMessages($limit = 25)
    {
        $builder = $this->createQueryBuilder('post');

        $builder->select('post');

        $builder->where($builder->expr()->eq('post.enabled', 1));
        $builder->andWhere($builder->expr()->eq('post.chat', 1));

        if ($limit) {
            $builder->setMaxResults($limit);
        }

        $builder->addOrderBy('post.dateTime', 'DESC');

        $query = $builder->getQuery();

        $result = $query->getResult();

        return $result;
    }

    public function findForTimelineThreadPostCollector(\DateTime $startDateTime = null, \DateTime $endDateTime = null, $limit = null)
    {
        $builder = $this->createQueryBuilder('post');

        $builder->select('post');

        $builder->join('post.thread', 'thread');

        $builder->where($builder->expr()->eq('post.enabled', 1));
        $builder->andWhere($builder->expr()->isNotNull('post.thread'));
        $builder->andWhere($builder->expr()->neq('post', 'thread.firstPost'));

        if ($startDateTime) {
            $builder->andWhere($builder->expr()->gte('post.dateTime', '\''.$startDateTime->format('Y-m-d H:i:s').'\''));
        }

        if ($endDateTime) {
            $builder->andWhere($builder->expr()->lte('post.dateTime', '\''.$endDateTime->format('Y-m-d H:i:s').'\''));
        }

        if ($limit) {
            $builder->setMaxResults($limit);
        }

        $builder->addOrderBy('post.dateTime', 'DESC');

        $query = $builder->getQuery();

        $result = $query->getResult();

        return $result;
    }

    public function findForTimelineContentCommentCollector(\DateTime $startDateTime = null, \DateTime $endDateTime = null, $limit = null)
    {
        $builder = $this->createQueryBuilder('post');

        $builder->select('post');
        
        $builder->where($builder->expr()->eq('post.enabled', 1));
        $builder->andWhere($builder->expr()->isNotNull('post.content'));

        if ($startDateTime) {
            $builder->andWhere($builder->expr()->gte('post.dateTime', '\''.$startDateTime->format('Y-m-d H:i:s').'\''));
        }

        if ($endDateTime) {
            $builder->andWhere($builder->expr()->lte('post.dateTime', '\''.$endDateTime->format('Y-m-d H:i:s').'\''));
        }

        if ($limit) {
            $builder->setMaxResults($limit);
        }

        $builder->addOrderBy('post.dateTime', 'DESC');

        $query = $builder->getQuery();

        $result = $query->getResult();

        return $result;
    }
}

