<?php declare(strict_types=1);

namespace Criticalmass\Bundle\AppBundle\Controller\Statistic;

use Criticalmass\Bundle\AppBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;

class TopStatsController extends AbstractController
{
    public function topStatsAction(): Response
    {
        $rideList = $this->getRideRepository()->findMostPopularRides();

        return $this->render('AppBundle:Statistic:top.html.twig', [
            'rideList' => $rideList,
        ]);
    }
}
