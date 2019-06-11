<?php declare(strict_types=1);

namespace App\Criticalmass\ViewStorage\ViewEntityFactory;

use App\Criticalmass\ViewStorage\ViewInterface\ViewableEntity;
use App\Criticalmass\ViewStorage\ViewInterface\ViewEntity;
use App\Criticalmass\ViewStorage\ViewModel\View;
use App\Entity\User;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ViewEntityFactory implements ViewEntityFactoryInterface
{
    /** @var RegistryInterface $registry */
    protected $registry;

    /** @var string $entityNamespace */
    protected $entityNamespace = 'App\\Entity\\';

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function createViewEntity(View $view, ViewableEntity $viewableEntity): ViewEntity
    {
        $viewEntity = $this->getViewEntity($view->getEntityClassName());

        $viewSetEntityMethod = sprintf('set%s', $view->getEntityClassName());

        $viewEntity
            ->setId($viewableEntity->getId())
            ->$viewSetEntityMethod($viewableEntity)
            ->setUser($this->getUser($view->getUserId()))
            ->setDateTime($view->getDateTime());

        return $viewEntity;
    }

    public function setEntityNamespace(string $entityNamespace): ViewEntityFactory
    {
        $this->entityNamespace = $entityNamespace;

        return $this;
    }

    protected function getViewEntity(string $className): ViewEntity
    {
        $viewClassName = sprintf('%s%sView', $this->entityNamespace, $className);

        return new $viewClassName;
    }

    protected function getUser(int $userId = null): ?User
    {
        if (!$userId) {
            return null;
        }

        return $this->registry->getRepository(User::class)->find($userId);
    }
}
