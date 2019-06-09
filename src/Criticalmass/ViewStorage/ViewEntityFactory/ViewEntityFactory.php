<?php declare(strict_types=1);

namespace App\Criticalmass\ViewStorage\ViewEntityFactory;

use App\Criticalmass\ViewStorage\ViewInterface\ViewableEntity;
use App\Criticalmass\ViewStorage\ViewInterface\ViewEntity;
use App\Criticalmass\ViewStorage\ViewModel\View;
use App\Entity\User;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ViewEntityFactory implements ViewEntityFactoryInterface
{
    /** @var RegistryInterface $registry */
    protected $registry;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function createViewEntity(View $view, ViewableEntity $viewableEntity, UserInterface $user = null, string $namespace = 'App\\Entity\\'): ViewEntity
    {
        $viewEntity = $this->getViewEntity($view->getEntityClassName(), $namespace);

        $viewSetEntityMethod = sprintf('set%s', $view->getEntityClassName());

        $viewEntity
            ->setId($viewableEntity->getId())
            ->$viewSetEntityMethod($viewableEntity)
            ->setUser($user)
            ->setDateTime($view->getDateTime());

        return $viewEntity;
    }

    protected function getViewEntity(string $className, string $namespace = 'App\\Entity\\'): ViewEntity
    {
        $viewClassName = sprintf('%s%sView', $namespace, $className);

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
