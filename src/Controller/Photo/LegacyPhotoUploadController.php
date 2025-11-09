<?php declare(strict_types=1);

namespace App\Controller\Photo;

use App\Controller\AbstractController;
use App\Criticalmass\Image\PhotoUploader\PhotoUploaderInterface;
use App\Entity\Photo;
use App\Entity\Ride;
use App\Form\Type\LegacyPhotoUploadType;
use Flagception\Bundle\FlagceptionBundle\Attribute\Feature;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Feature('photos')]
class LegacyPhotoUploadController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route(
        '/{citySlug}/{rideIdentifier}/addphoto-legacy',
        name: 'caldera_criticalmass_gallery_legacy_photos_upload_ride',
        priority: 170
    )]
    public function uploadAction(Request $request, UserInterface $user = null, Ride $ride, PhotoUploaderInterface $photoUploader): Response
    {
        if (Request::METHOD_POST === $request->getMethod()) {
            return $this->uploadPostAction($request, $user, $ride, $photoUploader);
        } else {
            return $this->uploadGetAction($request, $user, $ride, $photoUploader);
        }
    }

    protected function uploadGetAction(Request $request, UserInterface $user = null, Ride $ride, PhotoUploaderInterface $photoUploader): Response
    {
        $form = $this->createForm(LegacyPhotoUploadType::class, new Photo());

        return $this->render('PhotoUpload/legacy.html.twig', [
            'ride' => $ride,
            'form' => $form->createView(),
        ]);
    }

    protected function uploadPostAction(Request $request, UserInterface $user = null, Ride $ride, PhotoUploaderInterface $photoUploader): Response
    {
        /** @var UploadedFile $uploadedFile */
        $fileArray = $request->files->get('legacy_photo_upload');

        if (!is_array($fileArray)) {

        } else {
            $uploadedFile = $fileArray['imageFile']['file'];
        }

        if ($uploadedFile && $uploadedFile instanceof UploadedFile) {
            $photoUploader
                ->setRide($ride)
                ->setUser($user)
                ->addUploadedFile($uploadedFile);
        }

        return $this->uploadGetAction($request, $user, $ride, $photoUploader);
    }
}
