<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Ride;
use App\Entity\Track;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class TrackController extends BaseController
{
    /**
     * Get a list of tracks which were uploaded to a specified ride.
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Retrieve a list of tracks of a ride",
     *  section="Track",
     *  requirements={
     *    {"name"="citySlug", "dataType"="string", "required"=true, "description"="Provide the slug of a city."},
     *    {"name"="rideIdentifier", "dataType"="string", "required"=true, "description"="Provide the ride identifier of a ride."},
     *  }
     * )
     * @ParamConverter("ride", class="App:Ride")
     */
    public function listRideTrackAction(RegistryInterface $registry, Ride $ride): Response
    {
        $photoList = $registry->getRepository(Track::class)->findByRide($ride);

        $view = View::create();
        $view
            ->setData($photoList)
            ->setFormat('json')
            ->setStatusCode(200);

        return $this->handleView($view);
    }

    /**
     * Show details of a specified track.
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Show details of a track",
     *  section="Track",
     *  requirements={
     *    {"name"="trackId", "dataType"="int", "required"=true, "description"="Unique id of the track."}
     *  }
     * )
     * @ParamConverter("track", class="App:Track")
     */
    public function viewAction(Track $track, UserInterface $user = null): Response
    {
        $context = new Context();

        $context->addGroup('api-public');

        if ($user) {
            $context->addGroup('api-private');
        }

        $view = View::create();
        $view
            ->setData($track)
            ->setFormat('json')
            ->setStatusCode(200)
            ->setContext($context);

        return $this->handleView($view);
    }
}
