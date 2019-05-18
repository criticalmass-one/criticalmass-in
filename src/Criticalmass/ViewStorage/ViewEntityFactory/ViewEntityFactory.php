<?php declare(strict_types=1);

namespace App\Criticalmass\ViewStorage\ViewEntityFactory;

use App\Criticalmass\ViewStorage\ViewInterface\ViewableEntity;
use App\Criticalmass\ViewStorage\ViewInterface\ViewEntity;
use App\Criticalmass\ViewStorage\ViewModel\View;
use FOS\UserBundle\Model\UserInterface;

class ViewEntityFactory
{
    private function __construct()
    {
    }

    public static function createViewEntity(View $view, ViewableEntity $viewableEntity, UserInterface $user): ViewEntity
    {
        $viewEntity = self::getViewEntity($view->getEntityClassName());

        $viewSetEntityMethod = sprintf('set%s', $view->getEntityClassName());

        $viewEntity
            ->$viewSetEntityMethod($viewableEntity)
            ->setUser($user)
            ->setDateTime($view->getDateTime());

        return $viewEntity;
    }

    protected static function getViewEntity(string $className): ViewEntity
    {
        $viewClassName = sprintf('App\Entity\\%sView', $className);

        return new $viewClassName;
    }
}