<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class StatisticController extends AbstractController
{
    public function citystatisticAction(Request $request, $citySlug)
    {
        $city = $this->getCheckedCity($citySlug);

        $rides = $this->getRideRepository()->findRidesForCity($city);

        return $this->render(
            'CalderaCriticalmassSiteBundle:Statistic:citystatistic.html.twig',
            [
                'city' => $city,
                'rides' => $rides
            ]
        );
    }
}
