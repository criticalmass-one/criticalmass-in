<?php declare(strict_types=1);

namespace App\Controller\Heatmap;

use App\Controller\AbstractController;
use App\Criticalmass\Heatmap\Generator\HeatmapGenerator;
use App\Entity\City;
use App\Entity\Heatmap;
use App\Entity\Ride;
use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class HeatmapController extends AbstractController
{
    /**
     * @ParamConverter("city", class="App:City")
     */
    public function cityAction(City $city): Response
    {
        if (!$city->getHeatmap()) {
            throw new NotFoundHttpException(sprintf('No heatmap defined for City #%d', $city->getId()));
        }

        return $this->render('Heatmap/city.html.twig', [
            'city' => $city,
        ]);
    }

    public function testAction(HeatmapGenerator $heatmapGenerator, RegistryInterface $registry): Response
    {
        $heatmap = $registry->getRepository(Heatmap::class)->find(1);

        $heatmapGenerator->setHeatmap($heatmap)->setZoomLevels([12])->generate();

        return new Response('');
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
