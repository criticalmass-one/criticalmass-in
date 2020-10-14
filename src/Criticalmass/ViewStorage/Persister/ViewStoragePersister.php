<?php declare(strict_types=1);

namespace App\Criticalmass\ViewStorage\Persister;

use App\Criticalmass\ViewStorage\ViewInterface\ViewableEntity;
use App\Criticalmass\ViewStorage\ViewInterface\ViewEntity;
use App\Criticalmass\ViewStorage\ViewModel\View;

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

    public function storeView(View $view, bool $flush = false): ViewStoragePersisterInterface
    {
        $entity = $this->getEntity($view->getEntityClassName(), $view->getEntityId());

        $viewEntity = $this->viewEntityFactory->createViewEntity($view, $entity);

        $entity->incViews();

        $this->registry->getManager()->persist($viewEntity);

        if ($flush) {
            $this->registry->getManager()->flush();
        }

        return $this;
    }

    protected function getView(string $className): ViewEntity
    {
        $viewClassName = sprintf('%s%sView', $this->entityNamespace, $className);

        return new $viewClassName;
    }

    protected function getEntity(string $className, int $entityId): ViewableEntity
    {
        return $this->registry->getRepository(sprintf('App:%s', $className))->find($entityId);
    }
}
