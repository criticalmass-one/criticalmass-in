<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Board;
use App\Entity\City;
use App\Entity\Thread;
use App\EntityInterface\BoardInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

class ThreadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Thread::class);
    }

    public function findThreadsForBoard(Board $board): array
    {
        return $this->queryThreadsForBoard($board)->getResult();
    }

    /**
     * Die Abfrage statt des Ergebnisses — der Paginator braucht sie, um selbst zu begrenzen.
     */
    public function queryThreadsForBoard(Board $board): Query
    {
        return $this->buildThreadQuery('t.board', $board);
    }

    public function findThreadsForCity(City $city): array
    {
        return $this->queryThreadsForCity($city)->getResult();
    }

    public function queryThreadsForCity(City $city): Query
    {
        return $this->buildThreadQuery('t.city', $city);
    }

    private function buildThreadQuery(string $field, Board|City $board): Query
    {
        $builder = $this->createQueryBuilder('t');

        $builder
            ->select('t')
            ->leftJoin('t.lastPost', 'lastPost')
            ->where($builder->expr()->eq($field, ':board'))
            ->setParameter('board', $board)
            ->andWhere($builder->expr()->eq('t.enabled', ':enabled'))
            ->setParameter('enabled', true)
            ->orderBy('t.sticky', 'DESC')
            ->addOrderBy('lastPost.dateTime', 'DESC');

        return $builder->getQuery();
    }

    /**
     * Das zuletzt aktive Thema eines Forums — Grundlage für die Anzeige „letzter Beitrag“,
     * wenn das bisherige lastThread verschoben oder deaktiviert wurde.
     */
    public function findLatestThread(BoardInterface $board, ?Thread $exclude = null): ?Thread
    {
        $builder = $this->createQueryBuilder('t');

        $builder
            ->select('t')
            ->leftJoin('t.lastPost', 'lastPost')
            ->where($builder->expr()->eq($board instanceof City ? 't.city' : 't.board', ':board'))
            ->setParameter('board', $board)
            ->andWhere($builder->expr()->eq('t.enabled', ':enabled'))
            ->setParameter('enabled', true)
            ->orderBy('lastPost.dateTime', 'DESC')
            ->setMaxResults(1);

        if (null !== $exclude && null !== $exclude->getId()) {
            // Der Aufrufer entfernt dieses Thema gerade. Bis zum flush() steht es noch
            // unveraendert in der Datenbank und waere sonst sein eigener Nachfolger.
            // Die Bedingung muss nach where() kommen -- where() ersetzt die Klausel.
            $builder
                ->andWhere($builder->expr()->neq('t.id', ':exclude'))
                ->setParameter('exclude', $exclude->getId());
        }

        return $builder->getQuery()->getOneOrNullResult();
    }

    public function findThreadBySlug(string $slug): ?Thread
    {
        $builder = $this->createQueryBuilder('t');

        $builder
            ->select('t')
            ->where($builder->expr()->eq('t.enabled', ':enabled'))
            ->setParameter('enabled', true)
            ->andWhere($builder->expr()->eq('t.slug', ':slug'))
            ->setParameter('slug', $slug);

        $query = $builder->getQuery();

        return $query->getSingleResult();
    }

    public function findForTimelineThreadCollector(?\DateTime $startDateTime = null, ?\DateTime $endDateTime = null, ?int $limit = null): array
    {
        $builder = $this->createQueryBuilder('t');

        $builder
            ->select('t')
            ->join('t.firstPost', 'firstPost')
            ->where($builder->expr()->eq('t.enabled', ':enabled'))
            ->setParameter('enabled', true)
            ->addOrderBy('firstPost.dateTime', 'DESC');

        if ($startDateTime) {
            $builder
                ->andWhere($builder->expr()->gte('firstPost.dateTime',':startDateTime'))
                ->setParameter('startDateTime', $startDateTime);
        }

        if ($endDateTime) {
            $builder
                ->andWhere($builder->expr()->lte('firstPost.dateTime', ':endDateTime'))
                ->setParameter('endDateTime', $endDateTime);
        }

        if ($limit) {
            $builder->setMaxResults($limit);
        }

        $query = $builder->getQuery();

        $result = $query->getResult();

        return $result;
    }
}

