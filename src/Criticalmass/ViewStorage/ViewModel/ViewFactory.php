<?php declare(strict_types=1);

namespace App\Criticalmass\ViewStorage\ViewModel;

use App\Criticalmass\Util\ClassUtil;
use App\Criticalmass\ViewStorage\ViewInterface\ViewableEntity;
use Carbon\Carbon;
use Symfony\Component\Security\Core\User\UserInterface;

class ViewFactory
{
    private function __construct()
    {
    }

    public static function createView(ViewableEntity $viewable, $user = null, ?Carbon $dateTime = null): View
    {
        $viewDateTime = $dateTime ?? Carbon::now('UTC');

        $view = new View();
        $view
            ->setEntityClassName(ClassUtil::getShortname($viewable))
            ->setEntityId($viewable->getId())
            ->setUserId($user instanceof UserInterface ? $user->getId() : null)
            ->setDateTime($viewDateTime);

        return $view;
    }
}