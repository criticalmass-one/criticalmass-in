<?php declare(strict_types=1);

namespace App\Criticalmass\Router\DelegatedRouter;

use App\Entity\Thread;
use App\EntityInterface\RouteableInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ThreadRouter extends AbstractDelegatedRouter
{
    /** @param Thread $thread */
    public function generate(RouteableInterface $thread, string $routeName = null, array $parameters = [], int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string
    {
        /* Let’s see if this is a city thread */
        if ($thread->getCity()) {
            $routeName = 'caldera_criticalmass_board_viewcitythread';
        } else {
            $routeName = 'caldera_criticalmass_board_viewthread';
        }

        $parameterList = array_merge($this->generateParameterList($thread, $routeName), $parameters);

        return $this->router->generate($routeName, $parameterList, $referenceType);
    }
}
