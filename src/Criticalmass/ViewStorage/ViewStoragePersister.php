<?php declare(strict_types=1);

namespace App\Criticalmass\ViewStorage;

use App\Entity\User;
use App\EntityInterface\ViewableInterface;
use App\EntityInterface\ViewInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ViewStoragePersister implements ViewStoragePersisterInterface
{
    /** @var RegistryInterface $registry */
    protected $registry;

    /** @var SerializerInterface $serializer */
    protected $serializer;

    public function __construct(RegistryInterface $registry, SerializerInterface $serializer)
    {
        $this->registry = $registry;
        $this->serializer = $serializer;
    }

    public function persistViews(array $viewList): ViewStoragePersisterInterface
    {
        foreach ($viewList as $unserializedView) {
            /** @var View $view */
            $view = $this->serializer->deserialize($unserializedView, View::class, 'json');

            $this->storeView($view);
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
        $viewEntity->setUser($this->getUser($view->getUserId()));
        $viewEntity->setDateTime($view->getDateTime());

        $entity->incViews();

        $this->registry->getManager()->persist($viewEntity);
    }

    protected function getView(string $className): ViewInterface
    {
        $viewClassName = sprintf('App\Entity\\%sView', $className);

        return new $viewClassName;
    }

    protected function getUser(int $userId = null): ?User
    {
        if (!$userId) {
            return null;
        }
        
        return $this->registry->getManager()->getRepository(User::class)->find($userId);
    }

    protected function getEntity(string $className, int $entityId): ViewableInterface
    {
        return $this->registry->getManager()->getRepository(sprintf('App:%s', $className))->find($entityId);
    }
}
