<?php declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as AbstractFrameworkController;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

abstract class AbstractController extends AbstractFrameworkController
{
    public function __construct(private readonly AuthorizationCheckerInterface $authorizationChecker)
    {
    }

    protected function isLoggedIn(): bool
    {
        return $this->authorizationChecker
            ->isGranted('IS_AUTHENTICATED_FULLY');
    }
}
