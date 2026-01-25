<?php declare(strict_types=1);

namespace App\Criticalmass\Router\DelegatedRouter;

use App\Entity\Board;
use App\EntityInterface\BoardInterface;
use App\EntityInterface\RouteableInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class BoardRouter extends AbstractDelegatedRouter
{
    /** @param BoardInterface $board */
    public function generate(
        RouteableInterface $board,
        ?string $routeName = null,
        array $parameters = [],
        int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH
    ): string {
        if ($board instanceof Board && !$routeName) {
            $routeName = 'caldera_criticalmass_board_listthreads';
        } elseif (!$routeName) {
            $routeName = 'caldera_criticalmass_board_listcitythreads';
        }

        $parameterList = array_merge($this->generateParameterList($board, $routeName), $parameters);

        return $this->router->generate($routeName, $parameterList, $referenceType);
    }
}
