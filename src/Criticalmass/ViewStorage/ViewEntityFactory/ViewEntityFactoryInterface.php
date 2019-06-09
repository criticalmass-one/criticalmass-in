<?php declare(strict_types=1);

namespace App\Criticalmass\ViewStorage\ViewEntityFactory;

use App\Criticalmass\ViewStorage\ViewInterface\ViewableEntity;
use App\Criticalmass\ViewStorage\ViewInterface\ViewEntity;
use App\Criticalmass\ViewStorage\ViewModel\View;
use FOS\UserBundle\Model\UserInterface;

interface ViewEntityFactoryInterface
{
    public function createViewEntity(View $view, ViewableEntity $viewableEntity, UserInterface $user = null, string $namespace = 'App\\Entity\\'): ViewEntity;
}