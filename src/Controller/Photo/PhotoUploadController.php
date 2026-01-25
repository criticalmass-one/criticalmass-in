<?php declare(strict_types=1);

namespace App\Controller\Photo;

use App\Criticalmass\Image\PhotoUploader\PhotoUploaderInterface;
use App\Controller\AbstractController;
use App\Entity\Ride;
use Flagception\Bundle\FlagceptionBundle\Attribute\Feature;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Feature('photos')]
class PhotoUploadController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/{citySlug}/{rideIdentifier}/addphoto', name: 'caldera_criticalmass_gallery_photos_upload_ride', priority: 170)]
    public function uploadAction(
        Request $request,
        Ride $ride,
        PhotoUploaderInterface $photoUploader,
        ?UserInterface $user = null
    ): Response {
        if (Request::METHOD_POST === $request->getMethod()) {
            return $this->uploadPostAction($request, $ride, $photoUploader, $user);
        }

        return $this->uploadGetAction($request, $ride, $photoUploader, $user);
    }

    protected function uploadGetAction(
        Request $request,
        Ride $ride,
        PhotoUploaderInterface $photoUploader,
        ?UserInterface $user = null
    ): Response {
        return $this->render('PhotoUpload/upload.html.twig', [
            'ride' => $ride,
        ]);
    }

    protected function uploadPostAction(
        Request $request,
        Ride $ride,
        PhotoUploaderInterface $photoUploader,
        ?UserInterface $user = null
    ): Response {
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $request->files->get('file');

        if ($uploadedFile instanceof UploadedFile) {
            $photoUploader
                ->setRide($ride)
                ->setUser($user)
                ->addUploadedFile($uploadedFile);

            return new Response('Success', Response::HTTP_OK);
        }

        return new Response('', Response::HTTP_FORBIDDEN);
    }
}
