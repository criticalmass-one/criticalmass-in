<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Caldera\Bundle\CriticalmassCoreBundle\Board\Builder\BoardBuilder;
use Symfony\Component\HttpFoundation\Request;

class BoardController extends AbstractController
{
    public function overviewAction(Request $request)
    {
        /**
         * @var BoardBuilder $boardBuilder
         */
        $boardBuilder = $this->get('caldera.criticalmass.board.builder.boardbuilder');

        $boardBuilder->buildOverview();

        $tree = $boardBuilder->getList();

        return $this->render(
            'CalderaCriticalmassSiteBundle:Board:overview.html.twig',
            [
                'boardTree' => $tree
            ]
        );
    }
}
