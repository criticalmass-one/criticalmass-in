<?php declare(strict_types=1);

namespace App\Controller\Photo;

use App\Controller\AbstractController;
use App\Entity\City;
use App\Entity\Photo;
use App\Entity\Ride;
use App\Repository\PhotoRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Flagception\Bundle\FlagceptionBundle\Annotations\Feature;

/**
 * @Feature("photos")
 */
class PhotoGalleryController extends AbstractController
{
    /**
     * @ParamConverter("ride", class="App:Ride")
     */
    public function galleryAction(
        Request $request,
        PaginatorInterface $paginator,
        PhotoRepository $photoRepository,
        Ride $ride
    ): Response {
        if ($ride && $ride->getRestrictedPhotoAccess() && !$this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $query = $photoRepository->buildQueryPhotosByRide($ride);

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            32
        );

        return $this->render('PhotoGallery/gallery_list.html.twig', [
            'ride' => $ride,
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Security("is_granted('ROLE_USER')")
     * @Feature("photos")
     */
    public function userlistAction(
        PhotoRepository $photoRepository,
        UserInterface $user = null
    ): Response {
        $result = $photoRepository->findRidesWithPhotoCounterByUser($user);

        return $this->render('PhotoGallery/user_list.html.twig', [
            'result' => $result,
        ]);
    }

    /**
     * @Feature("photos")
     */
    public function examplegalleryAction(
        PhotoRepository $photoRepository
    ): Response {
        $photos = $photoRepository->findSomePhotos(32);

        $cityList = [];

        /** @var Photo $photo */
        foreach ($photos as $photo) {
            /** @var City $city */
            $city = $photo->getRide()->getCity();
            $citySlug = $city->getSlug();

            $cityList[$citySlug] = $city;
        }

        shuffle($cityList);

        return $this->render('PhotoGallery/example_gallery.html.twig', [
            'photos' => $photos,
            'cities' => $cityList,
        ]);
    }
}
