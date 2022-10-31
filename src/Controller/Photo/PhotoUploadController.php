<?php declare(strict_types=1);

namespace App\Controller\Photo;

use App\Criticalmass\Image\PhotoUploader\PhotoUploaderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Controller\AbstractController;
use App\Entity\Ride;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Flagception\Bundle\FlagceptionBundle\Annotations\Feature;

/**
 * @Feature("photos")
 */
class PhotoUploadController extends AbstractController
{
    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("ride", class="App:Ride")
     */
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
        return $this->render('PhotoUpload/upload.html.twig', [
            'ride' => $ride,
        ]);
    }

    protected function uploadPostAction(Request $request, UserInterface $user = null, Ride $ride, PhotoUploaderInterface $photoUploader): Response
    {
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $request->files->get('file');

        if ($uploadedFile instanceof UploadedFile) {
            $photoUploader
                ->setRide($ride)
                ->setUser($user)
                ->addUploadedFile($uploadedFile);

            return new Response('Success', 200);
        }

        return new Response('', 403);
    }
}
