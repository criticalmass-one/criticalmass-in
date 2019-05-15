<?php declare(strict_types=1);

namespace App\Criticalmass\ViewStorage\View;

use App\Criticalmass\Util\ClassUtil;
use App\EntityInterface\ViewableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ViewFactory
{
    private function __construct()
    {
    }

    public static function createView(ViewableInterface $viewable, $user): View
    {
        $viewDateTime = new \DateTime('now', new \DateTimeZone('UTC'));

        $view = new View();
        $view
            ->setEntityClassName(ClassUtil::getShortname($viewable))
            ->setEntityId($viewable->getId())
            ->setUserId($user instanceof UserInterface ? $user->getId() : null)
            ->setDateTime($viewDateTime);

        return $view;
    }
}