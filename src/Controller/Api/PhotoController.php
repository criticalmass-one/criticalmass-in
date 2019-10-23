<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Traits\RepositoryTrait;
use App\Traits\UtilTrait;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Response;

class PhotoController extends BaseController
{
    use RepositoryTrait;
    use UtilTrait;

    /**
     * This is a pretty useless endpoint which is not ready for usage now.
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Does bullshit",
     *  section="Photo"
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
