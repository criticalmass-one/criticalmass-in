<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Traits\RepositoryTrait;
use App\Traits\UtilTrait;
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
     *  description="Does bullshit"
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
