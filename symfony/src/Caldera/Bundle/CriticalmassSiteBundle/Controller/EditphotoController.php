<?php

namespace Caldera\CriticalmassGalleryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EditphotoController extends Controller
{
    public function relocateAction(Request $request, $photoId)
    {
        if ($photoId > 0)
        {
            $em = $this->getDoctrine()->getManager();
            $photo = $em->find('CalderaCriticalmassGalleryBundle:Photo', $photoId);

            $photo->setLatitude($request->get('latitude'));
            $photo->setLongitude($request->get('longitude'));

            $em->merge($photo);
            $em->flush();
        }

        return new RedirectResponse($this->container->get('request')->headers->get('referer'));
    }
}
