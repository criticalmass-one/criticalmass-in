<?php

namespace Caldera\CriticalmassGalleryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EditphotoController extends Controller
{
    public function relocateAction(Request $request, $photoId, $latitude, $longitude)
    {
        if ($photoId > 0)
        {
            $em = $this->getDoctrine()->getManager();
            $photo = $em->find('CalderaCriticalmassGalleryBundle:Photo', $photoId);

            $photo->setLatitude($latitude);
            $photo->setLongitude($longitude);

            $em->merge($photo);
            $em->flush();
        }

        return new RedirectResponse($this->container->get('request')->headers->get('referer'));
    }
}
