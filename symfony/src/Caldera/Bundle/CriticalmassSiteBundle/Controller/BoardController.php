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

    public function viewtalkboardAction(Request $request, $citySlug)
    {
        $city = $this->getCheckedCity($citySlug);

        /**
         * @var BoardBuilder $boardBuilder
         */
        $boardBuilder = $this->get('caldera.criticalmass.board.builder.boardbuilder');

        $boardBuilder->buildTalkBoard($city);

        $tree = $boardBuilder->getList();

        return $this->render(
            'CalderaCriticalmassSiteBundle:Board:overview.html.twig',
            [
                'boardTree' => $tree
            ]
        );
    }

    public function viewrideboardAction(Request $request, $citySlug)
    {
        $city = $this->getCheckedCity($citySlug);

        /**
         * @var BoardBuilder $boardBuilder
         */
        $boardBuilder = $this->get('caldera.criticalmass.board.builder.boardbuilder');

        $boardBuilder->buildRideBoard($city);

        return $this->render(
            'CalderaCriticalmassSiteBundle:Board:viewCityRideBoard.html.twig',
            [
                'threads' => $boardBuilder->getList()
            ]
        );
    }
}
