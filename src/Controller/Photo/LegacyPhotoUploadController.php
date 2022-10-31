<?php declare(strict_types=1);

namespace App\Controller\Photo;

use App\Controller\AbstractController;
use App\Criticalmass\Image\PhotoUploader\PhotoUploaderInterface;
use App\Entity\Photo;
use App\Entity\Ride;
use App\Form\Type\LegacyPhotoUploadType;
use Flagception\Bundle\FlagceptionBundle\Annotations\Feature;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Feature("photos")
 */
class LegacyPhotoUploadController extends AbstractController
{
    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("ride", class="App:Ride")
     */
    public function uploadAction(Request $request, Ride $ride, PhotoUploaderInterface $photoUploader, UserInterface $user = null): Response
    {
        if (Request::METHOD_POST === $request->getMethod()) {
            return $this->uploadPostAction($request, $ride, $photoUploader, $user);
        } else {
            return $this->uploadGetAction($request, $ride, $photoUploader, $user);
        }
    }

    protected function uploadGetAction(Request $request, Ride $ride, PhotoUploaderInterface $photoUploader, UserInterface $user = null): Response
    {
        $form = $this->createForm(LegacyPhotoUploadType::class, new Photo());

        return $this->render('PhotoUpload/legacy.html.twig', [
            'ride' => $ride,
            'form' => $form->createView(),
        ]);
    }

    protected function uploadPostAction(Request $request, Ride $ride, PhotoUploaderInterface $photoUploader, UserInterface $user = null): Response
    {
        $uploadedFile = null;
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

        return $this->uploadGetAction($request, $ride, $photoUploader, $user);
    }
}
