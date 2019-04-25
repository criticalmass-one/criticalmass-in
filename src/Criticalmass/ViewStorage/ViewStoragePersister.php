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

    protected function storeView(View $view): void
    {
        $viewEntity = $this->getView($view->getEntityClassName());
        $entity = $this->getEntity($view->getEntityClassName(), $view->getEntityId());
        $viewSetEntityMethod = sprintf('set%s', $view->getEntityClassName());

        $viewEntity->$viewSetEntityMethod($entity);
        $viewEntity->setUser($view->getUser());
        $viewEntity->setDateTime($view->getDateTime());

        $entity->incViews();

        $this->registry->getManager()->persist($viewEntity);
    }

    protected function getView(string $className): ViewInterface
    {
        $viewClassName = sprintf('App\Entity\\%sView', $className);

        return new $viewClassName;
    }

    protected function getUser(int $userId): User
    {
        return $this->registry->getManager()->getRepository(User::class)->find($userId);
    }

    protected function getEntity(string $className, int $entityId): ViewableInterface
    {
        return $this->registry->getManager()->getRepository(sprintf('App:%s', $className))->find($entityId);
    }
}
