<?php declare(strict_types=1);

namespace App\Controller\Heatmap;

use App\Controller\AbstractController;
use App\Entity\City;
use App\Entity\Ride;
use App\Entity\User;
use App\Factory\Heatmap\HeatmapListFactoryInterface;
use App\Factory\Heatmap\UserHeatmapTrackFactoryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class HeatmapController extends AbstractController
{
    /**
     * @ParamConverter("city", class="App:City")
     */
    public function cityAction(City $city, UserHeatmapTrackFactoryInterface $userHeatmapTrackFactory): Response
    {
        if (!$city->getHeatmap()) {
            throw new NotFoundHttpException(sprintf('No heatmap defined for City #%d', $city->getId()));
        }

        return $this->render('Heatmap/city.html.twig', [
            'city' => $city,
            'userHeatmapTrackList' => $userHeatmapTrackFactory->generateList($city->getHeatmap()),
        ]);
    }

    public function listAction(HeatmapListFactoryInterface $heatmapListFactory): Response
    {
        return $this->render('Heatmap/list.html.twig', [
            'heatmapList' => $heatmapListFactory->build(),
        ]);
    }

    /**
     * @ParamConverter("ride", class="App:Ride")
     */
    public function rideAction(Ride $ride): Response
    {
        if (!$ride->getHeatmap()) {
            throw new NotFoundHttpException(sprintf('No heatmap defined for Ride #%d', $ride->getId()));
        }

        return $this->render('Heatmap/ride.html.twig', [
            'ride' => $ride,
        ]);
    }

    /**
     * @ParamConverter("user", class="App:User")
     */
    public function userAction(User $user): Response
    {
        return $this->render('Heatmap/user.html.twig');
    }
}
