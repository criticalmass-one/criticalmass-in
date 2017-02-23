<?php

namespace Caldera\Bundle\CalderaBundle\Controller\Api;

use Caldera\Bundle\CalderaBundle\Traits\RepositoryTrait;
use Caldera\Bundle\CalderaBundle\Traits\UtilTrait;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class PhotoController extends BaseController
{
    use RepositoryTrait;
    use UtilTrait;

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="This is a description of your API method"
     * )
     */
    public function galleryAction(): Response
    {
        $photoRides = $this->getPhotoRepository()->findRidesForGallery();

        $view = View::create();
        $view
            ->setData($photoRides)
            ->setFormat('json')
            ->setStatusCode(200);

        return $this->handleView($view);
    }
}
