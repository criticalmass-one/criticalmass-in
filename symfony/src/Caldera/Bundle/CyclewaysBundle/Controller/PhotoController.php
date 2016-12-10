<?php

namespace Caldera\Bundle\CyclewaysBundle\Controller;

use Caldera\Bundle\CalderaBundle\Entity\Incident;
use Caldera\Bundle\CalderaBundle\Entity\Photo;
use Caldera\Bundle\CriticalmassCoreBundle\BaseTrait\ViewStorageTrait;
use Caldera\Bundle\CriticalmassSiteBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PhotoController extends AbstractController
{
    use ViewStorageTrait;

    public function uploadAction(Request $request, string $slug): Response
    {
        $incident = $this->getIncidentRepository()->findOneBySlug($slug);

        if (!$incident) {
            throw $this->createNotFoundException();
        }

        if ($request->getMethod() == 'POST') {
            return $this->uploadPostAction($request, $incident);
        } else {
            return $this->uploadGetAction($request, $incident);
        }
    }

    protected function uploadGetAction(Request $request, Incident $incident): Response
    {
        return $this->render(
            'CalderaCyclewaysBundle:Photo:upload.html.twig',
            [
                'incident' => $incident
            ]
        );
    }

    protected function uploadPostAction(Request $request, Incident $incident): Response
    {
        $em = $this->getDoctrine()->getManager();

        $photo = new Photo();

        $photo->setImageFile($request->files->get('file'));
        $photo->setUser($this->getUser());

        $photo->setIncident($incident);
        $photo->setCity($incident->getCity());

        $em->persist($photo);
        $em->flush();

        return new Response('foo');
    }

    public function showAction(Request $request, string $slug, int $photoId): Response
    {
        $incident = $this->getIncidentRepository()->findOneBySlug($slug);

        if (!$incident) {
            throw $this->createNotFoundException();
        }

        /** @var Photo $photo */
        $photo = $this->getPhotoRepository()->find($photoId);

        $previousPhoto = $this->getPhotoRepository()->getPreviousPhoto($photo);
        $nextPhoto = $this->getPhotoRepository()->getNextPhoto($photo);

        $this->countPhotoView($photo);

        return $this->render('CalderaCyclewaysBundle:Photo:show.html.twig',
            [
                'photo' => $photo,
                'nextPhoto' => $nextPhoto,
                'previousPhoto' => $previousPhoto,
                'incident' => $incident
            ]
        );
    }
}
