<?php declare(strict_types=1);

namespace App\Criticalmass\ViewStorage\Persister;

use App\Criticalmass\ViewStorage\View\View;
use App\Entity\User;
use App\EntityInterface\ViewableInterface;
use App\EntityInterface\ViewInterface;

class ViewStoragePersister extends AbstractViewStoragePersister
{
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

    public function storeView(View $view): ViewStoragePersisterInterface
    {
        $viewEntity = $this->getView($view->getEntityClassName());
        $entity = $this->getEntity($view->getEntityClassName(), $view->getEntityId());
        $viewSetEntityMethod = sprintf('set%s', $view->getEntityClassName());

        $viewEntity->$viewSetEntityMethod($entity);
        $viewEntity->setUser($this->getUser($view->getUserId()));
        $viewEntity->setDateTime($view->getDateTime());

        $entity->incViews();

        $this->registry->getManager()->persist($viewEntity);

        return $this;
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
