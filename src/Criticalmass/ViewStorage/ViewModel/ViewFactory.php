<?php declare(strict_types=1);

namespace App\Criticalmass\ViewStorage\ViewModel;

use App\Criticalmass\Util\ClassUtil;
use App\Criticalmass\ViewStorage\ViewInterface\ViewableEntity;
use Symfony\Component\Security\Core\User\UserInterface;

class ViewFactory
{
    private function __construct()
    {
    }

    public static function createView(ViewableEntity $viewable, $user = null, \DateTime $dateTime = null): View
    {
        $viewDateTime = $dateTime ?? new \DateTime('now', new \DateTimeZone('UTC'));

        $view = new View();
        $view
            ->setEntityClassName(ClassUtil::getShortname($viewable))
            ->setEntityId($viewable->getId())
            ->setUserId($user instanceof UserInterface ? $user->getId() : null)
            ->setDateTime($viewDateTime);

        return $view;
    }
}