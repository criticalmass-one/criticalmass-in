<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Board;
use App\Entity\City;
use App\Entity\ForumSubscription;
use App\Entity\Thread;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ForumSubscription>
 */
class ForumSubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ForumSubscription::class);
    }

    /**
     * Hat dieser Nutzer genau diese Ebene schon abonniert? Ein eindeutiger Index kann
     * das nicht leisten, weil MySQL NULL-Werte darin nicht als gleich behandelt.
     */
    public function findExisting(User $user, ?Thread $thread, ?Board $board, ?City $city, bool $globalScope): ?ForumSubscription
    {
        $builder = $this->createQueryBuilder('s');

        $builder
            ->where($builder->expr()->eq('s.user', ':user'))
            ->setParameter('user', $user)
            ->andWhere($builder->expr()->eq('s.globalScope', ':globalScope'))
            ->setParameter('globalScope', $globalScope);

        foreach (['thread' => $thread, 'board' => $board, 'city' => $city] as $field => $value) {
            if (null === $value) {
                $builder->andWhere($builder->expr()->isNull('s.' . $field));
            } else {
                $builder
                    ->andWhere($builder->expr()->eq('s.' . $field, ':' . $field))
                    ->setParameter($field, $value);
            }
        }

        return $builder->getQuery()->setMaxResults(1)->getOneOrNullResult();
    }

    /**
     * Alle Abonnements eines Nutzers, für die Übersicht in der Kontoverwaltung.
     *
     * @return list<ForumSubscription>
     */
    public function findForUser(User $user): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.user = :user')
            ->setParameter('user', $user)
            ->orderBy('s.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Wer soll über einen neuen Beitrag in diesem Thema erfahren?
     *
     * Greift ein Nutzer über mehrere Ebenen zugleich — etwa Thema und ganzes Forum —,
     * steht er trotzdem nur einmal in der Liste.
     *
     * @return list<User>
     */
    public function findSubscribersForThread(Thread $thread): array
    {
        // Abgefragt wird von User aus, nicht vom Abonnement: Doctrine laesst kein
        // "SELECT DISTINCT u" zu, wenn u nur ein Join-Alias ist -- die Auswahl muss
        // mindestens einen Wurzel-Alias enthalten.
        $builder = $this->getEntityManager()->createQueryBuilder();

        $builder
            ->select('DISTINCT u')
            ->from(User::class, 'u')
            ->innerJoin(ForumSubscription::class, 's', Join::WITH, 's.user = u');

        $conditions = [
            $builder->expr()->eq('s.globalScope', ':globalScope'),
            $builder->expr()->eq('s.thread', ':thread'),
        ];

        $builder
            ->setParameter('globalScope', true)
            ->setParameter('thread', $thread);

        if (null !== $thread->getBoard()) {
            $conditions[] = $builder->expr()->eq('s.board', ':board');
            $builder->setParameter('board', $thread->getBoard());
        }

        if (null !== $thread->getCity()) {
            $conditions[] = $builder->expr()->eq('s.city', ':city');
            $builder->setParameter('city', $thread->getCity());
        }

        $builder->where($builder->expr()->orX(...$conditions));

        return $builder->getQuery()->getResult();
    }
}
