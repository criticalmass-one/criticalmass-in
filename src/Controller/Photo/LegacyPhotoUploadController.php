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
    public function uploadAction(Request $request, Ride $ride, PhotoUploaderInterface $photoUploader, ?UserInterface $user = null): Response
    {
        if (Request::METHOD_POST === $request->getMethod()) {
            return $this->uploadPostAction($request, $ride, $photoUploader, $user);
        } else {
            return $this->uploadGetAction($request, $ride, $photoUploader, $user);
        }
    }

    protected function uploadGetAction(Request $request, Ride $ride, PhotoUploaderInterface $photoUploader, ?UserInterface $user = null): Response
    {
        $form = $this->createForm(LegacyPhotoUploadType::class, new Photo());

        return $this->render('PhotoUpload/legacy.html.twig', [
            'ride' => $ride,
            'form' => $form->createView(),
        ]);
    }

    protected function uploadPostAction(Request $request, Ride $ride, PhotoUploaderInterface $photoUploader, ?UserInterface $user = null): Response
    {
        $form = $this->createForm(LegacyPhotoUploadType::class, new Photo());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Photo $photo */
            $photo = $form->getData();
            $uploadedFile = $photo->getImageFile();

            if ($uploadedFile instanceof UploadedFile) {
                $photoUploader
                    ->setRide($ride)
                    ->setUser($user)
                    ->addUploadedFile($uploadedFile);
            }
        }

        return $this->uploadGetAction($request, $ride, $photoUploader, $user);
    }
}
