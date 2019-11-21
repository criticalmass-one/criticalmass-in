<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Photo;
use App\Entity\Ride;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Response;

class PhotoController extends BaseController
{
    /**
     * Get a list of photos which were uploaded to a specified ride.
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Retrieve a list of photos of a ride",
     *  section="Photo",
     *  requirements={
     *    {"name"="citySlug", "dataType"="string", "required"=true, "description"="Provide the slug of a city."},
     *    {"name"="rideIdentifier", "dataType"="string", "required"=true, "description"="Provide the ride identifier of a ride."},
     *  }
     * )
     * @ParamConverter("ride", class="App:Ride")
     */
    public function listPhotosAction(RegistryInterface $registry, Ride $ride): Response
    {
        $photoList = $registry->getRepository(Photo::class)->findPhotosByRide($ride);

        $view = View::create();
        $view
            ->setData($photoList)
            ->setFormat('json')
            ->setStatusCode(200);

        return $this->handleView($view);
    }
}
