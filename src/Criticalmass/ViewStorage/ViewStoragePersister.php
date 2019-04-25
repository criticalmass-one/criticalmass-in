<?php declare(strict_types=1);

namespace App\Criticalmass\ViewStorage;

use App\Entity\User;
use App\EntityInterface\ViewableInterface;
use App\EntityInterface\ViewInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Console\Output\OutputInterface;

class ViewStoragePersister implements ViewStoragePersisterInterface
{
    /** @var RegistryInterface $registry */
    protected $registry;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function persistViews(array $viewList): ViewStoragePersisterInterface
    {
        foreach ($viewList as $viewArray) {
            $this->storeView($viewArray);
        }

        $this->registry->getManager()->flush();

        return $this;
    }

    protected function storeView(array $viewArray): void
    {
        $view = $this->getView($viewArray['className']);
        $entity = $this->getEntity($viewArray['className'], $viewArray['entityId']);
        $viewSetEntityMethod = 'set' . $viewArray['className'];

        $view->$viewSetEntityMethod($entity);

        $userId = $viewArray['userId'];
        $user = null;

        if (is_int($userId)) {
            $user = $this->getUser($userId);
        }

        $view->setUser($user);

        $dateTime = new \DateTime($viewArray['dateTime']);
        $view->setDateTime($dateTime);

        $entity->incViews();

        $this->registry->getManager()->persist($view);
    }

    protected function getView(string $className): ViewInterface
    {
        $viewClassName = 'App\Entity\\' . $className . 'View';

        return new $viewClassName;
    }

    protected function getUser(int $userId): User
    {
        return $this->registry->getManager()->getRepository('App:User')->find($userId);
    }

    protected function getEntity(string $className, int $entityId): ViewableInterface
    {
        return $this->registry->getManager()->getRepository('App:' . $className)->find($entityId);
    }
}
